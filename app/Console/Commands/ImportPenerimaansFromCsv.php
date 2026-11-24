<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Throwable;

class ImportPenerimaansFromCsv extends Command
{
    protected $signature = 'atk:import-penerimaans
        {file=D:\IT\App\migrasi\penerimaans.csv}
        {--branch=}
        {--dry-run}';

    protected $description = 'Import data penerimaan dari CSV dengan pencocokan kode supplier berdasarkan cabang';

    public function handle()
    {
        $path = $this->argument('file');

        // Mendukung path absolut Windows maupun path relatif project
        $file = preg_match('/^[A-Za-z]:[\/\\\\]/', $path)
            ? $path
            : base_path($path);

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $branchId = $this->option('branch');

        if (!$branchId) {
            $this->error('Parameter --branch wajib diisi.');
            $this->line('Contoh: php artisan atk:import-penerimaans --branch=7 --dry-run');
            return self::FAILURE;
        }

        if (!DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang dengan ID {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        // Pastikan kolom penting tersedia
        $requiredColumns = [
            'branch_id',
            'supplier_id',
            'nomor_penerimaan',
            'tanggal',
        ];

        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('penerimaans', $column)) {
                $this->error("Kolom penerimaans.{$column} tidak ditemukan.");
                return self::FAILURE;
            }
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('Gagal membuka file CSV.');
            return self::FAILURE;
        }

        // Baca header CSV
        $headers = fgetcsv($handle, 0, ',');

        if (!$headers) {
            fclose($handle);
            $this->error('Header CSV tidak ditemukan atau file kosong.');
            return self::FAILURE;
        }

        $headers = array_map(function ($header) {
            $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);
            return strtolower(trim($header));
        }, $headers);

        $headerIndex = [];

        foreach ($headers as $index => $header) {
            $headerIndex[$header] = $index;
        }

        // Alias header CSV
        $aliases = [
            'nomor_penerimaan' => [
                'nomor_penerimaan',
                'nomor_pene',
                'nomor',
                'no_penerimaan',
                'no_pene',
            ],

            // Kolom supplier_i dari DBF berisi KODE supplier
            'kode_supplier' => [
                'supplier_i',
                'kode_supplier',
                'kode_sup',
                'sup',
                'kd_supplier',
                'kd_sup',
                'supplier_code',
            ],

            // Digunakan jika CSV memiliki nama supplier secara terpisah
            'nama_supplier' => [
                'nama_supplier',
                'nama_sup',
                'supplier_name',
                'nama',
            ],

            'tanggal' => [
                'tanggal',
                'tgl',
                'date',
            ],

            'keterangan' => [
                'keterangan',
                'ket',
                'description',
            ],

            'bukti_po' => [
                'bukti_po',
                'bukti',
                'file_po',
            ],
        ];

        // Cari indeks setiap kolom berdasarkan alias
        $columns = [];

        foreach ($aliases as $field => $fieldAliases) {
            $columns[$field] = null;

            foreach ($fieldAliases as $alias) {
                if (array_key_exists($alias, $headerIndex)) {
                    $columns[$field] = $headerIndex[$alias];
                    break;
                }
            }
        }

        // Nomor, tanggal, dan salah satu identitas supplier wajib tersedia
        if (
            $columns['nomor_penerimaan'] === null ||
            $columns['tanggal'] === null ||
            (
                $columns['kode_supplier'] === null &&
                $columns['nama_supplier'] === null
            )
        ) {
            fclose($handle);

            $this->error('Kolom wajib tidak ditemukan di CSV.');
            $this->line('Header yang terbaca: ' . implode(', ', $headers));
            $this->line('Pastikan ada nomor_pene, supplier_i, dan tanggal.');
            return self::FAILURE;
        }

        $this->info("File: {$file}");
        $this->info("Cabang: {$branchId}");

        if ($this->option('dry-run')) {
            $this->warn('MODE DRY RUN: tidak ada data yang akan disimpan.');
        }

        $total = 0;
        $berhasil = 0;
        $gagal = 0;
        $duplikat = 0;

        $nomorDalamCsv = [];

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // Lewati baris kosong
                if (count(array_filter($row, function ($value) {
                    return trim((string) $value) !== '';
                })) === 0) {
                    continue;
                }

                $total++;

                $getValue = function ($field) use ($columns, $row) {
                    $index = $columns[$field] ?? null;

                    if ($index === null || !isset($row[$index])) {
                        return null;
                    }

                    $value = trim((string) $row[$index]);

                    return $value === '' ? null : $value;
                };

