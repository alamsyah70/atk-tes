<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditAveragePerolehan extends Command
{
    protected $signature = 'atk:audit-average-perolehan
                            {--branch= : Filter ID cabang}
                            {--code= : Filter kode barang}';

    protected $description =
        'Audit moving average barang berdasarkan histori transaksi tanpa mengubah database';

    public function handle(): int
    {
        $branchFilter = $this->option('branch');
        $codeFilter = $this->option('code');

        $query = DB::table('barangs')
            ->orderBy('branch_id')
            ->orderBy('kode_barang');

        if ($branchFilter !== null) {
            $query->where('branch_id', $branchFilter);
        }

        if ($codeFilter !== null) {
            $query->where('kode_barang', $codeFilter);
        }

        $barangs = $query->get();

        if ($barangs->isEmpty()) {
            $this->warn('Master barang tidak ditemukan.');
            return self::SUCCESS;
        }

        $hasil = [];

        foreach ($barangs as $barang) {
            $branchId = (int) $barang->branch_id;
            $barangId = (int) $barang->id;
            $kode = $barang->kode_barang;

            $events = [];

            // =====================================
            // 1. TRANSAKSI PENERIMAAN
            // =====================================

            $penerimaan = DB::table('detail_penerimaans as d')
                ->join(
                    'penerimaans as h',
                    'd.penerimaan_id',
                    '=',
                    'h.id'
                )
                ->where('h.branch_id', $branchId)
                ->where(function ($q) use ($barangId, $kode) {
                    $q->where('d.barang_id', $barangId)
                        ->orWhere(function ($legacy) use ($kode) {
                            $legacy->whereNull('d.barang_id')
                                ->where('d.kode_barang', $kode);
                        });
                })
                ->select(
                    'd.id as detail_id',
                    'h.id as transaksi_id',
                    'h.tanggal',
                    'h.created_at',
                    'd.qty',
                    'd.harga'
                )
                ->get();

            foreach ($penerimaan as $row) {
                $events[] = [
                    'jenis' => 'PENERIMAAN',
                    'tanggal' => $row->tanggal,
                    'created_at' => $row->created_at,
                    'transaksi_id' => $row->transaksi_id,
                    'detail_id' => $row->detail_id,
                    'qty' => (int) $row->qty,
                    'harga' => (float) $row->harga,
                ];
            }

            // =====================================
            // 2. TRANSAKSI PENGELUARAN
            // =====================================

            $pengeluaran = DB::table('detail_pengeluarans as d')
                ->join(
                    'pengeluarans as h',
                    'd.pengeluaran_id',
                    '=',
                    'h.id'
                )
                ->where('h.branch_id', $branchId)
                ->where(function ($q) use ($barangId, $kode) {
                    $q->where('d.barang_id', $barangId)
                        ->orWhere(function ($legacy) use ($kode) {
                            $legacy->whereNull('d.barang_id')
                                ->where('d.kode_barang', $kode);
                        });
                })
                ->select(
                    'd.id as detail_id',
                    'h.id as transaksi_id',
                    'h.tanggal',
                    'h.created_at',
                    'd.qty'
                )
                ->get();

            foreach ($pengeluaran as $row) {
                $events[] = [
                    'jenis' => 'PENGELUARAN',
                    'tanggal' => $row->tanggal,
                    'created_at' => $row->created_at,
                    'transaksi_id' => $row->transaksi_id,
                    'detail_id' => $row->detail_id,
                    'qty' => (int) $row->qty,
                    'harga' => 0,
                ];
            }

            // =====================================
            // 3. PENYESUAIAN STOK
            // =====================================

            $penyesuaian = DB::table('penyesuaian_stoks')
                ->where('branch_id', $branchId)
                ->where(function ($q) use ($barangId, $kode) {
                    $q->where('barang_id', $barangId)
                        ->orWhere(function ($legacy) use ($kode) {
                            $legacy->whereNull('barang_id')
                                ->where('kode_barang', $kode);
                        });
                })
                ->get();

            foreach ($penyesuaian as $row) {
                $events[] = [
                    'jenis' => 'PENYESUAIAN',
                    'tanggal' => $row->tanggal,
                    'created_at' => $row->created_at,
                    'transaksi_id' => $row->id,
                    'detail_id' => $row->id,
                    'qty' => (int) $row->qty_koreksi,
                    'harga' => 0,
                ];
            }

            // =====================================
            // 4. URUTKAN HISTORI
            // =====================================

            usort($events, function ($a, $b) {
                $dateCompare = strcmp(
                    (string) $a['tanggal'],
                    (string) $b['tanggal']
                );

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                $timeCompare = strcmp(
                    (string) ($a['created_at'] ?? ''),
                    (string) ($b['created_at'] ?? '')
                );

                if ($timeCompare !== 0) {
                    return $timeCompare;
                }

                $idCompare = $a['transaksi_id']
                    <=> $b['transaksi_id'];

                if ($idCompare !== 0) {
                    return $idCompare;
                }

                return $a['detail_id'] <=> $b['detail_id'];
            });

            // =====================================
            // 5. HITUNG MOVING AVERAGE
            // =====================================

            $stok = 0;
            $average = 0;
            $valid = true;
            $catatan = [];

            foreach ($events as $event) {
                $qty = $event['qty'];

                if (
                    ($event['jenis'] !== 'PENYESUAIAN' && $qty <= 0) ||
                    ($event['jenis'] === 'PENYESUAIAN' && $qty === 0)
                ) {
                    $valid = false;
                    $catatan[] = 'Qty transaksi tidak valid';
                    continue;
                }

                if ($event['jenis'] === 'PENERIMAAN') {
                    $harga = $event['harga'];

                    if ($harga < 0) {
                        $valid = false;
                        $catatan[] = 'Harga penerimaan negatif';
                        continue;
                    }

                    if ($stok < 0) {
                        $valid = false;
                        $catatan[] = 'Ada stok negatif sebelum penerimaan';
                        continue;
                    }

                    if ($stok === 0) {
                        $average = $harga;
                    } elseif ($average <= 0) {
                        $valid = false;
                        $catatan[] = 'Average lama tidak tersedia';
                        continue;
                    } else {
                        $average = (
                            ($stok * $average) +
                            ($qty * $harga)
                        ) / ($stok + $qty);
                    }

                    $stok += $qty;
                }

                elseif ($event['jenis'] === 'PENGELUARAN') {
                    if ($qty > $stok) {
                        $valid = false;
                        $catatan[] =
                            'Pengeluaran melebihi stok pada ' .
                            $event['tanggal'];
                    }

                    $stok -= $qty;
                }

                elseif ($event['jenis'] === 'PENYESUAIAN') {
                    $stokSebelum = $stok;
                    $stok += $qty;

                    // qty_koreksi positif atau negatif
                    // Average tidak diubah.
                    // Jika stok menjadi positif tanpa average,
                    // hasil audit tidak dapat dianggap valid.
                    if (
                        $stokSebelum === 0 &&
                        $stok > 0 &&
                        $average <= 0
                    ) {
                        $valid = false;
                        $catatan[] =
                            'Penyesuaian positif tanpa average';
                    }

                    if ($stok < 0) {
                        $valid = false;
                        $catatan[] =
                            'Penyesuaian menyebabkan stok negatif';
                    }
                }
            }

            $averageMaster = (float) $barang->average_perolehan;

            $selisih = $average - $averageMaster;

            $hasil[] = [
                $barang->branch_id,
                $barang->kode_barang,
                number_format($stok, 0, ',', '.'),
                number_format($averageMaster, 2, ',', '.'),
                $valid
                    ? number_format($average, 4, ',', '.')
                    : 'TIDAK VALID',
                $valid
                    ? number_format($selisih, 4, ',', '.')
                    : '-',
                $catatan
                    ? implode('; ', array_unique($catatan))
                    : 'OK',
            ];
        }

        $this->info('AUDIT MOVING AVERAGE PEROLEHAN');
        $this->warn('MODE BACA SAJA - DATABASE TIDAK DIUBAH');

        $this->table(
            [
                'Cabang',
                'Kode',
                'Stok Audit',
                'Average Master',
                'Average Audit',
                'Selisih',
                'Catatan',
            ],
            $hasil
        );

        $this->info('Jumlah barang diperiksa: ' . count($hasil));

        return self::SUCCESS;
    }
}
