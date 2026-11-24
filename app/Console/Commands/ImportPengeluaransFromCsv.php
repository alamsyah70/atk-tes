<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Throwable;

class ImportPengeluaransFromCsv extends Command
{
    protected $signature = 'atk:import-pengeluarans
        {file=D:\IT\App\migrasi\pengeluarans.csv}
        {--branch=}
        {--user=}
        {--dry-run}';

    protected $description = 'Import data pengeluaran dari CSV hasil konversi DBF';

    public function handle()
    {
        $file = $this->argument('file');

        if (!is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

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
            'BRANCH_ID',
            'USER_ID',
            'TANGGAL',
            'NOMOR_PENG',
            'NAMA_PENGA',
            'DIVISI',
        ];

        foreach ($required as $column) {
            if (!in_array($column, $headers)) {
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

        $dryRun = $this->option('dry-run');
        $branchOverride = $this->option('branch');
        $userOverride = $this->option('user');

        $success = 0;
        $duplicate = 0;
        $invalid = 0;

        try {
            DB::transaction(function () use (
                $rows,
                $branchOverride,
                $userOverride,
                $dryRun,
                &$success,
                &$duplicate,
                &$invalid
            ) {
                foreach ($rows as $row) {
                    $nomor = trim($row['NOMOR_PENG'] ?? '');

                    $branchId = $branchOverride !== null
                        ? (int) $branchOverride
                        : (int) ($row['BRANCH_ID'] ?? 0);

                    $userId = $userOverride !== null
                        ? (int) $userOverride
                        : (int) ($row['USER_ID'] ?? 0);

                    if (!$nomor || !$branchId || !$userId) {
                        $invalid++;
                        continue;
                    }

                    if (!DB::table('branches')->where('id', $branchId)->exists()) {
                        $invalid++;
                        continue;
                    }

                    if (!DB::table('users')->where('id', $userId)->exists()) {
                        $invalid++;
                        continue;
                    }

                    $exists = DB::table('pengeluarans')
                        ->where('branch_id', $branchId)
                        ->where('nomor_pengeluaran', $nomor)
                        ->exists();

                    if ($exists) {
                        $duplicate++;
                        continue;
                    }

                    try {
                        $tanggal = Carbon::parse(trim($row['TANGGAL']))->format('Y-m-d');
                    } catch (Throwable $e) {
                        $invalid++;
                        continue;
                    }

                    $data = [
                        'branch_id'         => $branchId,
                        'user_id'           => $userId,
                        'nomor_pengeluaran' => $nomor,
                        'tanggal'           => $tanggal,
                    
                        'nama_pengambil' => trim($row['NAMA_PENGA'] ?? '')
                            ?: 'TIDAK DIKETAHUI',
                    
                        'divisi' => trim($row['DIVISI'] ?? '')
                            ?: 'LAINNYA',
                    
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('pengeluarans', 'keterangan')) {
                        $data['keterangan'] = null;
                    }

                    if (Schema::hasColumn('pengeluarans', 'created_at')
                        && !empty($row['CREATED_AT'])) {
                        try {
                            $data['created_at'] = Carbon::createFromFormat(
                                'd/m/Y:H:i',
                                trim($row['CREATED_AT'])
                            );
                        } catch (Throwable $e) {
                            // Gunakan waktu sekarang jika format lama tidak terbaca.
                        }
                    }

                    if (!$dryRun) {
                        DB::table('pengeluarans')->insert($data);
                    }

                    $success++;
                }
            });

            $this->newLine();
            $this->info('HASIL IMPORT PENGELUARAN');
            $this->table(
                ['Keterangan', 'Jumlah'],
                [
                    ['Siap diimport / berhasil', $success],
                    ['Duplikat', $duplicate],
                    ['Data tidak valid (termasuk user/cabang tidak ditemukan)', $invalid],
                    ['Total diperiksa', count($rows)],
                ]
            );

            if ($dryRun) {
                $this->warn('Simulasi selesai. Belum ada data disimpan.');
            } else {
                $this->info('Import pengeluaran selesai.');
            }

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error('Import gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
