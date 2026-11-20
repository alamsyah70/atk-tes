<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
        ]);
    }

    private function branchId()
    {
        $branchId = auth()->user()->branch_id;

        abort_if(!$branchId, 403, 'User belum memiliki cabang.');

        return $branchId;
    }

    private function supplierList()
    {
        $query = Supplier::query()->orderBy('nama_supplier');

        if (!$this->isGlobalUser()) {
            $query->where('branch_id', $this->branchId());
        }

        return $query->get();
    }

    private function validateFilter(Request $request): array
    {
        return $request->validate([
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'divisi' => ['nullable', 'string', 'max:150'],
            'nama_pengambil' => ['nullable', 'string', 'max:150'],
            'q' => ['nullable', 'string', 'max:150'],
        ]);
    }

    /**
     * QUERY LAPORAN PENERIMAAN
     * Satu baris untuk setiap detail barang yang diterima.
     */
    private function queryPenerimaan(Request $request)
    {
        $query = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->leftJoin('suppliers as s', 'p.supplier_id', '=', 's.id')
            ->leftJoin('branches as b', 'p.branch_id', '=', 'b.id')
            ->select([
                'dp.id as detail_id',
                'p.id as penerimaan_id',
                'p.tanggal',
                'p.keterangan',
                'p.branch_id',
                's.nama_supplier',
                'b.nama_cabang',
                'dp.kode_barang',
                'dp.nama_barang',
                'dp.kategori',
                'dp.satuan',
                'dp.qty',
                'dp.harga',
                'dp.jumlah',
            ]);

        if (!$this->isGlobalUser()) {
            $query->where('p.branch_id', $this->branchId());
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('p.tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('p.tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('supplier_id')) {
            $query->where('p.supplier_id', $request->supplier_id);
        }

        if ($request->filled('q')) {
            $keyword = $request->q;

            $query->where(function ($q) use ($keyword) {
                $q->where('dp.kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('dp.nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('s.nama_supplier', 'like', "%{$keyword}%")
                    ->orWhere('p.keterangan', 'like', "%{$keyword}%");
            });
        }

        return $query->orderByDesc('p.tanggal')
            ->orderByDesc('p.id')
            ->orderBy('dp.id');
    }

    /**
     * HALAMAN LAPORAN PENERIMAAN.
     */
    public function penerimaan(Request $request)
    {
        $this->validateFilter($request);

        $data = $this->queryPenerimaan($request)
            ->paginate(10)
            ->withQueryString();

        return view('laporan.penerimaan', [
            'data' => $data,
            'suppliers' => $this->supplierList(),
        ]);
    }

    /**
     * PDF LAPORAN PENERIMAAN.
     */
    public function penerimaanPdf(Request $request)
    {
        $this->validateFilter($request);

        $data = $this->queryPenerimaan($request)->get();

        $pdf = Pdf::loadView('laporan.penerimaan-pdf', [
            'data' => $data,
            'filter' => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penerimaan.pdf');
    }

    /**
     * QUERY LAPORAN PENGELUARAN
     * Satu baris untuk setiap detail barang yang dikeluarkan.
     */
    private function queryPengeluaran(Request $request)
    {
        $query = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->leftJoin('branches as b', 'pg.branch_id', '=', 'b.id')
            ->select([
                'dp.id as detail_id',
                'pg.id as pengeluaran_id',
                'pg.nomor_pengeluaran',
                'pg.tanggal',
                'pg.divisi',
                'pg.nama_pengambil',
                'pg.keterangan',
                'pg.branch_id',
                'b.nama_cabang',
                'dp.kode_barang',
                'dp.nama_barang',
                'dp.kategori',
                'dp.satuan',
                'dp.lokasi',
                'dp.qty',
                'dp.harga_pokok',
                'dp.jumlah_harga_pokok',
            ]);

        if (!$this->isGlobalUser()) {
            $query->where('pg.branch_id', $this->branchId());
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('pg.tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('pg.tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('divisi')) {
            $query->where('pg.divisi', 'like', '%' . $request->divisi . '%');
        }

        if ($request->filled('nama_pengambil')) {
            $query->where('pg.nama_pengambil', 'like', '%' . $request->nama_pengambil . '%');
        }

        if ($request->filled('q')) {
            $keyword = $request->q;

            $query->where(function ($q) use ($keyword) {
                $q->where('dp.kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('dp.nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('pg.nama_pengambil', 'like', "%{$keyword}%")
                    ->orWhere('pg.divisi', 'like', "%{$keyword}%")
                    ->orWhere('pg.nomor_pengeluaran', 'like', "%{$keyword}%")
                    ->orWhere('pg.keterangan', 'like', "%{$keyword}%");
            });
        }

        return $query->orderByDesc('pg.tanggal')
            ->orderByDesc('pg.id')
            ->orderBy('dp.id');
    }

    /**
     * HALAMAN LAPORAN PENGELUARAN.
     */
    public function pengeluaran(Request $request)
    {
        $this->validateFilter($request);

        $query = $this->queryPengeluaran($request);

        // Query total dibuat terpisah agar tidak bercampur
        // dengan SELECT kolom detail barang.
        $totalHargaPokok = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->when(!$this->isGlobalUser(), function ($q) {
                $q->where('pg.branch_id', $this->branchId());
            })
            ->when($request->filled('tanggal_dari'), function ($q) use ($request) {
                $q->whereDate('pg.tanggal', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function ($q) use ($request) {
                $q->whereDate('pg.tanggal', '<=', $request->tanggal_sampai);
            })
            ->when($request->filled('divisi'), function ($q) use ($request) {
                $q->where('pg.divisi', 'like', '%' . $request->divisi . '%');
            })
            ->when($request->filled('nama_pengambil'), function ($q) use ($request) {
                $q->where('pg.nama_pengambil', 'like', '%' . $request->nama_pengambil . '%');
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = $request->q;

                $q->where(function ($sub) use ($keyword) {
                    $sub->where('dp.kode_barang', 'like', "%{$keyword}%")
                        ->orWhere('dp.nama_barang', 'like', "%{$keyword}%")
                        ->orWhere('pg.nama_pengambil', 'like', "%{$keyword}%")
                        ->orWhere('pg.divisi', 'like', "%{$keyword}%")
                        ->orWhere('pg.nomor_pengeluaran', 'like', "%{$keyword}%")
                        ->orWhere('pg.keterangan', 'like', "%{$keyword}%");
                });
            })
            ->sum('dp.jumlah_harga_pokok');

        $data = $query->paginate(10)->withQueryString();

        return view('laporan.pengeluaran', [
            'data' => $data,
            'totalHargaPokok' => $totalHargaPokok,
        ]);
    }

    /**
     * PDF LAPORAN PENGELUARAN.
     */
    public function pengeluaranPdf(Request $request)
    {
        $this->validateFilter($request);

        $query = $this->queryPengeluaran($request);

        $totalHargaPokok = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->when(!$this->isGlobalUser(), function ($q) {
                $q->where('pg.branch_id', $this->branchId());
            })
            ->when($request->filled('tanggal_dari'), function ($q) use ($request) {
                $q->whereDate('pg.tanggal', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function ($q) use ($request) {
                $q->whereDate('pg.tanggal', '<=', $request->tanggal_sampai);
            })
            ->when($request->filled('divisi'), function ($q) use ($request) {
                $q->where('pg.divisi', 'like', '%' . $request->divisi . '%');
            })
            ->when($request->filled('nama_pengambil'), function ($q) use ($request) {
                $q->where('pg.nama_pengambil', 'like', '%' . $request->nama_pengambil . '%');
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = $request->q;

                $q->where(function ($sub) use ($keyword) {
                    $sub->where('dp.kode_barang', 'like', "%{$keyword}%")
                        ->orWhere('dp.nama_barang', 'like', "%{$keyword}%")
                        ->orWhere('pg.nama_pengambil', 'like', "%{$keyword}%")
                        ->orWhere('pg.divisi', 'like', "%{$keyword}%")
                        ->orWhere('pg.nomor_pengeluaran', 'like', "%{$keyword}%")
                        ->orWhere('pg.keterangan', 'like', "%{$keyword}%");
                });
            })
            ->sum('dp.jumlah_harga_pokok');

        $data = $query->get();

        $pdf = Pdf::loadView('laporan.pengeluaran-pdf', [
            'data' => $data,
            'filter' => $request,
            'totalHargaPokok' => $totalHargaPokok,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-pengeluaran.pdf');
    }

    /**
 * QUERY LAPORAN PENYESUAIAN STOK
 * Satu baris untuk setiap transaksi penyesuaian.
 */
private function queryPenyesuaian(Request $request)
{
    $query = DB::table('penyesuaian_stoks as ps')
        ->leftJoin('branches as b', 'ps.branch_id', '=', 'b.id')
        ->select([
            'ps.id',
            'ps.nomor_penyesuaian',
            'ps.tanggal',
            'ps.branch_id',
            'ps.kode_barang',
            'ps.nama_barang',
            'ps.qty_koreksi',
            'ps.alasan',
            'b.nama_cabang',
        ]);

    // User cabang hanya melihat transaksi cabangnya sendiri.
    if (!$this->isGlobalUser()) {
        $query->where('ps.branch_id', $this->branchId());
    }

    // Filter cabang hanya untuk user global.
    if ($this->isGlobalUser() && $request->filled('branch_id')) {
        $query->where('ps.branch_id', $request->branch_id);
    }

    if ($request->filled('tanggal_dari')) {
        $query->whereDate('ps.tanggal', '>=', $request->tanggal_dari);
    }

    if ($request->filled('tanggal_sampai')) {
        $query->whereDate('ps.tanggal', '<=', $request->tanggal_sampai);
    }

    if ($request->filled('q')) {
        $keyword = $request->q;

        $query->where(function ($q) use ($keyword) {
            $q->where('ps.nomor_penyesuaian', 'like', "%{$keyword}%")
                ->orWhere('ps.kode_barang', 'like', "%{$keyword}%")
                ->orWhere('ps.nama_barang', 'like', "%{$keyword}%")
                ->orWhere('ps.alasan', 'like', "%{$keyword}%")
                ->orWhere('b.nama_cabang', 'like', "%{$keyword}%");
        });
    }

    return $query
        ->orderByDesc('ps.tanggal')
        ->orderByDesc('ps.id');
}

/**
 * HALAMAN LAPORAN PENYESUAIAN STOK.
 */
public function penyesuaian(Request $request)
{
    $request->validate([
        'tanggal_dari' => ['nullable', 'date'],
        'tanggal_sampai' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_dari',
        ],
        'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        'q' => ['nullable', 'string', 'max:150'],
    ]);

    $data = $this->queryPenyesuaian($request)
        ->paginate(10)
        ->withQueryString();

    // Ringkasan jumlah penambahan dan pengurangan.
    $ringkasanQuery = DB::table('penyesuaian_stoks as ps')
        ->when(!$this->isGlobalUser(), function ($q) {
            $q->where('ps.branch_id', $this->branchId());
        })
        ->when($this->isGlobalUser() && $request->filled('branch_id'), function ($q) use ($request) {
            $q->where('ps.branch_id', $request->branch_id);
        })
        ->when($request->filled('tanggal_dari'), function ($q) use ($request) {
            $q->whereDate('ps.tanggal', '>=', $request->tanggal_dari);
        })
        ->when($request->filled('tanggal_sampai'), function ($q) use ($request) {
            $q->whereDate('ps.tanggal', '<=', $request->tanggal_sampai);
        })
        ->when($request->filled('q'), function ($q) use ($request) {
            $keyword = $request->q;

            $q->where(function ($sub) use ($keyword) {
                $sub->where('ps.nomor_penyesuaian', 'like', "%{$keyword}%")
                    ->orWhere('ps.kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('ps.nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('ps.alasan', 'like', "%{$keyword}%");
            });
        });

    $ringkasan = (clone $ringkasanQuery)
        ->selectRaw("
            COUNT(*) as total_transaksi,
            COALESCE(SUM(CASE WHEN qty_koreksi > 0 THEN qty_koreksi ELSE 0 END), 0) as total_penambahan,
            COALESCE(SUM(CASE WHEN qty_koreksi < 0 THEN ABS(qty_koreksi) ELSE 0 END), 0) as total_pengurangan
        ")
        ->first();

    return view('laporan.penyesuaian', [
        'data' => $data,
        'branches' => $this->isGlobalUser()
            ? \App\Models\Branch::orderBy('nama_cabang')->get()
            : collect(),
        'isGlobalUser' => $this->isGlobalUser(),
        'ringkasan' => $ringkasan,
    ]);
    }

    private function ringkasanPenyesuaian(Request $request)
    {
        $query = DB::table('penyesuaian_stoks as ps')
            ->when(!$this->isGlobalUser(), function ($q) {
                $q->where('ps.branch_id', $this->branchId());
            })
            ->when($this->isGlobalUser() && $request->filled('branch_id'), function ($q) use ($request) {
                $q->where('ps.branch_id', $request->branch_id);
            })
            ->when($request->filled('tanggal_dari'), function ($q) use ($request) {
                $q->whereDate('ps.tanggal', '>=', $request->tanggal_dari);
            })
            ->when($request->filled('tanggal_sampai'), function ($q) use ($request) {
                $q->whereDate('ps.tanggal', '<=', $request->tanggal_sampai);
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = $request->q;

                $q->where(function ($sub) use ($keyword) {
                    $sub->where('ps.nomor_penyesuaian', 'like', "%{$keyword}%")
                        ->orWhere('ps.kode_barang', 'like', "%{$keyword}%")
                        ->orWhere('ps.nama_barang', 'like', "%{$keyword}%")
                        ->orWhere('ps.alasan', 'like', "%{$keyword}%");
                });
            });

        return $query->selectRaw("
            COUNT(*) as total_transaksi,
            COALESCE(SUM(CASE WHEN ps.qty_koreksi > 0 THEN ps.qty_koreksi ELSE 0 END), 0) as total_penambahan,
            COALESCE(SUM(CASE WHEN ps.qty_koreksi < 0 THEN ABS(ps.qty_koreksi) ELSE 0 END), 0) as total_pengurangan
        ")->first();
    }
    /**
     * PDF LAPORAN PENYESUAIAN STOK.
     */
    public function penyesuaianPdf(Request $request)
    {
        $request->validate([
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_dari',
            ],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'q' => ['nullable', 'string', 'max:150'],
        ]);

        // Ambil seluruh data detail untuk PDF.
        $data = $this->queryPenyesuaian($request)->get();

        // Ambil ringkasan menggunakan query agregasi terpisah.
        $ringkasan = $this->ringkasanPenyesuaian($request);

        $pdf = Pdf::loadView('laporan.penyesuaian-pdf', [
            'data' => $data,
            'filter' => $request,
            'ringkasan' => $ringkasan,
            'isGlobalUser' => $this->isGlobalUser(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penyesuaian-stok.pdf');
    }
}
