<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaan;
use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function panduan()
    {
        return view('dashboard.panduan', [
            'title' => 'Panduan',
        ]);
    }
    // ==========================================
    // HAK AKSES CABANG
    // ==========================================

    // Super Admin dan Admin Pusat dapat melihat semua cabang
    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
        ]);
    }

    // ==========================================
    // QUERY DETAIL BARANG SESUAI CABANG
    // ==========================================

    private function detailBarangQuery()
    {
        $query = DetailPenerimaan::query()
            ->join(
                'penerimaans as p',
                'detail_penerimaans.penerimaan_id',
                '=',
                'p.id'
            )
            ->select('detail_penerimaans.*');

        if (!$this->isGlobalUser()) {
            $branchId = auth()->user()->branch_id;

            abort_if(
                !$branchId,
                403,
                'User belum memiliki cabang.'
            );

            $query->where('p.branch_id', $branchId);
        }

        return $query;
    }

    // Query khusus untuk rekap dashboard
    private function dashboardDetailQuery()
    {
        return $this->detailBarangQuery();
    }

    // ==========================================
    // QUERY SUPPLIER SESUAI CABANG
    // ==========================================

    private function supplierQuery()
    {
        $query = Supplier::query();

        if (!$this->isGlobalUser()) {
            $branchId = auth()->user()->branch_id;

            abort_if(
                !$branchId,
                403,
                'User belum memiliki cabang.'
            );

            $query->where('branch_id', $branchId);
        }

        return $query;
    }

    // ==========================================
    // DASHBOARD
    // ==========================================

    
    public function index(Request $request)
    {
        // ==========================================
        // 1. INFORMASI USER DAN FILTER
        // ==========================================

        $search = trim($request->input('search', ''));
        $status = $request->input('status', '');

        $isGlobal = $this->isGlobalUser();
        $branchId = auth()->user()->branch_id;

        if (!$isGlobal) {
            abort_if(!$branchId, 403, 'User belum memiliki cabang.');
        }

        // ==========================================
        // 2. REKAP BARANG MASUK
        // ==========================================

        $stokMasuk = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->select('p.branch_id', 'dp.kode_barang')
            ->selectRaw('SUM(dp.qty) as total_masuk')
            ->groupBy('p.branch_id', 'dp.kode_barang');

        // ==========================================
        // 3. REKAP BARANG KELUAR
        // ==========================================

        $stokKeluar = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->select('pg.branch_id', 'dp.kode_barang')
            ->selectRaw('SUM(dp.qty) as total_keluar')
            ->groupBy('pg.branch_id', 'dp.kode_barang');

        // ==========================================
        // 4. REKAP PENYESUAIAN STOK
        // ==========================================

        $stokPenyesuaian = DB::table('penyesuaian_stoks')
            ->select('branch_id', 'kode_barang')
            ->selectRaw('SUM(qty_koreksi) as total_penyesuaian')
            ->groupBy('branch_id', 'kode_barang');

        // ==========================================
        // 5. REKAP HARGA RATA-RATA HISTORIS
        //
        // Digunakan sebagai fallback apabila
        // average_perolehan master masih 0 atau NULL.
        // ==========================================

        $averageHistoris = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->where('dp.qty', '>', 0)
            ->select('p.branch_id', 'dp.kode_barang')
            ->selectRaw('
                SUM(dp.qty * dp.harga) /
                NULLIF(SUM(dp.qty), 0) AS harga_rata_rata_historis
            ')
            ->groupBy('p.branch_id', 'dp.kode_barang');

        // ==========================================
        // 6. QUERY STOK DAN NILAI SALDO
        // ==========================================

        $buildStokQuery = function () use (
            $stokMasuk,
            $stokKeluar,
            $stokPenyesuaian,
            $averageHistoris,
            $isGlobal,
            $branchId
        ) {
            $query = DB::table('barangs as br')

                ->leftJoinSub(clone $stokMasuk, 'masuk', function ($join) {
                    $join->on('br.branch_id', '=', 'masuk.branch_id')
                        ->on('br.kode_barang', '=', 'masuk.kode_barang');
                })

                ->leftJoinSub(clone $stokKeluar, 'keluar', function ($join) {
                    $join->on('br.branch_id', '=', 'keluar.branch_id')
                        ->on('br.kode_barang', '=', 'keluar.kode_barang');
                })

                ->leftJoinSub(clone $stokPenyesuaian, 'penyesuaian', function ($join) {
                    $join->on('br.branch_id', '=', 'penyesuaian.branch_id')
                        ->on('br.kode_barang', '=', 'penyesuaian.kode_barang');
                })

                ->leftJoinSub(clone $averageHistoris, 'avg_historis', function ($join) {
                    $join->on('br.branch_id', '=', 'avg_historis.branch_id')
                        ->on('br.kode_barang', '=', 'avg_historis.kode_barang');
                })

                ->leftJoin('branches as b', 'br.branch_id', '=', 'b.id')

                ->select(
                    'br.id',
                    'br.branch_id',
                    'br.kode_barang',
                    'br.nama_barang',
                    'br.kategori',
                    'br.satuan',
                    'br.lokasi',
                    'b.nama_cabang'
                )

                // Stok akhir
                ->selectRaw('
                    (
                        COALESCE(masuk.total_masuk, 0)
                        - COALESCE(keluar.total_keluar, 0)
                        + COALESCE(penyesuaian.total_penyesuaian, 0)
                    ) AS stok
                ')

                // Harga rata-rata perolehan
                ->selectRaw('
                    COALESCE(
                        NULLIF(br.average_perolehan, 0),
                        avg_historis.harga_rata_rata_historis,
                        0
                    ) AS harga_rata_rata
                ')

                // Nilai saldo akhir = stok akhir x harga rata-rata
                ->selectRaw('
                    (
                        (
                            COALESCE(masuk.total_masuk, 0)
                            - COALESCE(keluar.total_keluar, 0)
                            + COALESCE(penyesuaian.total_penyesuaian, 0)
                        )
                        *
                        COALESCE(
                            NULLIF(br.average_perolehan, 0),
                            avg_historis.harga_rata_rata_historis,
                            0
                        )
                    ) AS nilai_saldo
                ');

            if (!$isGlobal) {
                $query->where('br.branch_id', $branchId);
            }

            return $query;
        };

        // ==========================================
        // 7. STATISTIK DASHBOARD
        // ==========================================

        $totalItems = DB::query()
            ->fromSub($buildStokQuery(), 'rekap')
            ->where('rekap.stok', '>', 0)
            ->count();

        $totalStock = DB::query()
            ->fromSub($buildStokQuery(), 'rekap')
            ->sum('rekap.stok') ?? 0;

        $lowStock = DB::query()
            ->fromSub($buildStokQuery(), 'rekap')
            ->whereBetween('rekap.stok', [1, 10])
            ->count();

        // Total nilai saldo seluruh barang
        $totalInventoryValue = DB::query()
            ->fromSub($buildStokQuery(), 'rekap')
            ->sum('rekap.nilai_saldo') ?? 0;

        // ==========================================
        // 8. TOTAL KATEGORI
        // ==========================================

        $kategoriQuery = DB::table('barangs')
            ->whereNotNull('kategori')
            ->where('kategori', '<>', '');

        if (!$isGlobal) {
            $kategoriQuery->where('branch_id', $branchId);
        }

        $totalCategories = $kategoriQuery
            ->distinct()
            ->count('kategori');

        // ==========================================
        // 9. DAFTAR BARANG
        // ==========================================

        $queryBarang = DB::query()
            ->fromSub($buildStokQuery(), 'rekap');

        if ($search !== '') {
            $queryBarang->where(function ($q) use ($search) {
                $q->where('rekap.kode_barang', 'like', "%{$search}%")
                    ->orWhere('rekap.nama_barang', 'like', "%{$search}%")
                    ->orWhere('rekap.kategori', 'like', "%{$search}%")
                    ->orWhere('rekap.nama_cabang', 'like', "%{$search}%")
                    ->orWhere('rekap.lokasi', 'like', "%{$search}%");
            });
        }

        // ==========================================
        // 10. FILTER STATUS
        // ==========================================

        if ($status === 'tersedia') {
            $queryBarang->where('rekap.stok', '>', 10);
        } elseif ($status === 'menipis') {
            $queryBarang->whereBetween('rekap.stok', [1, 10]);
        } elseif ($status === 'habis') {
            $queryBarang->where('rekap.stok', '<=', 0);
        }

        // ==========================================
        // 11. PAGINATION
        // ==========================================

        $barang = $queryBarang
            ->orderBy('rekap.nama_barang')
            ->paginate(10)
            ->withQueryString();

        // ==========================================
        // 12. TAMPILKAN DASHBOARD
        // ==========================================

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'lowStock' => $lowStock,
            'totalCategories' => $totalCategories,
            'totalInventoryValue' => $totalInventoryValue,
            'barang' => $barang,
            'search' => $search,
        ]);
    }


    // ==========================================
    // DAFTAR BARANG
    // ==========================================

    
    public function kelolabarang(Request $request)
    {
        $user = auth()->user();

        // ==========================================
        // 1. HAK AKSES DAN CABANG
        // ==========================================

        // Pertahankan pembatasan akses sesuai aturan saat ini.
        abort_unless($user->hasRole('Super Admin'), 403);

        $isGlobal = $this->isGlobalUser();
        $branchId = $user->branch_id;

        if (!$isGlobal) {
            abort_if(!$branchId, 403, 'User belum memiliki cabang.');
        }

        // ==========================================
        // 2. REKAP BARANG MASUK
        // ==========================================

        $barangMasuk = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->select(
                'p.branch_id',
                'dp.kode_barang'
            )
            ->selectRaw('SUM(dp.qty) as total_masuk')
            ->groupBy(
                'p.branch_id',
                'dp.kode_barang'
            );

        // ==========================================
        // 3. REKAP BARANG KELUAR
        // ==========================================

        $barangKeluar = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->select(
                'pg.branch_id',
                'dp.kode_barang'
            )
            ->selectRaw('SUM(dp.qty) as total_keluar')
            ->groupBy(
                'pg.branch_id',
                'dp.kode_barang'
            );

        // ==========================================
        // 4. REKAP PENYESUAIAN STOK
        // ==========================================

        $barangPenyesuaian = DB::table('penyesuaian_stoks')
            ->select(
                'branch_id',
                'kode_barang'
            )
            ->selectRaw('SUM(qty_koreksi) as total_penyesuaian')
            ->groupBy(
                'branch_id',
                'kode_barang'
            );

        // ==========================================
        // 5. QUERY MASTER BARANG + STOK
        // ==========================================

        $stokBersih = DB::table('barangs as br')

            // Barang masuk
            ->leftJoinSub($barangMasuk, 'masuk', function ($join) {
                $join->on('br.branch_id', '=', 'masuk.branch_id')
                    ->on('br.kode_barang', '=', 'masuk.kode_barang');
            })

            // Barang keluar
            ->leftJoinSub($barangKeluar, 'keluar', function ($join) {
                $join->on('br.branch_id', '=', 'keluar.branch_id')
                    ->on('br.kode_barang', '=', 'keluar.kode_barang');
            })

            // Penyesuaian stok
            ->leftJoinSub($barangPenyesuaian, 'penyesuaian', function ($join) {
                $join->on('br.branch_id', '=', 'penyesuaian.branch_id')
                    ->on('br.kode_barang', '=', 'penyesuaian.kode_barang');
            })

            // Cabang
            ->leftJoin('branches as b', 'br.branch_id', '=', 'b.id')

            // Kolom Master Barang
            ->select(
                'br.id',
                'br.branch_id',
                'br.kode_barang',
                'br.nama_barang',
                'br.kategori',
                'br.satuan',
                'br.lokasi',
                'b.nama_cabang'
            )

            // Rumus stok
            ->selectRaw('
                (
                    COALESCE(masuk.total_masuk, 0)
                    - COALESCE(keluar.total_keluar, 0)
                    + COALESCE(penyesuaian.total_penyesuaian, 0)
                ) as stok
            ')

            // Abaikan data Master Barang lama yang belum memiliki cabang
            ->whereNotNull('br.branch_id');

        // ==========================================
        // 6. BATASAN CABANG
        // ==========================================

        if (!$isGlobal) {
            $stokBersih->where('br.branch_id', $branchId);
        }

        // ==========================================
        // 7. QUERY UTAMA
        // ==========================================

        $query = DB::query()
            ->fromSub($stokBersih, 'rekap');

        // ==========================================
        // 8. FILTER KODE BARANG
        // ==========================================

        if ($request->filled('q_kode')) {
            $query->where(
                'rekap.kode_barang',
                'like',
                '%' . trim($request->q_kode) . '%'
            );
        }

        // ==========================================
        // 9. FILTER NAMA BARANG
        // ==========================================

        if ($request->filled('q_nama')) {
            $query->where(
                'rekap.nama_barang',
                'like',
                '%' . trim($request->q_nama) . '%'
            );
        }

        // ==========================================
        // 10. FILTER KATEGORI
        // ==========================================

        if ($request->filled('q_kategori')) {
            $query->where(
                'rekap.kategori',
                'like',
                '%' . trim($request->q_kategori) . '%'
            );
        }

        // ==========================================
        // 11. FILTER LOKASI / CABANG
        // ==========================================

        if ($request->filled('q_lokasi')) {
            $lokasi = trim($request->q_lokasi);

            $query->where(function ($q) use ($lokasi) {
                $q->where('rekap.lokasi', 'like', "%{$lokasi}%")
                    ->orWhere('rekap.nama_cabang', 'like', "%{$lokasi}%");
            });
        }

        // ==========================================
        // 12. FILTER JUMLAH STOK
        // ==========================================

        if ($request->filled('q_stok')) {
            $query->where('rekap.stok', '=', $request->q_stok);
        }

        // ==========================================
        // 13. FILTER STATUS
        // ==========================================

        if ($request->q_status === 'tersedia') {

            $query->where('rekap.stok', '>', 10);

        } elseif ($request->q_status === 'menipis') {

            $query->whereBetween('rekap.stok', [1, 10]);

        } elseif ($request->q_status === 'habis') {

            $query->where('rekap.stok', '<=', 0);
        }

        // ==========================================
        // 14. HASIL DAN PAGINATION
        // ==========================================

        $barangs = $query
            ->orderBy('rekap.nama_barang')
            ->orderBy('rekap.kode_barang')
            ->paginate(15)
            ->withQueryString();

        return view('databarang.kelolabarang', [
            'barangs' => $barangs,
            'title' => 'Daftar Barang',
        ]);
    }
    

    private function hitungStokSebelumPenerimaan(
        Barang $barang,
        int $branchId
    ): float {

        $totalMasuk = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->where('p.branch_id', $branchId)
            ->where(function ($q) use ($barang) {
                $q->where('dp.barang_id', $barang->id)
                    ->orWhere(function ($legacy) use ($barang) {
                        $legacy->whereNull('dp.barang_id')
                            ->where('dp.kode_barang', $barang->kode_barang);
                    });
            })
            ->sum('dp.qty');

        $totalKeluar = DB::table('detail_pengeluarans as d')
            ->join('pengeluarans as p', 'd.pengeluaran_id', '=', 'p.id')
            ->where('p.branch_id', $branchId)
            ->where(function ($q) use ($barang) {
                $q->where('d.barang_id', $barang->id)
                    ->orWhere(function ($legacy) use ($barang) {
                        $legacy->whereNull('d.barang_id')
                            ->where('d.kode_barang', $barang->kode_barang);
                    });
            })
            ->sum('d.qty');

        $totalPenyesuaian = DB::table('penyesuaian_stoks')
            ->where('branch_id', $branchId)
            ->where('kode_barang', $barang->kode_barang)
            ->sum('qty_koreksi');

        return (float) $totalMasuk
            - (float) $totalKeluar
            + (float) $totalPenyesuaian;
    }


    // ==========================================
    // PENERIMAAN BARANG
    // ==========================================

    public function penerimaanbarang(Request $request)
    {
        $suppliers = $this->supplierQuery()
            ->orderBy('nama_supplier')
            ->get();

        $user = auth()->user();

        // User cabang hanya melihat histori cabangnya sendiri.
        if (!$this->isGlobalUser()) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');
        }

        $riwayat = Penerimaan::with(['supplier', 'branch'])
            ->withCount('details')
            ->when(!$this->isGlobalUser(), function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('nomor_penerimaan', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplier) use ($search) {
                            $supplier->where('nama_supplier', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details', function ($detail) use ($search) {
                            $detail->where('kode_barang', 'like', "%{$search}%")
                                ->orWhere('nama_barang', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('databarang.penerimaanbarang', [
            'suppliers' => $suppliers,
            'riwayat' => $riwayat,
            'title' => 'Penerimaan Barang',
        ]);
    }

    // ==========================================
    // SIMPAN PENERIMAAN BARANG
    // ==========================================

    
    public function store(Request $request)
    {
        // ==========================================
        // 1. VALIDASI INPUT
        // ==========================================

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',

            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'required|integer',

            'kode_barang' => 'required|array|min:1',
            'kode_barang.*' => 'required|string|max:50',

            'nama_barang' => 'required|array',
            'nama_barang.*' => 'required|string',

            'kategori' => 'required|array',
            'kategori.*' => 'required|string',

            'satuan' => 'required|array',
            'satuan.*' => 'required|string',

            'lokasi' => 'required|array',
            'lokasi.*' => 'required|string',

            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',

            'harga' => 'required|array|min:1',
            'harga.*' => 'required|numeric|min:0',

            'bukti_po' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // ==========================================
        // 2. PERIKSA JUMLAH BARIS
        // ==========================================

        $jumlahBaris = count($validated['kode_barang']);

        foreach ([
            'barang_id',
            'nama_barang',
            'kategori',
            'satuan',
            'lokasi',
            'qty',
            'harga',
        ] as $field) {
            if (count($validated[$field]) !== $jumlahBaris) {
                return back()
                    ->withInput()
                    ->withErrors([
                        $field => "Jumlah data {$field} tidak sesuai dengan jumlah barang.",
                    ]);
            }
        }

        // ==========================================
        // 3. PERIKSA SUPPLIER
        // ==========================================

        $supplier = $this->supplierQuery()
            ->findOrFail($validated['supplier_id']);

        abort_if(
            !$supplier->branch_id,
            422,
            'Supplier belum memiliki cabang. Silakan atur cabang supplier terlebih dahulu.'
        );

        $branchId = (int) $supplier->branch_id;

        // User cabang tidak boleh menggunakan supplier
        // dari cabang lain.
        if (!$this->isGlobalUser()) {
            abort_if(
                (int) auth()->user()->branch_id !== $branchId,
                403,
                'Supplier bukan milik cabang Anda.'
            );
        }

        $buktiPoPath = null;

        if ($request->hasFile('bukti_po')) {
            $buktiPoPath = $request->file('bukti_po')
                ->store('bukti-po', 'public');
        }

        try {

            $penerimaan = DB::transaction(function () use (
                $validated,
                $branchId,
                $jumlahBaris,
                $buktiPoPath
            ) {

                // ==========================================
                // 4. KUNCI CABANG
                // ==========================================

                $branch = DB::table('branches')
                    ->where('id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (!$branch) {
                    throw new \RuntimeException(
                        'Cabang tidak ditemukan.'
                    );
                }

                // ==========================================
                // 5. VALIDASI DAN KUNCI SUPPLIER
                // ==========================================

                $supplier = $this->supplierQuery()
                    ->whereKey($validated['supplier_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int) $supplier->branch_id !== $branchId) {
                    throw new \RuntimeException(
                        'Cabang supplier tidak sesuai dengan transaksi.'
                    );
                }

                // ==========================================
                // 6. NOMOR PENERIMAAN
                // Format TRM-MMYY-000001
                // Urutan per cabang, berlanjut antarbulan.
                // ==========================================

                $urutanTerakhir = DB::table('penerimaans')
                    ->where('branch_id', $branchId)
                    ->whereRaw(
                        "nomor_penerimaan REGEXP ?",
                        ['^TRM-[0-9]{4}-[0-9]+$']
                    )
                    ->selectRaw("
                        COALESCE(
                            MAX(
                                CAST(
                                    SUBSTRING_INDEX(
                                        nomor_penerimaan,
                                        '-',
                                        -1
                                    ) AS UNSIGNED
                                )
                            ),
                            0
                        ) AS urutan
                    ")
                    ->value('urutan');

                $urutanBaru = ((int) $urutanTerakhir) + 1;

                $nomorPenerimaan = 'TRM-' .
                    \Carbon\Carbon::parse(
                        $validated['tanggal']
                    )->format('my') .
                    '-' .
                    str_pad(
                        $urutanBaru,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );

                // ==========================================
                // 7. PERIKSA MASTER BARANG
                // ==========================================

                $masterBarang = [];

                for ($i = 0; $i < $jumlahBaris; $i++) {

                    $barangId = (int) $validated['barang_id'][$i];
                    $kode = trim($validated['kode_barang'][$i]);

                    if (!isset($masterBarang[$barangId])) {

                        $barang = Barang::query()
                            ->where('id', $barangId)
                            ->where('branch_id', $branchId)
                            ->lockForUpdate()
                            ->first();

                        if (!$barang) {
                            throw new \RuntimeException(
                                "Barang {$kode} tidak ditemukan pada master barang cabang ini."
                            );
                        }

                        if ($barang->kode_barang !== $kode) {
                            throw new \RuntimeException(
                                "Kode barang {$kode} tidak sesuai dengan master barang."
                            );
                        }

                        $masterBarang[$barangId] = $barang;
                    }

                    if (
                        $masterBarang[$barangId]->kode_barang !== $kode
                    ) {
                        throw new \RuntimeException(
                            "Identitas barang pada baris " . ($i + 1) . " tidak sesuai."
                        );
                    }
                }

                // ==========================================
                // 8. KELOMPOKKAN QTY DAN NILAI PEMBELIAN
                // Untuk menghitung average satu kali per barang.
                // ==========================================

                $rekapBarang = [];

                for ($i = 0; $i < $jumlahBaris; $i++) {

                    $barangId = (int) $validated['barang_id'][$i];
                    $qty = (int) $validated['qty'][$i];
                    $harga = (float) $validated['harga'][$i];

                    if (!isset($rekapBarang[$barangId])) {
                        $rekapBarang[$barangId] = [
                            'qty' => 0,
                            'nilai' => 0,
                        ];
                    }

                    $rekapBarang[$barangId]['qty'] += $qty;
                    $rekapBarang[$barangId]['nilai'] +=
                        $qty * $harga;
                }

                // ==========================================
                // 9. HITUNG AVERAGE PEROLEHAN BARU
                // ==========================================

                $averageBaru = [];

                foreach ($rekapBarang as $barangId => $rekap) {

                    $barang = $masterBarang[$barangId];

                    $stokLama = $this->hitungStokSebelumPenerimaan(
                        $barang,
                        $branchId
                    );

                    if ($stokLama < 0) {
                        throw new \RuntimeException(
                            "Stok {$barang->kode_barang} negatif ({$stokLama}). Periksa histori transaksi dan penyesuaian terlebih dahulu."
                        );
                    }

                    $averageLama = (float) $barang->average_perolehan;

                    // Jika average master belum tersedia,
                    // hitung dari histori penerimaan.
                    $averageLama = (float) $barang->average_perolehan;

                        if ($stokLama > 0 && $averageLama <= 0) {
                            throw new \RuntimeException(
                                "Average perolehan {$barang->kode_barang} belum valid. " .
                                "Silakan lakukan audit histori transaksi sebelum penerimaan."
                            );
                        }

                    $qtyBaru = (int) $rekap['qty'];
                    $nilaiBaru = (float) $rekap['nilai'];

                    $totalQty = $stokLama + $qtyBaru;

                    if ($totalQty <= 0) {
                        throw new \RuntimeException(
                            "Total stok {$barang->kode_barang} tidak valid."
                        );
                    }

                    $totalNilaiLama = $stokLama * $averageLama;

                    $averageBaru[$barangId] = (
                        $totalNilaiLama + $nilaiBaru
                    ) / $totalQty;
                }

                // ==========================================
                // 10. SIMPAN HEADER PENERIMAAN
                // ==========================================

                $penerimaan = Penerimaan::create([
                    'supplier_id' => $supplier->id,
                    'branch_id' => $branchId,
                    'nomor_penerimaan' => $nomorPenerimaan,
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => $validated['keterangan'] ?? null,
                    'bukti_po' => $buktiPoPath,
                ]);

                // ==========================================
                // 11. SIMPAN DETAIL PENERIMAAN
                // ==========================================

                for ($i = 0; $i < $jumlahBaris; $i++) {

                    $barangId = (int) $validated['barang_id'][$i];
                    $barang = $masterBarang[$barangId];

                    $qty = (int) $validated['qty'][$i];
                    $harga = (float) $validated['harga'][$i];

                    DetailPenerimaan::create([
                        'penerimaan_id' => $penerimaan->id,

                        'barang_id' => $barang->id,
                        'kode_barang' => $barang->kode_barang,
                        'nama_barang' => $barang->nama_barang,
                        'kategori' => $barang->kategori,
                        'satuan' => $barang->satuan,
                        'lokasi' => $barang->lokasi,

                        'qty' => $qty,
                        'harga' => $harga,
                        'jumlah' => $qty * $harga,
                    ]);
                }

                // ==========================================
                // 12. UPDATE AVERAGE PEROLEHAN MASTER
                // ==========================================

                foreach ($averageBaru as $barangId => $average) {

                    DB::table('barangs')
                        ->where('id', $barangId)
                        ->where('branch_id', $branchId)
                        ->update([
                            'average_perolehan' => $average,
                            'updated_at' => now(),
                        ]);
                }

                return $penerimaan;

            }, 3);

            return redirect()
                ->route('databarang.penerimaan')
                ->with(
                    'success',
                    'Data penerimaan barang berhasil disimpan!'
                );

            } catch (\Throwable $e) {

                // Hapus file jika transaksi database gagal
                if ($buktiPoPath && Storage::disk('public')->exists($buktiPoPath)) {
                    Storage::disk('public')->delete($buktiPoPath);
                }
            
                return back()
                    ->withInput()
                    ->withErrors([
                        'penerimaan' => $e->getMessage(),
                    ]);
            }
    }


    // ==========================================
    // EDIT BARANG
    // ==========================================

    public function edit($id)
    {
        // Pastikan barang berasal dari cabang user
        $barang = $this->detailBarangQuery()
            ->findOrFail($id);

        return view('databarang.editbarang', [
            'barang' => $barang,
            'title' => 'Edit Barang',
        ]);
    }

    // ==========================================
    // UPDATE MASTER BARANG + PENYESUAIAN STOK
    // ==========================================

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $isGlobal = $this->isGlobalUser();

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'lokasi' => 'required|string|max:100',
            'qty_koreksi' => 'nullable|integer',
            'alasan' => 'nullable|string|max:255',
        ]);

        if (!$isGlobal && !$user->branch_id) {
            abort(403, 'User belum memiliki cabang.');
        }

        DB::transaction(function () use ($validated, $id, $user, $isGlobal) {

            // 1. Ambil Master Barang dan kunci baris
            $query = Barang::query()
                ->where('id', $id);

            if (!$isGlobal) {
                $query->where('branch_id', $user->branch_id);
            }

            $barang = $query->lockForUpdate()->firstOrFail();

            $branchId = $barang->branch_id;
            $kodeLama = $barang->kode_barang;
            $kodeBaru = trim($validated['kode_barang']);

            if (!$branchId) {
                throw ValidationException::withMessages([
                    'kode_barang' => 'Barang belum memiliki cabang.',
                ]);
            }

            // 2. Cek duplikasi kode pada cabang yang sama
            $kodeSudahAda = Barang::where('branch_id', $branchId)
                ->where('kode_barang', $kodeBaru)
                ->where('id', '!=', $barang->id)
                ->exists();

            if ($kodeSudahAda) {
                throw ValidationException::withMessages([
                    'kode_barang' => 'Kode barang sudah digunakan pada cabang ini.',
                ]);
            }

            // 3. Hitung barang masuk
            $stokMasuk = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->where('p.branch_id', $branchId)
            ->where(function ($query) use ($barang, $kodeLama) {
                $query->where('dp.barang_id', $barang->id)
                    ->orWhere(function ($subQuery) use ($kodeLama) {
                        $subQuery->whereNull('dp.barang_id')
                            ->where('dp.kode_barang', $kodeLama);
                    });
            })
            ->sum('dp.qty');

            // 4. Hitung barang keluar
            $stokKeluar = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as p', 'dp.pengeluaran_id', '=', 'p.id')
            ->where('p.branch_id', $branchId)
            ->where(function ($query) use ($barang, $kodeLama) {
                $query->where('dp.barang_id', $barang->id)
                    ->orWhere(function ($subQuery) use ($kodeLama) {
                        $subQuery->whereNull('dp.barang_id')
                            ->where('dp.kode_barang', $kodeLama);
                    });
            })
            ->sum('dp.qty');

            // 5. Hitung penyesuaian stok
            // Mendukung histori lama yang belum memiliki barang_id.
            $stokPenyesuaian = DB::table('penyesuaian_stoks')
                ->where('branch_id', $branchId)
                ->where(function ($query) use ($barang, $kodeLama) {
                    $query->where('barang_id', $barang->id)
                        ->orWhere(function ($subQuery) use ($kodeLama) {
                            $subQuery->whereNull('barang_id')
                                ->where('kode_barang', $kodeLama);
                        });
                })
                ->sum('qty_koreksi');

            $stokSekarang = (int) $stokMasuk
                - (int) $stokKeluar
                + (int) $stokPenyesuaian;

            // 6. Validasi koreksi stok
            $qtyKoreksi = (int) ($validated['qty_koreksi'] ?? 0);

            $stokAkhir = $stokSekarang + $qtyKoreksi;

            if ($stokAkhir < 0) {
                throw ValidationException::withMessages([
                    'qty_koreksi' => "Stok tidak mencukupi. Stok saat ini: {$stokSekarang}.",
                ]);
            }

            // 7. Update Master Barang
            $barang->update([
                'kode_barang' => $kodeBaru,
                'nama_barang' => $validated['nama_barang'],
                'kategori' => $validated['kategori'],
                'satuan' => $validated['satuan'],
                'lokasi' => $validated['lokasi'],

                // average_perolehan TIDAK diubah di sini.
                // Average berubah hanya melalui penerimaan barang.
            ]);

            // 8. Simpan histori penyesuaian stok
            if ($qtyKoreksi !== 0) {
                DB::table('penyesuaian_stoks')->insert([
                    'branch_id' => $branchId,
                    'barang_id' => $barang->id,
                    'user_id' => $user->id,

                    'nomor_penyesuaian' => 'ADJ-' .
                        now()->format('YmdHis') . '-' . $user->id,

                    'kode_barang' => $kodeBaru,
                    'nama_barang' => $validated['nama_barang'],
                    'qty_koreksi' => $qtyKoreksi,

                    'alasan' => $validated['alasan']
                        ?? 'Penyesuaian stok barang',

                    'tanggal' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('databarang.barang')
            ->with('success', 'Master barang dan penyesuaian stok berhasil diperbarui!');
    }

    // ==========================================
    // HAPUS MASTER BARANG
    // ==========================================

    public function destroy($id)
    {
        $user = auth()->user();
        $isGlobal = $this->isGlobalUser();

        if (!$isGlobal) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');
        }

        DB::transaction(function () use ($id, $user, $isGlobal) {

            // 1. Ambil Master Barang
            $query = Barang::query()->where('id', $id);

            if (!$isGlobal) {
                $query->where('branch_id', $user->branch_id);
            }

            $barang = $query->lockForUpdate()->firstOrFail();

            $branchId = $barang->branch_id;

            // 2. Cek histori penerimaan berdasarkan barang_id
            $totalMasuk = DB::table('detail_penerimaans')
                ->where('barang_id', $barang->id)
                ->sum('qty');

            // 3. Cek histori pengeluaran berdasarkan barang_id
            $totalKeluar = DB::table('detail_pengeluarans')
                ->where('barang_id', $barang->id)
                ->sum('qty');

            // 4. Cek histori penyesuaian
            $adaPenyesuaian = DB::table('penyesuaian_stoks')
                ->where('branch_id', $branchId)
                ->where('kode_barang', $barang->kode_barang)
                ->exists();

            // 5. Tolak jika sudah memiliki histori transaksi
            if ($totalMasuk > 0 || $totalKeluar > 0 || $adaPenyesuaian) {
                throw ValidationException::withMessages([
                    'barang' => 'Barang tidak dapat dihapus karena sudah memiliki histori penerimaan, pengeluaran, atau penyesuaian stok.',
                ]);
            }

            // 6. Hapus Master Barang
            $barang->delete();
        });

        return redirect()
            ->route('databarang.barang')
            ->with('success', 'Master barang berhasil dihapus!');
    }

    public function penerimaanPdf($id)
    {
        $user = auth()->user();

        if (!$this->isGlobalUser()) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');
        }

        $query = Penerimaan::with([
            'supplier',
            'branch',
            'details',
        ]);

        if (!$this->isGlobalUser()) {
            $query->where('branch_id', $user->branch_id);
        }

        $penerimaan = $query->findOrFail($id);

        $pdf = Pdf::loadView(
            'databarang.penerimaan-pdf',
            compact('penerimaan')
        );

        return $pdf->setPaper('a4', 'landscape')
            ->stream($penerimaan->nomor_penerimaan . '.pdf');
    }
}
