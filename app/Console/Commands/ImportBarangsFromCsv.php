<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportBarangsFromCsv extends Command
{
    protected $signature = 'atk:import-barangs
                            {file=D:\IT\App\migrasi\barangs.csv : Lokasi CSV}
                            {--branch= : ID cabang tujuan}
                            {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import master barang dari CSV hasil konversi DBF';

    public function handle()
    {
        $file = $this->argument('file');
        $branchId = $this->option('branch');
        $dryRun = $this->option('dry-run');

        if (!$branchId) {
            $this->error('Parameter --branch wajib diisi.');
            return self::FAILURE;
        }

        if (!is_file($file)) {
            $this->error("File CSV tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        if (!DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang dengan ID {$branchId} tidak ditemukan.");
            return self::FAILURE;
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

        $headers = array_map(function ($header) {
            $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
            return strtolower(trim($header));
        }, $headers);

        /*
         * PEMETAAN KOLOM CSV KE TABEL BARANGS
         *
         * Sesuaikan nama kolom kiri jika header CSV berbeda.
         */
        $mapping = [
            'kode_barang' => ['kode_barang', 'kode_baran', 'kode', 'kd_barang'],
            'nama_barang' => ['nama_barang', 'nama_baran', 'nama', 'nm_barang'],
            'kategori'    => ['kategori', 'jenis', 'golongan'],
            'satuan'      => ['satuan', 'unit'],
            'lokasi'      => ['lokasi', 'rak', 'tempat'],
        ];

        $columnIndex = [];

        foreach ($mapping as $target => $possibleNames) {
            foreach ($possibleNames as $name) {
                $index = array_search(strtolower($name), $headers, true);

                if ($index !== false) {
                    $columnIndex[$target] = $index;
                    break;
                }
            }
        }

        if (!isset($columnIndex['kode_barang']) ||
            !isset($columnIndex['nama_barang'])) {
            fclose($handle);

            $this->error('Kolom kode barang atau nama barang tidak ditemukan.');
            $this->line('Header CSV: ' . implode(', ', $headers));

            return self::FAILURE;
        }

        $requiredColumns = [
            'branch_id',
            'kode_barang',
            'nama_barang',
            'created_at',
            'updated_at',
        ];

        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('barangs', $column)) {
                fclose($handle);
                $this->error("Kolom {$column} tidak ditemukan pada tabel barangs.");
                return self::FAILURE;
            }
        }

        $inserted = 0;
        $skipped = 0;
        $invalid = 0;
        $rowNumber = 1;

        $this->info('Memulai pemeriksaan data barang...');
        $this->info("Cabang tujuan: {$branchId}");

        if ($dryRun) {
            $this->warn('MODE DRY RUN: tidak ada data yang disimpan.');
        }

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowNumber++;

                if (count($row) < count($headers)) {
                    $invalid++;
                    continue;
                }

                $getValue = function ($target) use ($columnIndex, $row) {
                    if (!isset($columnIndex[$target])) {
                        return null;
                    }

                    return trim((string) ($row[$columnIndex[$target]] ?? ''));
                };

                $kode = $getValue('kode_barang');
                $nama = $getValue('nama_barang');

                if ($kode === '' || $nama === '') {
                    $invalid++;
                    continue;
                }

                // Periksa duplikasi dalam cabang yang sama
                $exists = DB::table('barangs')
                    ->where('branch_id', $branchId)
                    ->where('kode_barang', $kode)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $data = [
                    'branch_id'  => $branchId,
                    'kode_barang' => $kode,
                    'nama_barang' => $nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach (['kategori', 'satuan', 'lokasi'] as $field) {
                    if (Schema::hasColumn('barangs', $field)) {
                        $data[$field] = $getValue($field) ?: null;
                    }
                }

                if (!$dryRun) {
                    DB::table('barangs')->insert($data);
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
            fclose($handle);
            DB::rollBack();

            $this->error('Import gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('HASIL IMPORT BARANG');
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
            $this->info('Import barang berhasil diselesaikan.');
        }

        return self::SUCCESS;
    }
}
