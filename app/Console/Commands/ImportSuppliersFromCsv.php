<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ImportSuppliersFromCsv extends Command
{
    protected $signature = 'atk:import-suppliers
                            {file=storage/app/import/suppliers.csv}
                            {--branch=3}
                            {--dry-run}';

    protected $description = 'Import supplier CSV ke database ATK berdasarkan cabang';

    public function handle()
    {
        $file = base_path($this->argument('file'));
        $branchId = (int) $this->option('branch');
        $dryRun = $this->option('dry-run');

        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        if ($branchId <= 0 || !DB::table('branches')->where('id', $branchId)->exists()) {
            $this->error("Cabang ID {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('File CSV tidak dapat dibuka.');
            return self::FAILURE;
        }

        $header = fgetcsv($handle, 0, ',');

        if (!$header) {
            fclose($handle);
            $this->error('Header CSV tidak ditemukan.');
            return self::FAILURE;
        }

        // Bersihkan BOM UTF-8 jika ada
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $expected = [
            'kode_supplier',
            'nama_supplier',
            'alamat',
            'no_telepon',
        ];

        if ($header !== $expected) {
            fclose($handle);
            $this->error('Format header CSV tidak sesuai.');
            $this->line('Header yang dibutuhkan: ' . implode(',', $expected));
            return self::FAILURE;
        }

        $inserted = 0;
        $skipped = 0;
        $invalid = 0;
        $line = 1;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $line++;

            if (count($row) !== 4) {
                $invalid++;
                $this->warn("Baris {$line}: jumlah kolom tidak sesuai.");
                continue;
            }

            $kode = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');
            $alamat = trim($row[2] ?? '');
            $telepon = trim($row[3] ?? '');

            if ($kode === '' || $nama === '') {
                $invalid++;
                $this->warn("Baris {$line}: kode atau nama supplier kosong.");
                continue;
            }

            // Periksa duplikasi dalam cabang tujuan
            $exists = DB::table('suppliers')
                ->where('branch_id', $branchId)
                ->where('kode_supplier', $kode)
                ->exists();

            if ($exists) {
                $skipped++;
                $this->warn("Baris {$line}: kode {$kode} sudah ada di cabang {$branchId}.");
                continue;
            }

            if ($dryRun) {
                $inserted++;
                continue;
            }

            try {
                DB::table('suppliers')->insert([
                    'kode_supplier' => $kode,
                    'nama_supplier' => $nama,
                    'alamat' => $alamat !== '' ? $alamat : null,
                    'no_telepon' => $telepon !== '' ? $telepon : null,
                    'email' => null,
                    'branch_id' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $inserted++;

            } catch (QueryException $e) {
                // SQLSTATE 23000 biasanya menunjukkan pelanggaran constraint,
                // misalnya kode supplier unik yang sudah digunakan.
                if ($e->getCode() === '23000') {
                    $skipped++;
                    $this->warn("Baris {$line}: kode {$kode} bertabrakan dengan constraint database.");
                    continue;
                }

                fclose($handle);
                throw $e;
            }
        }

        fclose($handle);

        $this->newLine();

        $this->info($dryRun ? 'SIMULASI IMPORT SELESAI' : 'IMPORT SELESAI');
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Berhasil / siap diimport', $inserted],
                ['Dilewati', $skipped],
                ['Data tidak valid', $invalid],
            ]
        );

        if ($dryRun) {
            $this->comment('Ini hanya simulasi. Belum ada data yang dimasukkan ke database.');
        }

        return self::SUCCESS;
    }
}
