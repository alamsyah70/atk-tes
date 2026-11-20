<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Schema;

class ImportDetailPenerimaansFromCsv extends Command
{
    protected $signature = 'atk:import-detail-penerimaans
        {file=D:\IT\App\Data\detail_penerimaans.csv}
        {--branch= : ID cabang tujuan}
        {--receipts=D:\IT\App\Data\penerimaans.csv : CSV penerimaan lama}
        {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import detail penerimaan DBF dengan pemetaan ID lama ke Laravel';

    public function handle()
    {
        $file = $this->argument('file');
        $receiptFile = $this->option('receipts');
        $branchId = (int) $this->option('branch');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Memulai import detail penerimaan DBF...');
        $this->line("Cabang tujuan: {$branchId}");

        if ($branchId <= 0) {
            $this->error('Parameter --branch wajib diisi.');
            return self::FAILURE;
        }

        if (!is_file($file)) {
            $this->error("File detail tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        if (!is_file($receiptFile)) {
            $this->error("File penerimaan lama tidak ditemukan: {$receiptFile}");
            return self::FAILURE;
        }

        foreach ([
            'branches',
            'barangs',
            'penerimaans',
            'detail_penerimaans',
        ] as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("Tabel {$table} tidak ditemukan.");
                return self::FAILURE;
            }
        }

        foreach ([
            'barangs' => ['branch_id', 'kode_barang'],
            'penerimaans' => ['branch_id', 'nomor_penerimaan'],
            'detail_penerimaans' => [
                'penerimaan_id',
                'barang_id',
                'kode_barang',
                'nama_barang',
                'kategori',
                'satuan',
                'lokasi',
                'qty',
                'harga',
                'jumlah',
                'created_at',
                'updated_at',
            ],
        ] as $table => $columns) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $this->error("Kolom {$table}.{$column} tidak ditemukan.");
                    return self::FAILURE;
                }
            }
        }

        if (!DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        // ==========================================
        // 1. BACA PEMETAAN PENERIMAAN LAMA
        // ==========================================

        $this->info('Membaca pemetaan penerimaan lama...');

        $receiptMap = $this->loadReceiptMap($receiptFile);

        if ($receiptMap === null) {
            return self::FAILURE;
        }

        $this->info('Pemetaan penerimaan lama: ' . count($receiptMap));

        // ==========================================
        // 2. BUKA CSV DETAIL
        // ==========================================

        $csv = $this->openCsv($file);

        if ($csv === null) {
            return self::FAILURE;
        }

        [$handle, $delimiter, $columns] = $csv;

        $this->line('Header detail: ' . implode(' | ', array_keys($columns)));

        // ==========================================
        // 3. VALIDASI HEADER
        // ==========================================

        $required = [
            'penerimaan_lama',
            'kode_barang',
            'nama_barang',
            'qty',
            'harga',
        ];

        $missing = [];

        foreach ($required as $field) {
            if (!isset($columns[$field])) {
                $missing[] = $field;
            }
        }

        if ($missing) {
            fclose($handle);

            $this->error(
                'Kolom CSV tidak ditemukan: ' . implode(', ', $missing)
            );

            return self::FAILURE;
        }

        // ==========================================
        // 4. CACHE DAN COUNTER
        // ==========================================

        $total = 0;
        $ready = 0;
        $duplicates = 0;
        $receiptNotFound = 0;
        $barangNotFound = 0;
        $invalid = 0;
        $inserted = 0;

        $receiptCache = [];
        $barangCache = [];

        $now = now()->format('Y-m-d H:i:s');

        $get = function ($row, $field) use ($columns) {
            if (!isset($columns[$field])) {
                return '';
            }

            return trim((string) ($row[$columns[$field]] ?? ''));
        };

        try {
            // ==========================================
            // 5. PROSES SATU PER SATU
            // ==========================================

            while (($row = fgetcsv(
                $handle,
                0,
                $delimiter,
                '"',
                '\\'
            )) !== false) {

                if (
                    count($row) === 1 &&
                    trim((string) $row[0]) === ''
                ) {
                    continue;
                }

                $total++;

                $oldReceiptId = $get($row, 'penerimaan_lama');
                $kodeBarang = $get($row, 'kode_barang');
                $namaBarang = $get($row, 'nama_barang');

                $qty = $this->parseNumber($get($row, 'qty'));
                $harga = $this->parseNumber($get($row, 'harga'));
                $jumlahRaw = $get($row, 'jumlah');
                $jumlah = $jumlahRaw === ''
                    ? null
                    : $this->parseNumber($jumlahRaw);

                if (
                    $oldReceiptId === '' ||
                    !ctype_digit($oldReceiptId) ||
                    (int) $oldReceiptId <= 0 ||
                    $kodeBarang === '' ||
                    $namaBarang === '' ||
                    $qty === null ||
                    $harga === null ||
                    $qty < 0 ||
                    $harga < 0 ||
                    ($jumlahRaw !== '' && $jumlah === null)
                ) {
                    $invalid++;
                    continue;
                }

                // ==========================================
                // 6. ID PENERIMAAN LAMA -> NOMOR LAMA
                // ==========================================

                if (!isset($receiptMap[$oldReceiptId])) {
                    $receiptNotFound++;
                    continue;
                }

                $nomorPenerimaan = $receiptMap[$oldReceiptId];

                // ==========================================
                // 7. NOMOR LAMA -> ID PENERIMAAN LARAVEL
                // ==========================================

                if (!array_key_exists($nomorPenerimaan, $receiptCache)) {
                    $receiptCache[$nomorPenerimaan] =
                        DB::table('penerimaans')
                            ->where('branch_id', $branchId)
                            ->where('nomor_penerimaan', $nomorPenerimaan)
                            ->value('id');
                }

                $penerimaanId = $receiptCache[$nomorPenerimaan];

                if (!$penerimaanId) {
                    $receiptNotFound++;
                    continue;
                }

                // ==========================================
                // 8. CARI BARANG LARAVEL BERDASARKAN KODE
                // ==========================================

                // BARANG_ID lama sengaja tidak digunakan.
                // Kode barang menjadi kunci pencocokan.

                if (!array_key_exists($kodeBarang, $barangCache)) {
                    $barangCache[$kodeBarang] =
                        DB::table('barangs')
                            ->where('branch_id', $branchId)
                            ->where('kode_barang', $kodeBarang)
                            ->first();
                }

                $barang = $barangCache[$kodeBarang];

                if (!$barang) {
                    $barangNotFound++;
                    continue;
                }

                // ==========================================
                // 9. DATA MASTER BARANG
                // ==========================================

                $kategori = $get($row, 'kategori');
                $satuan = $get($row, 'satuan');
                $lokasi = $get($row, 'lokasi');

                if ($kategori === '') {
                    $kategori = $barang->kategori ?? '';
                }

                if ($satuan === '') {
                    $satuan = $barang->satuan ?? '';
                }

                if ($lokasi === '') {
                    $lokasi = $barang->lokasi ?? '';
                }

                // Nilai 0.00 tetap dipertahankan.
                if ($jumlah === null) {
                    $jumlah = $qty * $harga;
                }

                $createdAt = $this->parseDate(
                    $get($row, 'created_at'),
                    $now
                );

                $updatedAt = $this->parseDate(
                    $get($row, 'updated_at'),
                    $now
                );

                $data = [
                    'penerimaan_id' => $penerimaanId,
                    'barang_id' => $barang->id,
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $namaBarang,
                    'kategori' => $kategori,
                    'satuan' => $satuan,
                    'lokasi' => $lokasi,
                    'qty' => $qty,
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                // ==========================================
                // 10. CEK DUPLIKAT
                // ==========================================

                $exists = DB::table('detail_penerimaans')
                    ->where('penerimaan_id', $penerimaanId)
                    ->where('kode_barang', $kodeBarang)
                    ->where('nama_barang', $namaBarang)
                    ->where('qty', $qty)
                    ->where('harga', $harga)
                    ->where('jumlah', $jumlah)
                    ->where(function ($query) use ($barang) {
                        $query->where('barang_id', $barang->id)
                            ->orWhereNull('barang_id');
                    })
                    ->exists();

                if ($exists) {
                    $duplicates++;
                    continue;
                }

                // ==========================================
                // 11. SIMPAN DATA
                // ==========================================

                $ready++;

                if (!$dryRun) {
                    try {
                        DB::table('detail_penerimaans')->insert($data);
                        $inserted++;
                    } catch (Throwable $e) {
                        $invalid++;

                        if ($invalid <= 10) {
                            $this->warn(
                                "Gagal baris {$total}: {$e->getMessage()}"
                            );
                        }
                    }
                }

                if ($total % 100 === 0) {
                    $this->line("Diperiksa: {$total}");
                }
            }

            // ==========================================
            // 12. HASIL
            // ==========================================

            $this->newLine();
            $this->info('HASIL IMPORT DETAIL PENERIMAAN');

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Siap diimport', $ready],
                    ['Berhasil disimpan', $inserted],
                    ['Duplikat', $duplicates],
                    ['Penerimaan tidak ditemukan', $receiptNotFound],
                    ['Barang tidak ditemukan', $barangNotFound],
                    ['Data tidak valid / gagal simpan', $invalid],
                    ['Total diperiksa', $total],
                ]
            );

            if ($dryRun) {
                $this->warn('Simulasi selesai. Tidak ada data disimpan.');
            } else {
                $this->info("Import selesai. Berhasil: {$inserted}");
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error('Kesalahan: ' . $e->getMessage());
            return self::FAILURE;

        } finally {
            fclose($handle);
        }
    }

    // ==========================================
    // BUKA CSV DAN DETEKSI DELIMITER
    // ==========================================

    private function openCsv(string $path): ?array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            $this->error("Tidak dapat membuka: {$path}");
            return null;
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);
            $this->error("CSV kosong: {$path}");
            return null;
        }

        $delimiters = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];

        $delimiter = array_search(
            max($delimiters),
            $delimiters,
            true
        );

        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter, '"', '\\');

        if (!$header) {
            fclose($handle);
            $this->error('Header tidak terbaca.');
            return null;
        }

        $header[0] = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            (string) $header[0]
        );

        $columns = [];

        foreach ($header as $index => $name) {
            $name = strtolower(trim((string) $name));
            $name = preg_replace('/[^a-z0-9_]/', '', $name);

            $columns[$name] = $index;
        }

        $this->line(
            'Delimiter: ' . ($delimiter === "\t" ? 'TAB' : $delimiter)
        );

        return [$handle, $delimiter, $this->mapHeaders($columns)];
    }

    // ==========================================
    // NORMALISASI ALIAS HEADER
    // ==========================================

    private function mapHeaders(array $header): array
    {
        $aliases = [
            'penerimaan_lama' => [
                'penerimaan_id',
                'penerimaan',
                'id_penerimaan',
            ],
            'kode_barang' => [
                'kode_barang',
                'kode_bara2',
                'kode_baran',
                'kode_bara',
                'kd_barang',
                'kode',
            ],
            'nama_barang' => [
                'nama_barang',
                'nama_baran',
                'nama_bara',
                'nm_barang',
                'nama',
            ],
            'kategori' => ['kategori'],
            'satuan' => ['satuan'],
            'lokasi' => ['lokasi'],
            'qty' => ['qty', 'quantity'],
            'harga' => ['harga', 'price'],
            'jumlah' => ['jumlah', 'total', 'subtotal'],
            'created_at' => ['created_at', 'created'],
            'updated_at' => ['updated_at', 'updated'],
        ];

        $result = [];

        foreach ($aliases as $field => $names) {
            foreach ($names as $name) {
                if (isset($header[$name])) {
                    $result[$field] = $header[$name];
                    break;
                }
            }
        }

        return $result;
    }

    // ==========================================
    // BACA FILE PENERIMAAN LAMA
    // ID LAMA -> NOMOR PENERIMAAN
    // ==========================================

    private function loadReceiptMap(string $path): ?array
    {
        $csv = $this->openCsv($path);

        if (!$csv) {
            return null;
        }

        [$handle, $delimiter, $columns] = $csv;

        $header = [];

        // openCsv sudah memetakan header detail.
        // Untuk file penerimaan, baca ulang header mentah
        // menggunakan posisi kolom yang dibutuhkan.

        $rawHeader = array_flip($columns);

        // mapHeaders tidak memetakan ID lama dan nomor.
        // Baca header file sekali lagi secara langsung.
        rewind($handle);

        $raw = fgetcsv($handle, 0, $delimiter, '"', '\\');

        if (!$raw) {
            fclose($handle);
            return null;
        }

        $raw[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $raw[0]);

        foreach ($raw as $index => $name) {
            $name = strtolower(trim((string) $name));
            $name = preg_replace('/[^a-z0-9_]/', '', $name);
            $header[$name] = $index;
        }

        $idIndex = null;
        $numberIndex = null;

        foreach (['id', 'penerimaan_id', 'penerimaan'] as $name) {
            if (isset($header[$name])) {
                $idIndex = $header[$name];
                break;
            }
        }

        foreach ([
            'nomor_penerimaan',
            'nomor_pene',
            'no_penerimaan',
            'no_terima',
        ] as $name) {
            if (isset($header[$name])) {
                $numberIndex = $header[$name];
                break;
            }
        }

        if ($idIndex === null || $numberIndex === null) {
            fclose($handle);

            $this->error(
                'File penerimaan lama harus memiliki kolom ID dan nomor_pene.'
            );

            return null;
        }

        $map = [];

        while (($row = fgetcsv(
            $handle,
            0,
            $delimiter,
            '"',
            '\\'
        )) !== false) {
            $oldId = trim((string) ($row[$idIndex] ?? ''));
            $number = trim((string) ($row[$numberIndex] ?? ''));

            if (
                $oldId === '' ||
                !ctype_digit($oldId) ||
                $number === ''
            ) {
                continue;
            }

            $oldId = (string) ((int) $oldId);

            if (isset($map[$oldId]) && $map[$oldId] !== $number) {
                fclose($handle);

                $this->error(
                    "ID penerimaan lama {$oldId} memiliki nomor berbeda."
                );

                return null;
            }

            $map[$oldId] = $number;
        }

        fclose($handle);

        return $map;
    }

    // ==========================================
    // PARSE ANGKA DBF
    // ==========================================

    private function parseNumber(string $value): ?float
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $value = str_replace(' ', '', $value);

        if (
            strpos($value, '.') !== false &&
            strpos($value, ',') !== false
        ) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (strpos($value, ',') !== false) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    // ==========================================
    // PARSE TANGGAL
    // ==========================================

    private function parseDate(string $value, string $fallback): string
    {
        $value = trim($value);

        if ($value === '') {
            return $fallback;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return $fallback;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}

