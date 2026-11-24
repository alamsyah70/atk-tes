<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportDetailPengeluaransFromCsv extends Command
{
    protected $signature = 'atk:import-detail-pengeluarans
        {file=D:\IT\App\migrasi\detail_pengeluarans.csv}
        {--branch=1 : ID cabang tujuan}
        {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import detail pengeluaran dengan pemetaan ID dan perhitungan harga pokok otomatis';

    public function handle()
    {
        $path = $this->argument('file');

        // Mendukung path absolut Windows dan path relatif project
        $file = preg_match('/^[A-Za-z]:[\/\\\\]/', $path)
            ? $path
            : base_path($path);

        $branchId = (int) $this->option('branch');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('IMPORT DETAIL PENGELUARAN');
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

        // ==========================================
        // VALIDASI TABEL DAN KOLOM
        // ==========================================

        $requiredColumns = [
            'barangs' => [
                'id',
                'branch_id',
                'kode_barang',
            ],

            'pengeluarans' => [
                'id',
                'branch_id',
                'nomor_pengeluaran',
                'tanggal',
            ],

            'detail_pengeluarans' => [
                'pengeluaran_id',
                'barang_id',
                'kode_barang',
                'nama_barang',
                'qty',
                'harga_pokok',
                'jumlah_harga_pokok',
            ],

            'penerimaans' => [
                'id',
                'branch_id',
                'tanggal',
            ],

            'detail_penerimaans' => [
                'penerimaan_id',
                'kode_barang',
                'qty',
                'harga',
            ],
        ];

        foreach ($requiredColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                $this->error("Tabel {$table} tidak ditemukan.");
                return self::FAILURE;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $this->error(
                        "Kolom {$table}.{$column} tidak ditemukan."
                    );

                    return self::FAILURE;
                }
            }
        }

        // ==========================================
        // BUKA CSV
        // ==========================================

        $csv = $this->openCsv($file);

        if ($csv === null) {
            return self::FAILURE;
        }

        [$handle, $delimiter, $columns] = $csv;

        $this->line(
            'Header terbaca: ' . implode(', ', array_keys($columns))
        );

        $required = [
            'nomor_pengeluaran',
            'kode_barang',
            'nama_barang',
            'qty',
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
        // DETEKSI KOLOM TABEL TUJUAN
        // ==========================================

        $tableColumns = array_flip(
            Schema::getColumnListing('detail_pengeluarans')
        );

        $hasKategori = isset($tableColumns['kategori']);
        $hasSatuan = isset($tableColumns['satuan']);
        $hasLokasi = isset($tableColumns['lokasi']);
        $hasHarga = isset($tableColumns['harga']);
        $hasJumlah = isset($tableColumns['jumlah']);
        $hasCreatedAt = isset($tableColumns['created_at']);
        $hasUpdatedAt = isset($tableColumns['updated_at']);

        // ==========================================
        // CACHE
        // ==========================================

        $pengeluaranCache = [];
        $barangCache = [];
        $hargaPokokCache = [];

        // ==========================================
        // COUNTER
        // ==========================================

        $total = 0;
        $ready = 0;
        $inserted = 0;
        $duplicates = 0;
        $pengeluaranNotFound = 0;
        $barangNotFound = 0;
        $hargaPokokNotFound = 0;
        $invalid = 0;

        // Mendeteksi duplikat dari CSV yang sama
        $seenSource = [];

        // ==========================================
        // FINGERPRINT DUPLIKAT
        // ==========================================

        $fingerprint = function (
            $pengeluaranId,
            $kode,
            $nama,
            $qty,
            $satuan
        ) {
            return hash('sha256', json_encode([
                (int) $pengeluaranId,
                strtoupper(trim((string) $kode)),
                strtoupper(trim((string) $nama)),
                number_format((float) $qty, 4, '.', ''),
                strtoupper(trim((string) $satuan)),
            ]));
        };

        // ==========================================
        // AMBIL NILAI CSV
        // ==========================================

        $get = function ($row, $field) use ($columns) {
            if (!isset($columns[$field])) {
                return '';
            }

            return trim((string) ($row[$columns[$field]] ?? ''));
        };

        if ($dryRun) {
            $this->warn(
                'MODE DRY RUN: tidak ada data yang disimpan.'
            );
        }

        DB::beginTransaction();

        try {
            $insertRows = [];
            $batchSize = 500;

            // ==========================================
            // PROSES SETIAP BARIS CSV
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

                // =====================================
                // 1. AMBIL DATA CSV
                // =====================================

                $nomor = $get($row, 'nomor_pengeluaran');
                $kode = $get($row, 'kode_barang');
                $nama = $get($row, 'nama_barang');

                $qty = $this->parseNumber(
                    $get($row, 'qty')
                );

                $satuanCsv = $get($row, 'satuan');

                $hargaRaw = $get($row, 'harga');
                $jumlahRaw = $get($row, 'jumlah');

                $harga = $hargaRaw === ''
                    ? 0
                    : $this->parseNumber($hargaRaw);

                $jumlah = $jumlahRaw === ''
                    ? null
                    : $this->parseNumber($jumlahRaw);

                // =====================================
                // 2. VALIDASI DATA
                // =====================================

                if (
                    $nomor === '' ||
                    $kode === '' ||
                    $nama === '' ||
                    $qty === null ||
                    $qty < 0 ||
                    $harga === null ||
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

                if ($jumlah === null) {
                    $jumlah = $qty * $harga;
                }

                // =====================================
                // 3. CARI PENGELUARAN
                //    NOMOR CSV -> ID DATABASE
                // =====================================

                $nomorKey = strtoupper(trim($nomor));

                if (!array_key_exists(
                    $nomorKey,
                    $pengeluaranCache
                )) {
                    $pengeluaranCache[$nomorKey] =
                        DB::table('pengeluarans')
                            ->where('branch_id', $branchId)
                            ->whereRaw(
                                'UPPER(TRIM(nomor_pengeluaran)) = ?',
                                [$nomorKey]
                            )
                            ->first([
                                'id',
                                'tanggal',
                            ]);
                }

                $pengeluaran = $pengeluaranCache[$nomorKey];

                if (!$pengeluaran) {
                    $pengeluaranNotFound++;

                    if ($pengeluaranNotFound <= 10) {
                        $this->warn(
                            "Baris {$total}: nomor pengeluaran '{$nomor}' tidak ditemukan di cabang {$branchId}."
                        );
                    }

                    continue;
                }

                $pengeluaranId = (int) $pengeluaran->id;

                $tanggalPengeluaran = $pengeluaran->tanggal
                    ? substr(
                        (string) $pengeluaran->tanggal,
                        0,
                        10
                    )
                    : null;

                // =====================================
                // 4. CARI MASTER BARANG
                //    KODE CSV -> barang_id DATABASE
                // =====================================

                $kodeKey = strtoupper(trim($kode));

                if (!array_key_exists(
                    $kodeKey,
                    $barangCache
                )) {
                    $barangCache[$kodeKey] =
                        DB::table('barangs')
                            ->where('branch_id', $branchId)
                            ->whereRaw(
                                'UPPER(TRIM(kode_barang)) = ?',
                                [$kodeKey]
                            )
                            ->first();
                }

                $barang = $barangCache[$kodeKey];

                if (!$barang) {
                    $barangNotFound++;

                    if ($barangNotFound <= 10) {
                        $this->warn(
                            "Baris {$total}: kode barang '{$kode}' tidak ditemukan di cabang {$branchId}."
                        );
                    }

                    continue;
                }

                // =====================================
                // 5. DATA MASTER BARANG
                // =====================================

                $kategori = trim(
                    (string) ($barang->kategori ?? '')
                );

                $satuanMaster = trim(
                    (string) ($barang->satuan ?? '')
                );

                $lokasi = trim(
                    (string) ($barang->lokasi ?? '')
                );

                if ($kategori === '') {
                    $kategori = 'Lainnya';
                }

                if ($satuanCsv === '') {
                    $satuanCsv = $satuanMaster ?: 'PCS';
                }

                if ($lokasi === '') {
                    $lokasi = '-';
                }

                // =====================================
                // 6. HITUNG HARGA POKOK
                // =====================================

                $hargaPokokKey = $barang->id
                    . '|'
                    . ($tanggalPengeluaran ?? 'NULL');

                if (!array_key_exists(
                    $hargaPokokKey,
                    $hargaPokokCache
                )) {
                    $hargaPokokCache[$hargaPokokKey] =
                        $this->getHargaPokok(
                            $branchId,
                            (int) $barang->id,
                            $kode,
                            $tanggalPengeluaran
                        );
                }

                $hargaPokok = $hargaPokokCache[$hargaPokokKey];

                if ($hargaPokok === null) {
                    $hargaPokokNotFound++;

                    // Tidak ada riwayat penerimaan sebelum
                    // tanggal pengeluaran.
                    // Tetap import dengan harga pokok 0.

                    $hargaPokok = 0;

                    if ($hargaPokokNotFound <= 10) {
                        $this->warn(
                            "Baris {$total}: harga pokok '{$kode}' tidak ditemukan sebelum tanggal pengeluaran."
                        );
                    }
                }

                $jumlahHargaPokok = round(
                    (float) $qty * (float) $hargaPokok,
                    2
                );

                // =====================================
                // 7. SUSUN DATA DETAIL
                // =====================================

                $data = [
                    'pengeluaran_id' => $pengeluaranId,
                    'barang_id' => $barang->id,
                    'kode_barang' => $kode,
                    'nama_barang' => $nama,
                    'qty' => $qty,

                    'harga_pokok' => $hargaPokok,
                    'jumlah_harga_pokok' => $jumlahHargaPokok,
                ];

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

                // =====================================
                // 8. CEK DUPLIKAT
                // =====================================

                $key = $fingerprint(
                    $pengeluaranId,
                    $kode,
                    $nama,
                    $qty,
                    $satuanCsv
                );

                $seenSource[$key] =
                    ($seenSource[$key] ?? 0) + 1;

                $existingQuery = DB::table(
                    'detail_pengeluarans'
                )
                    ->where('pengeluaran_id', $pengeluaranId)
                    ->whereRaw(
                        'UPPER(TRIM(kode_barang)) = ?',
                        [$kodeKey]
                    )
                    ->whereRaw(
                        'UPPER(TRIM(nama_barang)) = ?',
                        [strtoupper(trim($nama))]
                    )
                    ->where('qty', $qty);

                if ($hasSatuan) {
                    $existingQuery->whereRaw(
                        "UPPER(TRIM(COALESCE(satuan, ''))) = ?",
                        [strtoupper(trim($satuanCsv))]
                    );
                }

                $existingCount = $existingQuery->count();

                if ($seenSource[$key] <= $existingCount) {
                    $duplicates++;
                    continue;
                }

                $sourceOccurrence =
                    $seenSource[$key] - $existingCount;

                if ($sourceOccurrence > 1) {
                    $duplicates++;
                    continue;
                }

                // =====================================
                // 9. SIAPKAN INSERT
                // =====================================

                $insertRows[] = $data;
                $ready++;

                // =====================================
                // 10. INSERT BATCH 500 BARIS
                // =====================================

                if (count($insertRows) >= $batchSize) {
                    if (!$dryRun) {
                        DB::table('detail_pengeluarans')
                            ->insert($insertRows);

                        $inserted += count($insertRows);
                    }

                    $insertRows = [];
                }

                if ($total % 500 === 0) {
                    $this->line(
                        "Diperiksa: {$total}"
                    );
                }
            }

            // ==========================================
            // 11. INSERT SISA DATA
            // ==========================================

            if (!empty($insertRows)) {
                if (!$dryRun) {
                    DB::table('detail_pengeluarans')
                        ->insert($insertRows);

                    $inserted += count($insertRows);
                }
            }

            fclose($handle);

            // ==========================================
            // 12. COMMIT / ROLLBACK
            // ==========================================

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            // ==========================================
            // 13. HASIL IMPORT
            // ==========================================

            $this->newLine();

            $this->info(
                'HASIL IMPORT DETAIL PENGELUARAN'
            );

            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Siap diimport', $ready],
                    ['Berhasil disimpan', $inserted],
                    ['Duplikat', $duplicates],
                    ['Pengeluaran tidak ditemukan', $pengeluaranNotFound],
                    ['Barang tidak ditemukan', $barangNotFound],
                    ['Harga pokok tidak ditemukan', $hargaPokokNotFound],
                    ['Data tidak valid', $invalid],
                    ['Total diperiksa', $total],
                ]
            );

            if ($dryRun) {
                $this->warn(
                    'DRY RUN SELESAI. TIDAK ADA DATA DISIMPAN.'
                );
            } else {
                $this->info(
                    "IMPORT SELESAI. Berhasil disimpan: {$inserted}"
                );
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $this->error(
                'Import gagal: ' . $e->getMessage()
            );

            $this->line(
                $e->getFile() . ':' . $e->getLine()
            );

            return self::FAILURE;
        }
    }

    // ==========================================
    // HITUNG HARGA POKOK RATA-RATA TERTIMBANG
    // ==========================================

    private function getHargaPokok(
        int $branchId,
        int $barangId,
        string $kodeBarang,
        ?string $tanggalPengeluaran
    ): ?float {

        if (!$tanggalPengeluaran) {
            return null;
        }

        $query = DB::table('detail_penerimaans as dp')
            ->join(
                'penerimaans as p',
                'p.id',
                '=',
                'dp.penerimaan_id'
            )
            ->where('p.branch_id', $branchId)
            ->whereDate(
                'p.tanggal',
                '<=',
                $tanggalPengeluaran
            )
            ->where('dp.qty', '>', 0)
            ->whereNotNull('dp.harga')
            ->where('dp.harga', '>=', 0);

        // Gunakan barang_id jika tersedia.
        // Untuk data lama dengan barang_id NULL,
        // gunakan pencocokan kode_barang.

        if (
            Schema::hasColumn(
                'detail_penerimaans',
                'barang_id'
            )
        ) {
            $query->where(function ($q) use (
                $barangId,
                $kodeBarang
            ) {
                $q->where(
                    'dp.barang_id',
                    $barangId
                )->orWhere(function ($sub) use (
                    $kodeBarang
                ) {
                    $sub->whereNull('dp.barang_id')
                        ->whereRaw(
                            'UPPER(TRIM(dp.kode_barang)) = ?',
                            [strtoupper(trim($kodeBarang))]
                        );
                });
            });
        } else {
            $query->whereRaw(
                'UPPER(TRIM(dp.kode_barang)) = ?',
                [strtoupper(trim($kodeBarang))]
            );
        }

        // Rata-rata tertimbang:
        // SUM(qty * harga) / SUM(qty)

        $result = $query
            ->selectRaw('
                SUM(dp.qty * dp.harga) /
                NULLIF(SUM(dp.qty), 0) AS harga_pokok,
                SUM(dp.qty) AS total_qty
            ')
            ->first();

        if (
            !$result ||
            $result->total_qty === null ||
            (float) $result->total_qty <= 0 ||
            $result->harga_pokok === null
        ) {
            return null;
        }

        return (float) $result->harga_pokok;
    }

    // ==========================================
    // BUKA CSV DAN DETEKSI DELIMITER
    // ==========================================

    private function openCsv(string $path): ?array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            $this->error(
                "Tidak dapat membuka file: {$path}"
            );

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

        $header = fgetcsv(
            $handle,
            0,
            $delimiter,
            '"',
            '\\'
        );

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

            $name = preg_replace(
                '/[^a-z0-9_]/',
                '',
                $name
            );

            $columns[$name] = $index;
        }

        $this->line(
            'Delimiter: ' .
            ($delimiter === "\t" ? 'TAB' : $delimiter)
        );

        return [
            $handle,
            $delimiter,
            $this->mapHeaders($columns),
        ];
    }

    // ==========================================
    // PEMETAAN ALIAS HEADER CSV
    // ==========================================

    private function mapHeaders(array $header): array
    {
        $aliases = [
            'nomor_pengeluaran' => [
                'nomor_pengeluaran',
                'nomor_peng',
                'nomor_penge',
                'nomor',
                'no_pengeluaran',
                'no_peng',
            ],

            'kode_barang' => [
                'kode_barang',
                'kode_baran',
                'kode_bara',
                'kode_bara2',
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

            'qty' => [
                'qty',
                'quantity',
            ],

            'satuan' => [
                'satuan',
                'unit',
            ],

            'harga' => [
                'harga',
                'price',
            ],

            'jumlah' => [
                'jumlah',
                'val',
                'total',
                'subtotal',
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
    // PARSE ANGKA CSV
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
}

