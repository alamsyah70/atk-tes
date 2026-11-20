<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportDetailPengeluaransFromCsv extends Command
{
    protected $signature = 'atk:import-detail-pengeluarans
        {file=D:\IT\App\Data\detail_pengeluarans.csv}
        {--branch=1}
        {--dry-run}';

    protected $description = 'Import detail pengeluaran CSV dengan proses cepat berbasis cache';

    public function handle()
    {
        $file = $this->argument('file');
        $branchId = (int) $this->option('branch');
        $dryRun = $this->option('dry-run');

        if (!is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $this->info('Membaca file CSV...');

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('File CSV tidak dapat dibuka.');
            return self::FAILURE;
        }

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            $this->error('Header CSV tidak ditemukan.');
            return self::FAILURE;
        }

        $headers = array_map(function ($header) {
            return strtoupper(trim(str_replace("\xEF\xBB\xBF", '', $header)));
        }, $headers);

        $required = [
            'NOMOR_PENG',
            'KODE_BARAN',
            'NAMA_BARAN',
            'QTY',
            'SATUAN',
        ];

        foreach ($required as $column) {
            if (!in_array($column, $headers, true)) {
                fclose($handle);

                $this->error("Kolom {$column} tidak ditemukan.");
                $this->line('Header tersedia: ' . implode(', ', $headers));

                return self::FAILURE;
            }
        }

        $rows = [];

        while (($values = fgetcsv($handle)) !== false) {
            if (count($values) !== count($headers)) {
                continue;
            }

            $rows[] = array_combine($headers, $values);
        }

        fclose($handle);

        $total = count($rows);

        $this->info("Total baris CSV: {$total}");

        if ($total === 0) {
            $this->warn('Tidak ada data untuk diproses.');
            return self::SUCCESS;
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. Ambil struktur tabel sekali saja
            |--------------------------------------------------------------------------
            */

            $columns = array_flip(
                Schema::getColumnListing('detail_pengeluarans')
            );

            $hasBarangId = isset($columns['barang_id']);
            $hasKategori = isset($columns['kategori']);
            $hasSatuan = isset($columns['satuan']);
            $hasLokasi = isset($columns['lokasi']);
            $hasHarga = isset($columns['harga']);
            $hasJumlah = isset($columns['jumlah']);
            $hasCreatedAt = isset($columns['created_at']);
            $hasUpdatedAt = isset($columns['updated_at']);

            /*
            |--------------------------------------------------------------------------
            | 2. Ambil semua nomor pengeluaran dari CSV
            |--------------------------------------------------------------------------
            */

            $nomorList = [];

            foreach ($rows as $row) {
                $nomor = trim($row['NOMOR_PENG'] ?? '');

                if ($nomor !== '') {
                    $nomorList[$nomor] = $nomor;
                }
            }

            $this->info('Memuat data pengeluaran...');

            /*
            |--------------------------------------------------------------------------
            | 3. Cache pengeluaran
            |--------------------------------------------------------------------------
            */

            $pengeluaranMap = DB::table('pengeluarans')
                ->where('branch_id', $branchId)
                ->whereIn('nomor_pengeluaran', array_values($nomorList))
                ->get(['id', 'nomor_pengeluaran'])
                ->keyBy(function ($item) {
                    return trim($item->nomor_pengeluaran);
                });

            $this->info(
                'Pengeluaran ditemukan: ' . $pengeluaranMap->count()
            );

            /*
            |--------------------------------------------------------------------------
            | 4. Cache Master Barang
            |--------------------------------------------------------------------------
            */

            $this->info('Memuat Master Barang...');

            $barangMap = DB::table('barangs')
                ->where('branch_id', $branchId)
                ->get(['id', 'kode_barang', 'kategori', 'satuan', 'lokasi'])
                ->keyBy(function ($item) {
                    return trim($item->kode_barang);
                });

            $this->info(
                'Master Barang ditemukan: ' . $barangMap->count()
            );

            /*
            |--------------------------------------------------------------------------
            | 5. Fingerprint untuk mendeteksi duplikat
            |--------------------------------------------------------------------------
            */

            $fingerprint = function (
                $pengeluaranId,
                $kode,
                $nama,
                $qty,
                $satuan
            ) {
                return hash('sha256', json_encode([
                    (int) $pengeluaranId,
                    trim((string) $kode),
                    trim((string) $nama),
                    number_format((float) $qty, 4, '.', ''),
                    trim((string) $satuan),
                ]));
            };

            /*
            |--------------------------------------------------------------------------
            | 6. Ambil ID pengeluaran yang ditemukan
            |--------------------------------------------------------------------------
            */

            $pengeluaranIds = $pengeluaranMap
                ->pluck('id')
                ->unique()
                ->values()
                ->all();

            $existingCounts = [];

            if (!empty($pengeluaranIds)) {
                $this->info('Memeriksa detail yang sudah ada...');

                DB::table('detail_pengeluarans')
                    ->whereIn('pengeluaran_id', $pengeluaranIds)
                    ->select([
                        'id',
                        'pengeluaran_id',
                        'kode_barang',
                        'nama_barang',
                        'qty',
                        'satuan',
                    ])
                    ->orderBy('id')
                    ->chunkById(1000, function ($details) use (
                        &$existingCounts,
                        $fingerprint
                    ) {
                        foreach ($details as $detail) {
                            $key = $fingerprint(
                                $detail->pengeluaran_id,
                                $detail->kode_barang,
                                $detail->nama_barang,
                                $detail->qty,
                                $detail->satuan
                            );

                            $existingCounts[$key] =
                                ($existingCounts[$key] ?? 0) + 1;
                        }
                    });
            }

            $this->info(
                'Pemeriksaan detail lama selesai: ' .
                count($existingCounts) . ' kelompok data.'
            );

            /*
            |--------------------------------------------------------------------------
            | 7. Proses data CSV
            |--------------------------------------------------------------------------
            */

            $this->newLine();
            $this->info('Memulai proses detail pengeluaran...');

            $success = 0;
            $duplicate = 0;
            $pengeluaranNotFound = 0;
            $barangNotFound = 0;
            $invalid = 0;

            $seenSource = [];
            $insertRows = [];

            foreach ($rows as $index => $row) {

                $nomor = trim($row['NOMOR_PENG'] ?? '');
                $kode = trim($row['KODE_BARAN'] ?? '');
                $nama = trim($row['NAMA_BARAN'] ?? '');
                $satuanCsv = trim($row['SATUAN'] ?? '');

                $qty = is_numeric($row['QTY'] ?? null)
                    ? (float) $row['QTY']
                    : null;

                if (
                    $nomor === '' ||
                    $kode === '' ||
                    $nama === '' ||
                    $qty === null
                ) {
                    $invalid++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Cari pengeluaran dari cache
                |--------------------------------------------------------------------------
                */

                $pengeluaran = $pengeluaranMap->get($nomor);

                if (!$pengeluaran) {
                    $pengeluaranNotFound++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Cari barang dari cache
                |--------------------------------------------------------------------------
                */

                $barang = $barangMap->get($kode);

                $barangId = $barang->id ?? null;

                if (!$barang) {
                    $barangNotFound++;
                }

                /*
                |--------------------------------------------------------------------------
                | Tentukan kategori, satuan, lokasi
                |--------------------------------------------------------------------------
                */

                $kategori = trim((string) ($barang->kategori ?? ''));
                $satuanMaster = trim((string) ($barang->satuan ?? ''));
                $lokasi = trim((string) ($barang->lokasi ?? ''));

                if ($kategori === '') {
                    $kategori = 'Lainnya';
                }

                if ($satuanCsv === '') {
                    $satuanCsv = $satuanMaster ?: 'PCS';
                }

                if ($lokasi === '') {
                    $lokasi = '-';
                }

                /*
                |--------------------------------------------------------------------------
                | Harga dan jumlah dari CSV
                |--------------------------------------------------------------------------
                */

                $harga = is_numeric($row['PRICE'] ?? null)
                    ? (float) $row['PRICE']
                    : 0;

                $jumlah = is_numeric($row['VAL'] ?? null)
                    ? (float) $row['VAL']
                    : ($qty * $harga);

                /*
                |--------------------------------------------------------------------------
                | Cek duplikat menggunakan cache
                |--------------------------------------------------------------------------
                */

                $key = $fingerprint(
                    $pengeluaran->id,
                    $kode,
                    $nama,
                    $qty,
                    $satuanCsv
                );

                $seenSource[$key] = ($seenSource[$key] ?? 0) + 1;

                if (
                    $seenSource[$key] <=
                    ($existingCounts[$key] ?? 0)
                ) {
                    $duplicate++;
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Susun data detail
                |--------------------------------------------------------------------------
                */

                $data = [
                    'pengeluaran_id' => $pengeluaran->id,
                    'kode_barang' => $kode,
                    'nama_barang' => $nama,
                    'qty' => $qty,
                ];

                if ($hasBarangId) {
                    $data['barang_id'] = $barangId;
                }

                if ($hasKategori) {
                    $data['kategori'] = $kategori;
                }

                if ($hasSatuan) {
                    $data['satuan'] = $satuanCsv;
                }

                if ($hasLokasi) {
                    $data['lokasi'] = $lokasi;
                }

                if ($hasHarga) {
                    $data['harga'] = $harga;
                }

                if ($hasJumlah) {
                    $data['jumlah'] = $jumlah;
                }

                if ($hasCreatedAt) {
                    $data['created_at'] = now();
                }

                if ($hasUpdatedAt) {
                    $data['updated_at'] = now();
                }

                $insertRows[] = $data;
                $success++;

                /*
                |--------------------------------------------------------------------------
                | Insert per 500 baris
                |--------------------------------------------------------------------------
                */

                if (count($insertRows) >= 500) {
                    if (!$dryRun) {
                        DB::table('detail_pengeluarans')
                            ->insert($insertRows);
                    }

                    $insertRows = [];
                }

                /*
                |--------------------------------------------------------------------------
                | Tampilkan progres setiap 500 baris
                |--------------------------------------------------------------------------
                */

                $processed = $index + 1;

                if ($processed % 500 === 0 || $processed === $total) {
                    $percent = round(
                        ($processed / $total) * 100,
                        2
                    );

                    $this->line(
                        "Diproses: {$processed}/{$total} ({$percent}%)"
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 8. Insert sisa data
            |--------------------------------------------------------------------------
            */

            if (!empty($insertRows) && !$dryRun) {
                DB::table('detail_pengeluarans')
                    ->insert($insertRows);
            }

            /*
            |--------------------------------------------------------------------------
            | 9. Hasil import
            |--------------------------------------------------------------------------
            */

            $this->newLine();

            $this->info('HASIL IMPORT DETAIL PENGELUARAN');

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Siap diimport / berhasil', $success],
                    ['Duplikat', $duplicate],
                    ['Pengeluaran tidak ditemukan', $pengeluaranNotFound],
                    ['Barang tidak ditemukan (barang_id NULL)', $barangNotFound],
                    ['Data tidak valid', $invalid],
                    ['Total diperiksa', $total],
                ]
            );

            if ($dryRun) {
                $this->warn(
                    'SIMULASI SELESAI. BELUM ADA DATA DISIMPAN.'
                );
            } else {
                $this->info('IMPORT DETAIL PENGELUARAN SELESAI.');
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error('Import gagal: ' . $e->getMessage());
            $this->line($e->getFile() . ':' . $e->getLine());

            return self::FAILURE;
        }
    }
}
