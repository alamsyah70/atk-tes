<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class StockCardController extends Controller
{
    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
            'IT Cabang',
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isGlobal = $this->isGlobalUser();

        // =========================================
        // 1. CABANG DAN HAK AKSES
        // =========================================

        $branches = $isGlobal
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        if (!$isGlobal) {
            abort_if(
                !$user->branch_id,
                403,
                'User belum memiliki cabang.'
            );

            $branchId = (int) $user->branch_id;
        } else {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);

            $branchId = $request->filled('branch_id')
                ? (int) $request->branch_id
                : null;
        }

        // =========================================
        // 2. FILTER TANGGAL
        // =========================================

        $request->validate([
            'barang_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input(
            'start_date',
            Carbon::now()->startOfMonth()->toDateString()
        );

        $endDate = $request->input(
            'end_date',
            Carbon::now()->toDateString()
        );

        // =========================================
        // 3. MASTER BARANG SESUAI CABANG
        // =========================================

        $barangs = collect();
        $barang = null;
        $movements = collect();

        $openingBalance = 0;
        $closingBalance = 0;

        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalKoreksi = 0;

        if ($branchId) {
            $barangs = Barang::query()
                ->where('branch_id', $branchId)
                ->orderBy('kode_barang')
                ->get();

            if ($request->filled('barang_id')) {
                $barang = Barang::query()
                    ->where('branch_id', $branchId)
                    ->where('id', $request->barang_id)
                    ->firstOrFail();

                // =================================
                // 4. AMBIL TRANSAKSI PENERIMAAN
                // =================================

                $penerimaan = DB::table('detail_penerimaans as d')
                    ->join(
                        'penerimaans as p',
                        'd.penerimaan_id',
                        '=',
                        'p.id'
                    )
                    ->leftJoin(
                        'suppliers as s',
                        'p.supplier_id',
                        '=',
                        's.id'
                    )
                    ->where('p.branch_id', $branchId)
                    ->where(function ($query) use ($barang) {
                        $query->where(
                            'd.barang_id',
                            $barang->id
                        )->orWhere(function ($legacy) use ($barang) {
                            $legacy->whereNull('d.barang_id')
                                ->where(
                                    'd.kode_barang',
                                    $barang->kode_barang
                                );
                        });
                    })
                    ->whereDate('p.tanggal', '<=', $endDate)
                    ->select(
                        'd.id',
                        'p.tanggal',
                        'p.nomor_penerimaan as nomor_transaksi',
                        'p.keterangan',
                        's.nama_supplier',
                        'd.qty'
                    )
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'tanggal' => $row->tanggal,
                            'nomor' => $row->nomor_transaksi,
                            'jenis' => 'Penerimaan',
                            'keterangan' => trim(
                                'Supplier: ' .
                                ($row->nama_supplier ?? '-') .
                                ' | ' .
                                ($row->keterangan ?? '')
                            ),
                            'masuk' => (int) $row->qty,
                            'keluar' => 0,
                            'koreksi' => 0,
                            'urutan' => 1,
                        ];
                    });

                // =================================
                // 5. AMBIL TRANSAKSI PENGELUARAN
                // =================================

                $pengeluaran = DB::table('detail_pengeluarans as d')
                    ->join(
                        'pengeluarans as p',
                        'd.pengeluaran_id',
                        '=',
                        'p.id'
                    )
                    ->where('p.branch_id', $branchId)
                    ->where(function ($query) use ($barang) {
                        $query->where(
                            'd.barang_id',
                            $barang->id
                        )->orWhere(function ($legacy) use ($barang) {
                            $legacy->whereNull('d.barang_id')
                                ->where(
                                    'd.kode_barang',
                                    $barang->kode_barang
                                );
                        });
                    })
                    ->whereDate('p.tanggal', '<=', $endDate)
                    ->select(
                        'd.id',
                        'p.tanggal',
                        'p.nomor_pengeluaran as nomor_transaksi',
                        'p.divisi',
                        'p.nama_pengambil',
                        'p.keterangan',
                        'd.qty'
                    )
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'tanggal' => $row->tanggal,
                            'nomor' => $row->nomor_transaksi,
                            'jenis' => 'Pengeluaran',
                            'keterangan' => trim(
                                'Divisi: ' . $row->divisi .
                                ' | Pengambil: ' . $row->nama_pengambil .
                                ' | ' . ($row->keterangan ?? '')
                            ),
                            'masuk' => 0,
                            'keluar' => (int) $row->qty,
                            'koreksi' => 0,
                            'urutan' => 2,
                        ];
                    });

                // =================================
                // 6. AMBIL PENYESUAIAN STOK
                // =================================

                $penyesuaian = DB::table('penyesuaian_stoks as ps')
                    ->where('ps.branch_id', $branchId)
                    ->where(function ($query) use ($barang) {
                        $query->where(
                            'ps.barang_id',
                            $barang->id
                        )->orWhere(function ($legacy) use ($barang) {
                            $legacy->whereNull('ps.barang_id')
                                ->where(
                                    'ps.kode_barang',
                                    $barang->kode_barang
                                );
                        });
                    })
                    ->whereDate('ps.tanggal', '<=', $endDate)
                    ->select(
                        'ps.id',
                        'ps.tanggal',
                        'ps.nomor_penyesuaian',
                        'ps.qty_koreksi',
                        'ps.alasan'
                    )
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'tanggal' => $row->tanggal,
                            'nomor' => $row->nomor_penyesuaian,
                            'jenis' => 'Penyesuaian',
                            'keterangan' => $row->alasan,
                            'masuk' => 0,
                            'keluar' => 0,
                            'koreksi' => (int) $row->qty_koreksi,
                            'urutan' => 3,
                        ];
                    });

                // =================================
                // 7. GABUNGKAN SEMUA PERGERAKAN
                // =================================

                $allMovements = $penerimaan
                    ->concat($pengeluaran)
                    ->concat($penyesuaian)
                    ->sort(function ($a, $b) {
                        $tanggal = strcmp(
                            $a['tanggal'],
                            $b['tanggal']
                        );

                        if ($tanggal !== 0) {
                            return $tanggal;
                        }

                        if ($a['urutan'] !== $b['urutan']) {
                            return $a['urutan'] <=> $b['urutan'];
                        }

                        return $a['id'] <=> $b['id'];
                    })
                    ->values();

                // =================================
                // 8. HITUNG SALDO AWAL
                // =================================

                foreach ($allMovements as $movement) {
                    if ($movement['tanggal'] < $startDate) {
                        $openingBalance +=
                            $movement['masuk']
                            - $movement['keluar']
                            + $movement['koreksi'];
                    }
                }

                // =================================
                // 9. HITUNG SALDO BERJALAN
                // =================================

                $saldo = $openingBalance;

                $movements = $allMovements
                    ->filter(function ($movement) use (
                        $startDate,
                        $endDate
                    ) {
                        return $movement['tanggal'] >= $startDate
                            && $movement['tanggal'] <= $endDate;
                    })
                    ->map(function ($movement) use (
                        &$saldo,
                        &$totalMasuk,
                        &$totalKeluar,
                        &$totalKoreksi
                    ) {
                        $saldo +=
                            $movement['masuk']
                            - $movement['keluar']
                            + $movement['koreksi'];

                        $totalMasuk += $movement['masuk'];
                        $totalKeluar += $movement['keluar'];
                        $totalKoreksi += $movement['koreksi'];

                        $movement['saldo'] = $saldo;

                        return $movement;
                    })
                    ->values();

                $closingBalance = $saldo;
            }
        }

        return view('stock-card.index', compact(
            'branches',
            'branchId',
            'barangs',
            'barang',
            'startDate',
            'endDate',
            'movements',
            'openingBalance',
            'closingBalance',
            'totalMasuk',
            'totalKeluar',
            'totalKoreksi'
        ));
    }
}
