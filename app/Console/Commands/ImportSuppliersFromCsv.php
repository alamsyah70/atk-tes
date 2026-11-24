<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportSuppliersFromCsv extends Command
{
    protected $signature = 'atk:import-supplier
                            {file=D:\IT\App\migrasi\supplier.csv : Lokasi CSV}
                            {--branch= : ID cabang tujuan}
                            {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import supplier dari CSV dengan header fleksibel berdasarkan cabang';

    public function handle()
    {
        $file = $this->argument('file');
        $branchId = (int) $this->option('branch');
        $dryRun = $this->option('dry-run');

        // Validasi file
        if (!is_file($file)) {
            $this->error("File CSV tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        // Validasi cabang
        if ($branchId <= 0 ||
            !DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang ID {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        // Validasi tabel dan kolom tujuan
        $requiredColumns = [
            'branch_id',
            'kode_supplier',
            'nama_supplier',
            'created_at',
            'updated_at',
        ];

        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('suppliers', $column)) {
                $this->error("Kolom {$column} tidak ditemukan pada tabel suppliers.");
                return self::FAILURE;
            }
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('File CSV tidak dapat dibuka.');
            return self::FAILURE;
        }

        // Baca header CSV
        $headers = fgetcsv($handle, 0, ',');

        if (!$headers) {
            fclose($handle);
            $this->error('Header CSV tidak ditemukan.');
            return self::FAILURE;
        }

        // Normalisasi header
        $headers = array_map(function ($header) {
            $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
            return strtolower(trim($header));
        }, $headers);

        /*
         * PEMETAAN HEADER CSV KE TABEL SUPPLIERS
         */
        $mapping = [
            'kode_supplier' => [
                'kode_supplier',
                'kode_suppl',
                'sup',
                'kode',
                'kd_supplier',
                'kd_suppl',
                'supplier_code',
            ],

            'nama_supplier' => [
                'nama_supplier',
                'nama_suppl',
                'name',
                'nama',
                'supplier',
                'supplier_name',
            ],

            'alamat' => [
                'alamat',
                'address',
                'addr',
                'alamat_supplier',
                'supplier_address',
            ],

            'no_telepon' => [
                'no_telepon',
                'telepon',
                'telp',
                'phone',
                'no_hp',
                'handphone',
                'telephone',
            ],
        ];

        // Cari posisi kolom CSV
        $columnIndex = [];

        foreach ($mapping as $target => $possibleNames) {
            foreach ($possibleNames as $name) {
                $index = array_search($name, $headers, true);

                if ($index !== false) {
                    $columnIndex[$target] = $index;
                    break;
                }
            }
        }

        // Kode dan nama supplier wajib tersedia
        if (
            !isset($columnIndex['kode_supplier']) ||
            !isset($columnIndex['nama_supplier'])
        ) {
            fclose($handle);

            $this->error('Kolom kode supplier atau nama supplier tidak ditemukan.');
            $this->line('Header CSV yang terbaca: ' . implode(', ', $headers));

            return self::FAILURE;
        }

        $inserted = 0;
        $skipped = 0;
        $invalid = 0;
        $rowNumber = 1;

        $this->info('Memulai pemeriksaan data supplier...');
        $this->info("File CSV: {$file}");
        $this->info("Cabang tujuan: {$branchId}");

        if ($dryRun) {
            $this->warn('MODE DRY RUN: tidak ada data yang disimpan.');
        }

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowNumber++;

                // Abaikan baris kosong
                if (
                    count($row) === 1 &&
                    trim((string) ($row[0] ?? '')) === ''
                ) {
                    continue;
                }

                $getValue = function ($target) use ($columnIndex, $row) {
                    if (!isset($columnIndex[$target])) {
                        return null;
                    }

                    return trim((string) ($row[$columnIndex[$target]] ?? ''));
                };

                $kode = $getValue('kode_supplier');
                $nama = $getValue('nama_supplier');
                $alamat = $getValue('alamat');
                $telepon = $getValue('no_telepon');

                // Validasi data wajib
                if ($kode === '' || $nama === '') {
                    $invalid++;
                    $this->warn("Baris {$rowNumber}: kode atau nama supplier kosong.");
                    continue;
                }

                // Periksa duplikasi dalam cabang yang sama
                $exists = DB::table('suppliers')
                    ->where('branch_id', $branchId)
                    ->where('kode_supplier', $kode)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $this->warn("Baris {$rowNumber}: kode {$kode} sudah ada di cabang {$branchId}.");
                    continue;
                }

                $data = [
                    'branch_id' => $branchId,
                    'kode_supplier' => $kode,
                    'nama_supplier' => $nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Isi kolom opsional jika tersedia di database
                if (Schema::hasColumn('suppliers', 'alamat')) {
                    $data['alamat'] = $alamat !== '' ? $alamat : null;
                }

                if (Schema::hasColumn('suppliers', 'no_telepon')) {
                    $data['no_telepon'] = $telepon !== '' ? $telepon : null;
                }

                if (Schema::hasColumn('suppliers', 'email')) {
                    $data['email'] = null;
                }

                if (!$dryRun) {
                    DB::table('suppliers')->insert($data);
                }

                $inserted++;
            }

            fclose($handle);

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }

        } catch (\Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            DB::rollBack();

            $this->error('Import gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('HASIL IMPORT SUPPLIER');

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Siap diimport / berhasil', $inserted],
                ['Dilewati (duplikat)', $skipped],
                ['Data tidak valid', $invalid],
                ['Total diperiksa', $inserted + $skipped + $invalid],
            ]
        );

        if ($dryRun) {
            $this->warn('Simulasi selesai. Belum ada data yang disimpan.');
        } else {
            $this->info('Import supplier berhasil diselesaikan.');
        }

        return self::SUCCESS;
    }
}
