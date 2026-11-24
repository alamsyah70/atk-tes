<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Schema;

class ImportDetailPenerimaansFromCsv extends Command
{
    protected $signature = 'atk:import-detail-penerimaans
        {file=D:\IT\App\migrasi\detail_penerimaans.csv}
        {--branch= : ID cabang tujuan}
        {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import detail penerimaan dengan pemetaan barang_id dan penerimaan_id berdasarkan kode dan nomor';

    public function handle()
    {
        $path = $this->argument('file');

        // Mendukung path absolut Windows dan path relatif
        $file = preg_match('/^[A-Za-z]:[\/\\\\]/', $path)
            ? $path
            : base_path($path);

        $branchId = (int) $this->option('branch');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('IMPORT DETAIL PENERIMAAN');
        $this->line("File   : {$file}");
        $this->line("Cabang : {$branchId}");

        if ($branchId <= 0) {
            $this->error('Parameter --branch wajib diisi.');
            return self::FAILURE;
        }

        if (!is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        if (!Schema::hasTable('branches')) {
            $this->error('Tabel branches tidak ditemukan.');
            return self::FAILURE;
        }

        if (!DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang ID {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        foreach ([
            'barangs' => [
                'id',
                'branch_id',
                'kode_barang',
            ],
            'penerimaans' => [
                'id',
                'branch_id',
                'nomor_penerimaan',
            ],
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
            ],
        ] as $table => $columns) {
            if (!Schema::hasTable($table)) {
                $this->error("Tabel {$table} tidak ditemukan.");
                return self::FAILURE;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $this->error("Kolom {$table}.{$column} tidak ditemukan.");
                    return self::FAILURE;
                }
            }
        }

        $csv = $this->openCsv($file);

        if ($csv === null) {
            return self::FAILURE;
        }

        [$handle, $delimiter, $columns] = $csv;

        $this->line(
            'Header terbaca: ' . implode(', ', array_keys($columns))
        );

        $required = [
            'nomor_penerimaan',
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

        // Counter
        $total = 0;
        $ready = 0;
        $inserted = 0;
        $duplicates = 0;
        $receiptNotFound = 0;
        $barangNotFound = 0;
        $invalid = 0;

        // Cache agar tidak query berulang untuk kode/nomor yang sama
        $receiptCache = [];
        $barangCache = [];

        $now = now()->format('Y-m-d H:i:s');

        $get = function ($row, $field) use ($columns) {
            if (!isset($columns[$field])) {
                return '';
            }

            return trim((string) ($row[$columns[$field]] ?? ''));
        };

        if ($dryRun) {
            $this->warn('MODE DRY RUN: data hanya disimulasikan.');
        }

        DB::beginTransaction();

        try {
            while (($row = fgetcsv(
                $handle,
                0,
                $delimiter,
                '"',
                '\\'
            )) !== false) {

                // Lewati baris kosong
                if (
                    count($row) === 1 &&
                    trim((string) $row[0]) === ''
                ) {
                    continue;
                }

                $total++;

                // =====================================
                // 1. AMBIL DATA CSV
                // =====================================

                $nomorPenerimaan = $get($row, 'nomor_penerimaan');
                $kodeBarang = $get($row, 'kode_barang');
                $namaBarang = $get($row, 'nama_barang');

                $kategori = $get($row, 'kategori');
                $satuan = $get($row, 'satuan');
                $lokasi = $get($row, 'lokasi');

                $qty = $this->parseNumber($get($row, 'qty'));
                $harga = $this->parseNumber($get($row, 'harga'));

                $jumlahRaw = $get($row, 'jumlah');

                $jumlah = $jumlahRaw === ''
                    ? null
                    : $this->parseNumber($jumlahRaw);

                // =====================================
                // 2. VALIDASI DATA
                // =====================================

                if (
                    $nomorPenerimaan === '' ||
                    $kodeBarang === '' ||
                    $namaBarang === '' ||
                    $qty === null ||
                    $harga === null ||
                    $qty < 0 ||
                    $harga < 0 ||
                    ($jumlahRaw !== '' && $jumlah === null)
                ) {
                    $invalid++;

                    if ($invalid <= 10) {
                        $this->warn(
                            "Baris {$total}: data tidak valid."
                        );
                    }

                    continue;
                }

                // Jika jumlah kosong, hitung qty x harga
                if ($jumlah === null) {
                    $jumlah = $qty * $harga;
                }

                // =====================================
                // 3. NOMOR PENERIMAAN DBF
                //    -> penerimaan_id DATABASE
                // =====================================

                $receiptKey = strtoupper(trim($nomorPenerimaan));

                if (!array_key_exists($receiptKey, $receiptCache)) {
                    $receiptCache[$receiptKey] = DB::table('penerimaans')
                        ->where('branch_id', $branchId)
                        ->whereRaw(
                            'UPPER(TRIM(nomor_penerimaan)) = ?',
                            [$receiptKey]
                        )
                        ->value('id');
                }

                $penerimaanId = $receiptCache[$receiptKey];

                if (!$penerimaanId) {
                    $receiptNotFound++;

                    if ($receiptNotFound <= 10) {
                        $this->warn(
                            "Baris {$total}: nomor penerimaan '{$nomorPenerimaan}' tidak ditemukan di cabang {$branchId}."
                        );
                    }

                    continue;
                }

                // =====================================
                // 4. KODE BARANG DBF
                //    -> barang_id DATABASE
                // =====================================

                $barangKey = strtoupper(trim($kodeBarang));

                if (!array_key_exists($barangKey, $barangCache)) {
                    $barangCache[$barangKey] = DB::table('barangs')
                        ->where('branch_id', $branchId)
                        ->whereRaw(
                            'UPPER(TRIM(kode_barang)) = ?',
                            [$barangKey]
                        )
                        ->first();
                }

                $barang = $barangCache[$barangKey];

                if (!$barang) {
                    $barangNotFound++;

                    if ($barangNotFound <= 10) {
                        $this->warn(
                            "Baris {$total}: kode barang '{$kodeBarang}' tidak ditemukan di cabang {$branchId}."
                        );
                    }

                    continue;
                }

                // =====================================
                // 5. AMBIL DATA MASTER BARANG
                //    JIKA DATA DBF KOSONG
                // =====================================

                if ($kategori === '') {
                    $kategori = $barang->kategori ?? '';
                }

                if ($satuan === '') {
                    $satuan = $barang->satuan ?? '';
                }

                if ($lokasi === '') {
                    $lokasi = $barang->lokasi ?? '';
                }

                // =====================================
                // 6. TANGGAL CREATED / UPDATED
                // =====================================

                $createdAt = $this->parseDate(
                    $get($row, 'created_at'),
                    $now
                );

                $updatedAt = $this->parseDate(
                    $get($row, 'updated_at'),
                    $now
                );

                // =====================================
                // 7. SUSUN DATA DETAIL
                // =====================================

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
                ];

                if (Schema::hasColumn('detail_penerimaans', 'created_at')) {
                    $data['created_at'] = $createdAt;
                }

                if (Schema::hasColumn('detail_penerimaans', 'updated_at')) {
                    $data['updated_at'] = $updatedAt;
                }

                // =====================================
                // 8. CEK DUPLIKAT
                // =====================================

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

                // =====================================
                // 9. SIMPAN DATA
                // =====================================

                $ready++;

                if (!$dryRun) {
                    try {
                        DB::table('detail_penerimaans')->insert($data);
                        $inserted++;
                    } catch (Throwable $e) {
                        $invalid++;

                        if ($invalid <= 10) {
                            $this->warn(
                                "Gagal menyimpan baris {$total}: " .
                                $e->getMessage()
                            );
                        }
                    }
                }

                if ($total % 100 === 0) {
                    $this->line("Diperiksa: {$total}");
                }
            }

            fclose($handle);

            // =====================================
            // 10. COMMIT / ROLLBACK
            // =====================================

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            // =====================================
            // 11. HASIL IMPORT
            // =====================================

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
                $this->warn(
                    'DRY RUN selesai. Tidak ada data yang disimpan.'
                );
            } else {
                $this->info(
                    "Import selesai. Berhasil disimpan: {$inserted}"
                );
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            DB::rollBack();

            $this->error('Import gagal: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    // ==========================================
    // BUKA CSV DAN DETEKSI DELIMITER
    // ==========================================

    private function openCsv(string $path): ?array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            $this->error("Tidak dapat membuka file: {$path}");
            return null;
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);
            $this->error('File CSV kosong.');
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
            $this->error('Header CSV tidak terbaca.');
            return null;
        }

        $columns = [];

        foreach ($header as $index => $name) {
            $name = preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                (string) $name
            );

            $name = strtolower(trim($name));

            // Hilangkan karakter selain huruf, angka, dan underscore
            $name = preg_replace('/[^a-z0-9_]/', '', $name);

            $columns[$name] = $index;
        }

        $this->line(
            'Delimiter: ' . ($delimiter === "\t" ? 'TAB' : $delimiter)
        );

        return [
            $handle,
            $delimiter,
            $this->mapHeaders($columns),
        ];
    }

    // ==========================================
    // NORMALISASI NAMA HEADER
    // ==========================================

    private function mapHeaders(array $header): array
    {
        $aliases = [
            'nomor_penerimaan' => [
                'nomor_penerimaan',
                'nomor_pene',
                'nomor',
                'no_penerimaan',
                'no_pene',
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

            'kategori' => [
                'kategori',
                'category',
            ],

            'satuan' => [
                'satuan',
                'unit',
            ],

            'lokasi' => [
                'lokasi',
                'location',
            ],

            'qty' => [
                'qty',
                'quantity',
            ],

            'harga' => [
                'harga',
                'price',
            ],

            'jumlah' => [
                'jumlah',
                'total',
                'subtotal',
            ],

            'created_at' => [
                'created_at',
                'created',
            ],

            'updated_at' => [
                'updated_at',
                'updated',
            ],
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

        return is_numeric($value)
            ? (float) $value
            : null;
    }

    // ==========================================
    // PARSE TANGGAL DBF
    // ==========================================

    private function parseDate(string $value, string $fallback): string
    {
        $value = trim($value);

        if ($value === '') {
            return $fallback;
        }

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd-m-Y',
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);

            if ($date !== false) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return $fallback;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