                $nomor = $getValue('nomor_penerimaan');
                $kodeSupplier = $getValue('kode_supplier');
                $namaSupplier = $getValue('nama_supplier');
                $tanggalRaw = $getValue('tanggal');
                $keterangan = $getValue('keterangan');
                $buktiPo = $getValue('bukti_po');

                // Validasi nomor penerimaan
                if (!$nomor) {
                    $gagal++;
                    $this->warn("Baris {$total}: nomor penerimaan kosong.");
                    continue;
                }

                // Validasi tanggal
                $tanggal = $this->parseTanggal($tanggalRaw);

                if (!$tanggal) {
                    $gagal++;
                    $this->warn(
                        "Baris {$total}: tanggal tidak valid '{$tanggalRaw}'."
                    );
                    continue;
                }

                // Cari supplier berdasarkan kode terlebih dahulu
                $supplierQuery = DB::table('suppliers')
                    ->where('branch_id', $branchId);

                if ($kodeSupplier) {
                    $supplierQuery->whereRaw(
                        'UPPER(TRIM(kode_supplier)) = ?',
                        [strtoupper(trim($kodeSupplier))]
                    );
                } else {
                    // Fallback jika CSV hanya memiliki nama supplier
                    $supplierQuery->whereRaw(
                        'UPPER(TRIM(nama_supplier)) = ?',
                        [strtoupper(trim($namaSupplier))]
                    );
                }

                $suppliers = $supplierQuery->limit(2)->get();

                if ($suppliers->isEmpty()) {
                    $gagal++;

                    $identitas = $kodeSupplier
                        ? "kode supplier '{$kodeSupplier}'"
                        : "nama supplier '{$namaSupplier}'";

                    $this->warn(
                        "Baris {$total}: {$identitas} tidak ditemukan di cabang {$branchId}."
                    );

                    continue;
                }

                if ($suppliers->count() > 1) {
                    $gagal++;

                    $this->warn(
                        "Baris {$total}: supplier '{$kodeSupplier}' ditemukan lebih dari satu."
                    );

                    continue;
                }

                $supplierId = $suppliers->first()->id;

                // Cek nomor penerimaan duplikat di CSV
                $duplicateKey = $branchId . '|' . strtoupper($nomor);

                if (isset($nomorDalamCsv[$duplicateKey])) {
                    $duplikat++;

                    $this->warn(
                        "Baris {$total}: nomor {$nomor} duplikat di dalam CSV."
                    );

                    continue;
                }

                $nomorDalamCsv[$duplicateKey] = true;

                // Cek nomor penerimaan yang sudah ada di database
                $exists = DB::table('penerimaans')
                    ->where('branch_id', $branchId)
                    ->where('nomor_penerimaan', $nomor)
                    ->exists();

                if ($exists) {
                    $duplikat++;

                    $this->warn(
                        "Baris {$total}: nomor {$nomor} sudah ada di database."
                    );

                    continue;
                }

                $now = now();

                $data = [
                    'branch_id' => $branchId,
                    'supplier_id' => $supplierId,
                    'nomor_penerimaan' => $nomor,
                    'tanggal' => $tanggal,
                ];

                if (Schema::hasColumn('penerimaans', 'keterangan')) {
                    $data['keterangan'] = $keterangan;
                }

                if (Schema::hasColumn('penerimaans', 'bukti_po')) {
                    $data['bukti_po'] = $buktiPo;
                }

                if (Schema::hasColumn('penerimaans', 'created_at')) {
                    $data['created_at'] = $now;
                }

                if (Schema::hasColumn('penerimaans', 'updated_at')) {
                    $data['updated_at'] = $now;
                }

                DB::table('penerimaans')->insert($data);

                $berhasil++;
            }

            fclose($handle);

            if ($this->option('dry-run')) {
                DB::rollBack();
                $this->warn('DRY RUN selesai. Seluruh perubahan database dibatalkan.');
            } else {
                DB::commit();
                $this->info('Import berhasil disimpan.');
            }

        } catch (Throwable $e) {
            fclose($handle);

            DB::rollBack();

            $this->error('Import gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('========== HASIL IMPORT ==========');
        $this->line("Total baris     : {$total}");
        $this->line("Berhasil        : {$berhasil}");
        $this->line("Gagal           : {$gagal}");
        $this->line("Duplikat        : {$duplikat}");
        $this->info('==================================');

        return self::SUCCESS;
    }

    /**
     * Mengubah berbagai format tanggal CSV menjadi Y-m-d.
     */
    private function parseTanggal(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        $formats = [
            'd/m/Y:H:i:s',
            'd/m/Y:H:i',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd-m-Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (Throwable $e) {
                // Coba format berikutnya
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable $e) {
            return null;
        }
    }
}
