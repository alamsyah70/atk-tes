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
            ->paginate(15)
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
            $query->where('pg.divisi', 'like', '%'.$request->divisi.'%');
        }

        if ($request->filled('nama_pengambil')) {
            $query->where('pg.nama_pengambil', 'like', '%'.$request->nama_pengambil.'%');
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

        $data = $this->queryPengeluaran($request)
            ->paginate(15)
            ->withQueryString();

        return view('laporan.pengeluaran', [
            'data' => $data,
        ]);
    }

    /**
     * PDF LAPORAN PENGELUARAN.
     */
    public function pengeluaranPdf(Request $request)
    {
        $this->validateFilter($request);

        $data = $this->queryPengeluaran($request)->get();

        $pdf = Pdf::loadView('laporan.pengeluaran-pdf', [
            'data' => $data,
            'filter' => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-pengeluaran.pdf');
    }
}
