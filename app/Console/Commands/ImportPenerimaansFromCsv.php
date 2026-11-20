<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ImportPenerimaansFromCsv extends Command
{
    protected $signature = 'atk:import-penerimaans
                            {file=D:\IT\App\Data\penerimaans.csv : Lokasi file CSV}
                            {--branch= : ID cabang tujuan}
                            {--dry-run : Simulasi tanpa menyimpan data}';

    protected $description = 'Import data penerimaan dari CSV hasil konversi DBF';

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
            $this->error("Cabang {$branchId} tidak ditemukan.");
            return self::FAILURE;
        }

        foreach (['branch_id', 'supplier_id', 'tanggal', 'nomor_penerimaan'] as $column) {
            if (!Schema::hasColumn('penerimaans', $column)) {
                $this->error("Kolom penerimaans.{$column} tidak ditemukan.");
                return self::FAILURE;
            }
        }

        foreach (['id', 'kode_supplier', 'branch_id'] as $column) {
            if (!Schema::hasColumn('suppliers', $column)) {
                $this->error("Kolom suppliers.{$column} tidak ditemukan.");
                return self::FAILURE;
            }
        }

        $handle = fopen($file, 'r');

        if (!$handle) {
            $this->error('File CSV tidak dapat dibuka.');
            return self::FAILURE;
        }

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
         * PEMETAAN KOLOM DBF
         *
         * Sesuaikan daftar kandidat jika nama header DBF berbeda.
         */
        $mapping = [
            'kode_supplier' => [
                'kode_supplier',
                'supplier_id',
                'supplier_i',
                'supplier',
                'sup',
                'kode_sup',
                'kd_supplier',
                'kd_sup',
            ],
        
            'nomor_penerimaan' => [
                'nomor_penerimaan',
                'nomor_pene',
                'no_penerimaan',
                'no_terima',
                'noterima',
                'no_trm',
                'notrm',
                'nomor',
                'no_bukti',
            ],
        
            'tanggal' => [
                'tanggal',
                'tgl',
                'tgl_terima',
                'tanggal_terima',
                'tgl_penerimaan',
            ],
        
            'keterangan' => [
                'keterangan',
                'ket',
                'catatan',
                'uraian',
            ],
        ];

        $columnIndex = [];

        foreach ($mapping as $target => $candidates) {
            foreach ($candidates as $candidate) {
                $index = array_search($candidate, $headers, true);

                if ($index !== false) {
                    $columnIndex[$target] = $index;
                    break;
                }
            }
        }

        $requiredMappings = [
            'kode_supplier',
            'nomor_penerimaan',
            'tanggal',
        ];

        foreach ($requiredMappings as $field) {
            if (!isset($columnIndex[$field])) {
                fclose($handle);

                $this->error("Kolom {$field} tidak ditemukan dalam CSV.");
                $this->line('Header yang ditemukan: ' . implode(', ', $headers));

                return self::FAILURE;
            }
        }

        $hasKeterangan = Schema::hasColumn('penerimaans', 'keterangan');
        $hasCreatedAt = Schema::hasColumn('penerimaans', 'created_at');
        $hasUpdatedAt = Schema::hasColumn('penerimaans', 'updated_at');

        $inserted = 0;
        $duplicate = 0;
        $invalid = 0;
        $supplierNotFound = 0;
        $rowNumber = 1;

        $seenNumbers = [];

        $this->info('Memulai pemeriksaan penerimaan...');
        $this->info("Cabang tujuan: {$branchId}");

        if ($dryRun) {
            $this->warn('DRY RUN: tidak ada data yang disimpan.');
        }

        try {
            DB::beginTransaction();

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowNumber++;

                $getValue = function ($field) use ($columnIndex, $row) {
                    if (!isset($columnIndex[$field])) {
                        return '';
                    }

                    return trim((string) ($row[$columnIndex[$field]] ?? ''));
                };

                $kodeSupplier = $getValue('kode_supplier');
                $nomor = $getValue('nomor_penerimaan');
                $tanggalRaw = $getValue('tanggal');
                $keterangan = $getValue('keterangan');

                if ($kodeSupplier === '' || $nomor === '' || $tanggalRaw === '') {
                    $invalid++;
                    continue;
                }

                // Konversi tanggal
                try {
                    $tanggal = $this->parseTanggal($tanggalRaw);
                } catch (\Throwable $e) {
                    $this->warn("Baris {$rowNumber}: tanggal tidak valid ({$tanggalRaw}).");
                    $invalid++;
                    continue;
                }

                // Cari supplier pada cabang tujuan
                $supplier = DB::table('suppliers')
                    ->where('branch_id', $branchId)
                    ->where('kode_supplier', $kodeSupplier)
                    ->first();

                if (!$supplier) {
                    $supplierNotFound++;
                    continue;
                }

                // Cegah duplikasi dalam CSV yang sama
                $uniqueKey = $branchId . '|' . $nomor;

                if (isset($seenNumbers[$uniqueKey])) {
                    $duplicate++;
                    continue;
                }

                $seenNumbers[$uniqueKey] = true;

                // Cegah duplikasi dengan data database
                $exists = DB::table('penerimaans')
                    ->where('branch_id', $branchId)
                    ->where('nomor_penerimaan', $nomor)
                    ->exists();

                if ($exists) {
                    $duplicate++;
                    continue;
                }

                $data = [
                    'branch_id' => $branchId,
                    'supplier_id' => $supplier->id,
                    'tanggal' => $tanggal,
                    'nomor_penerimaan' => $nomor,
                ];

                if ($hasKeterangan) {
                    $data['keterangan'] = $keterangan ?: null;
                }

                if ($hasCreatedAt) {
                    $data['created_at'] = now();
                }

                if ($hasUpdatedAt) {
                    $data['updated_at'] = now();
                }

                if (!$dryRun) {
                    DB::table('penerimaans')->insert($data);
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

        $this->info('HASIL IMPORT PENERIMAAN');

        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Siap diimport / berhasil', $inserted],
                ['Duplikat', $duplicate],
                ['Supplier tidak ditemukan', $supplierNotFound],
                ['Data tidak valid', $invalid],
                ['Total diperiksa', $inserted + $duplicate + $supplierNotFound + $invalid],
            ]
        );

        if ($dryRun) {
            $this->warn('Simulasi selesai. Belum ada data yang disimpan.');
        } else {
            $this->info('Import penerimaan selesai.');
        }

        return self::SUCCESS;
    }

    private function parseTanggal(string $value): string
{
    $value = trim($value);

    $formats = [
        'd/m/Y:H:i',
        'd/m/Y H:i',
        'd/m/Y',
        'd-m-Y',
        'Y-m-d H:i:s',
        'Y-m-d',
        'm/d/Y',
        'd/m/y',
        'd-m-y',
    ];

    foreach ($formats as $format) {
        try {
            $date = Carbon::createFromFormat('!' . $format, $value);

            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            // Coba format berikutnya
        }
    }

    throw new \InvalidArgumentException(
        "Format tanggal tidak dikenali: {$value}"
    );
}
}
