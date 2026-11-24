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

    /**
 * VALIDASI PERIODE LAPORAN BULANAN.
 */
private function filterPeriodeLaporan(Request $request): array
{
    $validated = $request->validate([
        'bulan' => ['nullable', 'integer', 'between:1,12'],
        'tahun' => ['nullable', 'integer', 'between:2000,2100'],
        'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
    ]);

    return [
        'bulan' => (int) ($validated['bulan'] ?? now()->month),
        'tahun' => (int) ($validated['tahun'] ?? now()->year),
        'branch_id' => $validated['branch_id'] ?? null,
    ];
}

/**
 * LAPORAN PENERIMAAN PER SUPPLIER / KATEGORI.
 * Data dikelompokkan berdasarkan supplier, kemudian kategori.
 */
public function penerimaanSupplierPdf(Request $request)
{
    $filter = $this->filterPeriodeLaporan($request);

    $query = DB::table('detail_penerimaans as dp')
        ->join('penerimaans as p', 'p.id', '=', 'dp.penerimaan_id')
        ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
        ->leftJoin('branches as b', 'b.id', '=', 'p.branch_id')
        ->whereYear('p.tanggal', $filter['tahun'])
        ->whereMonth('p.tanggal', $filter['bulan']);

    if (!$this->isGlobalUser()) {
        $query->where('p.branch_id', $this->branchId());
    } elseif (!empty($filter['branch_id'])) {
        $query->where('p.branch_id', $filter['branch_id']);
    }

    $data = $query->select([
            'p.id as penerimaan_id',
            'p.tanggal',
            'p.nomor_penerimaan',
            'p.branch_id',
            's.id as supplier_id',
            's.nama_supplier',
            'b.nama_cabang',
            'dp.kode_barang',
            'dp.nama_barang',
            'dp.kategori',
            'dp.satuan',
            'dp.qty',
            'dp.harga',
            'dp.jumlah',
        ])
        ->orderBy('s.nama_supplier')
        ->orderBy('dp.kategori')
        ->orderBy('p.tanggal')
        ->orderBy('p.id')
        ->get();

    $groups = $data->groupBy(function ($item) {
        return $item->supplier_id ?? 'tanpa-supplier';
    });

    $totalQty = (float) $data->sum('qty');
    $totalNilai = (float) $data->sum('jumlah');

    $pdf = Pdf::loadView('laporan.penerimaan-supplier-pdf', [
        'groups' => $groups,
        'filter' => $filter,
        'totalQty' => $totalQty,
        'totalNilai' => $totalNilai,
        'isGlobalUser' => $this->isGlobalUser(),
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('laporan-penerimaan-per-supplier.pdf');
}

/**
 * LAPORAN PEMAKAIAN PER DIVISI / KATEGORI.
 * Data dikelompokkan berdasarkan divisi, kemudian kategori.
 */
public function pemakaianDivisiPdf(Request $request)
{
    $filter = $this->filterPeriodeLaporan($request);

    $query = DB::table('detail_pengeluarans as dp')
        ->join('pengeluarans as pg', 'pg.id', '=', 'dp.pengeluaran_id')
        ->leftJoin('branches as b', 'b.id', '=', 'pg.branch_id')
        ->whereYear('pg.tanggal', $filter['tahun'])
        ->whereMonth('pg.tanggal', $filter['bulan']);

    if (!$this->isGlobalUser()) {
        $query->where('pg.branch_id', $this->branchId());
    } elseif (!empty($filter['branch_id'])) {
        $query->where('pg.branch_id', $filter['branch_id']);
    }

    $data = $query->select([
            'pg.id as pengeluaran_id',
            'pg.nomor_pengeluaran',
            'pg.tanggal',
            'pg.divisi',
            'pg.nama_pengambil',
            'pg.branch_id',
            'b.nama_cabang',
            'dp.kode_barang',
            'dp.nama_barang',
            'dp.kategori',
            'dp.satuan',
            'dp.qty',
            'dp.harga_pokok',
            'dp.jumlah_harga_pokok',
        ])
        ->orderBy('pg.divisi')
        ->orderBy('dp.kategori')
        ->orderBy('pg.tanggal')
        ->orderBy('pg.id')
        ->get();

    $groups = $data->groupBy(function ($item) {
        return $item->divisi ?: 'Tanpa Divisi';
    });

    $totalQty = (float) $data->sum('qty');
    $totalNilai = (float) $data->sum('jumlah_harga_pokok');

    $pdf = Pdf::loadView('laporan.pemakaian-divisi-pdf', [
        'groups' => $groups,
        'filter' => $filter,
        'totalQty' => $totalQty,
        'totalNilai' => $totalNilai,
        'isGlobalUser' => $this->isGlobalUser(),
    ])->setPaper('a4', 'landscape');

    return $pdf->stream('laporan-pemakaian-per-divisi.pdf');
}

    /**
     * LAPORAN TAHUNAN PEMAKAIAN PER DIVISI.
     * Membandingkan nilai pemakaian bulan ini dengan bulan sebelumnya.
     */

    public function pemakaianTahunanPdf(Request $request)
    {
        // =========================================
        // 1. VALIDASI FILTER
        // =========================================
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        $tahun = (int) ($validated['tahun'] ?? now()->year);
        $branchId = $validated['branch_id'] ?? null;

        $isGlobalUser = $this->isGlobalUser();

        // =========================================
        // 2. QUERY DATA PEMAKAIAN
        // =========================================
        $subQuery = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'pg.id', '=', 'dp.pengeluaran_id')
            ->whereYear('pg.tanggal', $tahun);

        // User cabang hanya dapat melihat cabangnya sendiri
        if (!$isGlobalUser) {

            $userBranchId = $this->branchId();

            abort_if(
                !$userBranchId,
                403,
                'User belum memiliki cabang.'
            );

            $subQuery->where('pg.branch_id', $userBranchId);

            $branchId = $userBranchId;

        } elseif (!empty($branchId)) {

            // Global user dapat memilih cabang tertentu
            $subQuery->where('pg.branch_id', $branchId);
        }

        // Normalisasi nama divisi di subquery
        $subQuery->selectRaw("
            COALESCE(
                NULLIF(TRIM(pg.divisi), ''),
                'Tanpa Divisi'
            ) AS divisi,
            MONTH(pg.tanggal) AS bulan,
            dp.qty,
            dp.jumlah_harga_pokok
        ");

        // =========================================
        // 3. AGREGASI PER DIVISI DAN BULAN
        // =========================================
        $raw = DB::query()
            ->fromSub($subQuery, 'data')
            ->selectRaw("
                data.divisi,
                data.bulan,
                COALESCE(SUM(data.qty), 0) AS total_qty,
                COALESCE(SUM(data.jumlah_harga_pokok), 0) AS total_nilai
            ")
            ->groupBy('data.divisi', 'data.bulan')
            ->orderBy('data.divisi')
            ->orderBy('data.bulan')
            ->get();

        // =========================================
        // 4. DAFTAR NAMA BULAN
        // =========================================
        $namaBulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // =========================================
        // 5. SIAPKAN DATA SETIAP DIVISI
        // =========================================
        $divisiList = $raw
            ->pluck('divisi')
            ->unique()
            ->values();

        $dataTahunan = [];

        foreach ($divisiList as $divisi) {

            $dataTahunan[$divisi] = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {

                $item = $raw->first(function ($row) use ($divisi, $bulan) {
                    return $row->divisi === $divisi
                        && (int) $row->bulan === $bulan;
                });

                $dataTahunan[$divisi][$bulan] = [
                    'qty' => (float) ($item->total_qty ?? 0),
                    'nilai' => (float) ($item->total_nilai ?? 0),
                ];
            }
        }

        // =========================================
        // 6. TOTAL PER BULAN
        // =========================================
        $totalBulanan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $totalBulanan[$bulan] = [
                'qty' => 0,
                'nilai' => 0,
            ];

            foreach ($dataTahunan as $divisi => $dataBulan) {

                $totalBulanan[$bulan]['qty'] +=
                    $dataBulan[$bulan]['qty'];

                $totalBulanan[$bulan]['nilai'] +=
                    $dataBulan[$bulan]['nilai'];
            }
        }

        // =========================================
        // 7. HITUNG TOTAL TAHUNAN
        // =========================================
        $grandTotalQty = 0;
        $grandTotalNilai = 0;

        foreach ($totalBulanan as $total) {

            $grandTotalQty += $total['qty'];

            $grandTotalNilai += $total['nilai'];
        }

        // =========================================
        // 8. HITUNG KENAIKAN / PENURUNAN
        // =========================================
        $perubahanBulanan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {

            $nilaiSekarang = $totalBulanan[$bulan]['nilai'];

            if ($bulan === 1) {

                $perubahanBulanan[$bulan] = [
                    'selisih' => 0,
                    'persentase' => 0,
                    'status' => 'Awal Tahun',
                ];

                continue;
            }

            $nilaiSebelumnya = $totalBulanan[$bulan - 1]['nilai'];

            $selisih = $nilaiSekarang - $nilaiSebelumnya;

            if ($nilaiSebelumnya > 0) {

                $persentase = ($selisih / $nilaiSebelumnya) * 100;

            } else {

                $persentase = $nilaiSekarang > 0 ? 100 : 0;
            }

            if ($nilaiSebelumnya == 0 && $nilaiSekarang > 0) {

                $status = 'Mulai Ada Pemakaian';

            } elseif ($selisih > 0) {

                $status = 'Naik';

            } elseif ($selisih < 0) {

                $status = 'Turun';

            } else {

                $status = 'Tetap';
            }

            $perubahanBulanan[$bulan] = [
                'selisih' => $selisih,
                'persentase' => $persentase,
                'status' => $status,
            ];
        }

        // =========================================
        // 9. INFORMASI CABANG
        // =========================================
        $namaCabang = 'Semua Cabang';

        if (!empty($branchId)) {

            $namaCabang = DB::table('branches')
                ->where('id', $branchId)
                ->value('nama_cabang') ?? 'Cabang Tidak Diketahui';

        } elseif (!$isGlobalUser) {

            $namaCabang = DB::table('branches')
                ->where('id', $this->branchId())
                ->value('nama_cabang') ?? 'Cabang Tidak Diketahui';
        }

        // =========================================
        // 10. GENERATE PDF
        // =========================================
        $pdf = Pdf::loadView(
            'laporan.pemakaian-tahunan-pdf',
            compact(
                'tahun',
                'namaBulan',
                'divisiList',
                'dataTahunan',
                'totalBulanan',
                'grandTotalQty',
                'grandTotalNilai',
                'perubahanBulanan',
                'namaCabang'
            )
        )->setPaper('a3', 'landscape');

        return $pdf->stream(
            'laporan-pemakaian-tahunan-' . $tahun . '.pdf'
        );
    }


public function laporan (Request $request)
    {
        $this->validateFilter($request);

        return view('laporan.index');
    }
}
