<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaan;
use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Ambil kata pencarian
        $search = trim($request->input('search', ''));

        // Query penerimaan sesuai cabang
        $detailQuery = $this->dashboardDetailQuery();

        // 1. Rekap total barang masuk berdasarkan kode barang
        $stokMasuk = (clone $detailQuery)
            ->select('detail_penerimaans.kode_barang')
            ->selectRaw('SUM(detail_penerimaans.qty) as total_masuk')
            ->groupBy('detail_penerimaans.kode_barang');

        // 2. Rekap total barang keluar sesuai cabang
        $stokKeluar = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->select('dp.kode_barang')
            ->selectRaw('SUM(dp.qty) as total_keluar')
            ->when(!$this->isGlobalUser(), function ($query) {
                $branchId = auth()->user()->branch_id;

                abort_if(!$branchId, 403, 'User belum memiliki cabang.');

                $query->where('pg.branch_id', $branchId);
            })
            ->groupBy('dp.kode_barang');

        // 3. Rekap penyesuaian stok sesuai cabang
        $stokPenyesuaian = DB::table('penyesuaian_stoks')
            ->select('kode_barang')
            ->selectRaw('SUM(qty_koreksi) as total_penyesuaian')
            ->when(!$this->isGlobalUser(), function ($query) {
                $branchId = auth()->user()->branch_id;

                abort_if(!$branchId, 403, 'User belum memiliki cabang.');

                $query->where('branch_id', $branchId);
            })
            ->groupBy('kode_barang');

        // 4. Gabungkan perhitungan stok bersih
        $stokBersih = DB::query()
            ->fromSub(clone $stokMasuk, 'masuk')
            ->leftJoinSub(clone $stokKeluar, 'keluar', function ($join) {
                $join->on('masuk.kode_barang', '=', 'keluar.kode_barang');
            })
            ->leftJoinSub(clone $stokPenyesuaian, 'penyesuaian', function ($join) {
                $join->on('masuk.kode_barang', '=', 'penyesuaian.kode_barang');
            })
            ->select('masuk.kode_barang')
            ->selectRaw('
                (
                    COALESCE(masuk.total_masuk, 0)
                    - COALESCE(keluar.total_keluar, 0)
                    + COALESCE(penyesuaian.total_penyesuaian, 0)
                ) as stok
            ');

        // 5. Total jenis barang
        $totalItems = DB::query()
            ->fromSub(clone $stokBersih, 'rekap')
            ->where('stok', '>', 0)
            ->count();

        // 6. Total seluruh stok tersedia
        $totalStock = DB::query()
            ->fromSub(clone $stokBersih, 'rekap')
            ->sum('stok');

        // 7. Stok menipis (stok 1-10)
        $lowStock = DB::query()
            ->fromSub(clone $stokBersih, 'rekap')
            ->whereBetween('stok', [1, 10])
            ->count();

        // 8. Total kategori unik
        $totalCategories = (clone $detailQuery)
            ->whereNotNull('detail_penerimaans.kategori')
            ->where('detail_penerimaans.kategori', '<>', '')
            ->distinct()
            ->count('detail_penerimaans.kategori');

        // 9. Daftar barang + pencarian + pagination
        $rekapMasuk = DB::table('detail_penerimaans as dp')
        ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
        ->select(
            'p.branch_id',
            'dp.kode_barang',
            DB::raw('MAX(dp.nama_barang) as nama_barang'),
            DB::raw('MAX(dp.kategori) as kategori'),
            DB::raw('MAX(dp.satuan) as satuan'),
            DB::raw('MAX(dp.lokasi) as lokasi'),
            DB::raw('SUM(dp.qty) as total_masuk')
        )
        ->groupBy('p.branch_id', 'dp.kode_barang');

        // Rekap stok keluar
        $rekapKeluar = DB::table('detail_pengeluarans as dp')
        ->join('pengeluarans as p', 'dp.pengeluaran_id', '=', 'p.id')
        ->select(
            'p.branch_id',
            'dp.kode_barang',
            DB::raw('SUM(dp.qty) as total_keluar')
        )
        ->groupBy('p.branch_id', 'dp.kode_barang');

        // Query utama
        $queryBarang = DB::query()
        ->fromSub($rekapMasuk, 'rekap')
        ->leftJoinSub($rekapKeluar, 'keluar', function ($join) {
            $join->on('rekap.branch_id', '=', 'keluar.branch_id')
                ->on('rekap.kode_barang', '=', 'keluar.kode_barang');
        })
        ->leftJoin('branches as b', 'rekap.branch_id', '=', 'b.id')
        ->select(
            'rekap.branch_id',
            'rekap.kode_barang',
            'rekap.nama_barang',
            'rekap.kategori',
            'rekap.satuan',
            'rekap.lokasi',
            'b.nama_cabang',
            DB::raw('rekap.total_masuk - COALESCE(keluar.total_keluar, 0) as stok')
        )
        ->whereRaw('(rekap.total_masuk - COALESCE(keluar.total_keluar, 0)) > 0');

        // Batasi data untuk pengguna cabang
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat'])) {
            $queryBarang->where(
                'rekap.branch_id',
                auth()->user()->branch_id
            );
        }

        // Pencarian barang
        $queryBarang->when($search !== '', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rekap.kode_barang', 'like', "%{$search}%")
                    ->orWhere('rekap.nama_barang', 'like', "%{$search}%")
                    ->orWhere('rekap.kategori', 'like', "%{$search}%")
                    ->orWhere('b.nama_cabang', 'like', "%{$search}%")
                    ->orWhere('rekap.lokasi', 'like', "%{$search}%");
            });
        });

        // Pagination
        $barang = $queryBarang
        ->orderBy('rekap.nama_barang')
        ->paginate(10)
        ->withQueryString();

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'lowStock' => $lowStock,
            'totalCategories' => $totalCategories,
            'barang' => $barang,
            'search' => $search,
        ]);
    }

    // ==========================================
    // DAFTAR BARANG
    // ==========================================

    public function kelolabarang(Request $request)
    {
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $user = auth()->user();

        // Super Admin dapat melihat seluruh cabang.
        // User selain Super Admin hanya melihat cabangnya sendiri.
        $isGlobal = $user->hasRole('Super Admin');

        if (!$isGlobal) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');
        }

        // ==========================================
        // 1. REKAP BARANG MASUK
        // ==========================================
        $barangMasuk = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
            ->select(
                'p.branch_id',
                'dp.kode_barang'
            )
            ->selectRaw('
                MAX(dp.nama_barang) as nama_barang,
                MAX(dp.kategori) as kategori,
                MAX(dp.satuan) as satuan,
                MAX(dp.lokasi) as lokasi,
                SUM(dp.qty) as total_masuk
            ')
            ->when(!$isGlobal, function ($q) use ($user) {
                $q->where('p.branch_id', $user->branch_id);
            })
            ->groupBy('p.branch_id', 'dp.kode_barang');

        // ==========================================
        // 2. REKAP BARANG KELUAR
        // ==========================================
        $barangKeluar = DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
            ->select(
                'pg.branch_id',
                'dp.kode_barang'
            )
            ->selectRaw('SUM(dp.qty) as total_keluar')
            ->when(!$isGlobal, function ($q) use ($user) {
                $q->where('pg.branch_id', $user->branch_id);
            })
            ->groupBy('pg.branch_id', 'dp.kode_barang');

        // ==========================================
        // 3. REKAP PENYESUAIAN STOK
        // ==========================================
        $barangPenyesuaian = DB::table('penyesuaian_stoks')
            ->select(
                'branch_id',
                'kode_barang'
            )
            ->selectRaw('SUM(qty_koreksi) as total_penyesuaian')
            ->when(!$isGlobal, function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->groupBy('branch_id', 'kode_barang');

        // ==========================================
        // 4. HITUNG STOK BERSIH
        // ==========================================
        $stokBersih = DB::query()
            ->fromSub(clone $barangMasuk, 'masuk')

            // Gabungkan barang keluar berdasarkan cabang dan kode
            ->leftJoinSub(clone $barangKeluar, 'keluar', function ($join) {
                $join->on('masuk.kode_barang', '=', 'keluar.kode_barang')
                    ->on('masuk.branch_id', '=', 'keluar.branch_id');
            })

            // Gabungkan penyesuaian berdasarkan cabang dan kode
            ->leftJoinSub(clone $barangPenyesuaian, 'penyesuaian', function ($join) {
                $join->on('masuk.kode_barang', '=', 'penyesuaian.kode_barang')
                    ->on('masuk.branch_id', '=', 'penyesuaian.branch_id');
            })

            // Ambil nama cabang
            ->leftJoin('branches as b', 'masuk.branch_id', '=', 'b.id')

            ->select(
                'masuk.branch_id',
                'masuk.kode_barang',
                'masuk.nama_barang',
                'masuk.kategori',
                'masuk.satuan',
                'masuk.lokasi',
                'b.nama_cabang'
            )

            // Rumus stok bersih
            ->selectRaw('
                (
                    COALESCE(masuk.total_masuk, 0)
                    - COALESCE(keluar.total_keluar, 0)
                    + COALESCE(penyesuaian.total_penyesuaian, 0)
                ) as stok
            ');

        // ==========================================
        // 5. FILTER PENCARIAN
        // ==========================================

        // Pencarian kode barang
        if ($request->filled('q_kode')) {
            $stokBersih->where(
                'masuk.kode_barang',
                'like',
                '%'.$request->q_kode.'%'
            );
        }

        // Pencarian nama barang
        if ($request->filled('q_nama')) {
            $stokBersih->where(
                'masuk.nama_barang',
                'like',
                '%'.$request->q_nama.'%'
            );
        }

        // Pencarian kategori
        if ($request->filled('q_kategori')) {
            $stokBersih->where(
                'masuk.kategori',
                'like',
                '%'.$request->q_kategori.'%'
            );
        }

        // Pencarian lokasi dan nama cabang
        if ($request->filled('q_lokasi')) {
            $stokBersih->where(function ($q) use ($request) {
                $q->where(
                    'masuk.lokasi',
                    'like',
                    '%'.$request->q_lokasi.'%'
                )
                ->orWhere(
                    'b.nama_cabang',
                    'like',
                    '%'.$request->q_lokasi.'%'
                );
            });
        }

        // Pencarian stok
        if ($request->filled('q_stok')) {
            $stokBersih->whereRaw('
                (
                    COALESCE(masuk.total_masuk, 0)
                    - COALESCE(keluar.total_keluar, 0)
                    + COALESCE(penyesuaian.total_penyesuaian, 0)
                ) = ?
            ', [(int) $request->q_stok]);
        }

        // Pencarian status stok
        if ($request->filled('q_status')) {
            if ($request->q_status === 'tersedia') {
                $stokBersih->whereRaw('
                    (
                        COALESCE(masuk.total_masuk, 0)
                        - COALESCE(keluar.total_keluar, 0)
                        + COALESCE(penyesuaian.total_penyesuaian, 0)
                    ) > 0
                ');
            } elseif ($request->q_status === 'habis') {
                $stokBersih->whereRaw('
                    (
                        COALESCE(masuk.total_masuk, 0)
                        - COALESCE(keluar.total_keluar, 0)
                        + COALESCE(penyesuaian.total_penyesuaian, 0)
                    ) <= 0
                ');
            }
        }

        // ==========================================
        // 6. HASIL DAFTAR BARANG
        // ==========================================
        $barangs = $stokBersih
            ->orderBy('masuk.nama_barang')
            ->get();

        return view('databarang.kelolabarang', [
            'barangs' => $barangs,
            'title' => 'Daftar Barang',
        ]);
    }

    // ==========================================
    // PENERIMAAN BARANG
    // ==========================================

    public function penerimaanbarang()
    {
        // Supplier hanya dari cabang user,
        // kecuali Super Admin dan Admin Pusat.
        $suppliers = $this->supplierQuery()
            ->orderBy('nama_supplier')
            ->get();

        return view('databarang.penerimaanbarang', [
            'suppliers' => $suppliers,
            'title' => 'Penerimaan Barang',
        ]);
    }

    // ==========================================
    // SIMPAN PENERIMAAN BARANG
    // ==========================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',

            'kode_barang' => 'required|array|min:1',
            'nama_barang' => 'required|array',
            'kategori' => 'required|array',
            'satuan' => 'required|array',
            'lokasi' => 'required|array',
            'qty' => 'required|array',
            'harga' => 'required|array',

            'kode_barang.*' => 'required|string',
            'nama_barang.*' => 'required|string',
            'kategori.*' => 'required|string',
            'satuan.*' => 'required|string',
            'lokasi.*' => 'required|string',
            'qty.*' => 'required|integer|min:1',
            'harga.*' => 'required|numeric|min:0',
        ]);

        // Pastikan supplier sesuai cabang user
        $supplier = $this->supplierQuery()
            ->findOrFail($validated['supplier_id']);

        // Supplier wajib memiliki cabang
        abort_if(
            !$supplier->branch_id,
            422,
            'Supplier belum memiliki cabang. Silakan atur cabang supplier terlebih dahulu.'
        );

        // Simpan penerimaan dan detail barang
        DB::transaction(function () use ($validated, $supplier) {
            $penerimaan = Penerimaan::create([
                'supplier_id' => $supplier->id,
                'branch_id' => $supplier->branch_id,
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            foreach ($validated['kode_barang'] as $index => $kode) {
                $qty = $validated['qty'][$index];
                $harga = $validated['harga'][$index];

                DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'kode_barang' => $kode,
                    'nama_barang' => $validated['nama_barang'][$index],
                    'kategori' => $validated['kategori'][$index],
                    'satuan' => $validated['satuan'][$index],
                    'lokasi' => $validated['lokasi'][$index],
                    'qty' => $qty,
                    'harga' => $harga,
                    'jumlah' => $qty * $harga,
                ]);
            }
        });

        return redirect()
            ->route('databarang.penerimaan')
            ->with('success', 'Data penerimaan barang berhasil disimpan!');
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
    // UPDATE BARANG
    // ==========================================

    public function update(Request $request, $kode_barang)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string',
            'kategori' => 'required|string',
            'satuan' => 'required|string',
            'lokasi' => 'required|string',

            'qty_koreksi' => 'nullable|integer',
            'alasan' => 'nullable|string|max:255',
        ]);

        $kodeLama = $kode_barang;
        $kodeBaru = $validated['kode_barang'];
        $qtyKoreksi = (int) ($validated['qty_koreksi'] ?? 0);

        // Pastikan user memiliki cabang jika bukan user global
        if (!$this->isGlobalUser()) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');
        }

        DB::transaction(function () use (
            $validated,
            $kodeLama,
            $kodeBaru,
            $qtyKoreksi,
            $user
        ) {
            // ==========================================
            // 1. AMBIL DATA BARANG
            // ==========================================

            $barangs = $this->detailBarangQuery()
                ->where('detail_penerimaans.kode_barang', $kodeLama)
                ->lockForUpdate()
                ->get();

            if ($barangs->isEmpty()) {
                abort(404, 'Barang tidak ditemukan.');
            }

            // Pastikan seluruh penerimaan berasal dari cabang yang sama
            $branchId = $barangs->first()->branch_id
                ?? DB::table('penerimaans')
                    ->where('id', $barangs->first()->penerimaan_id)
                    ->value('branch_id');

            if (!$this->isGlobalUser() && (int) $branchId !== (int) $user->branch_id) {
                abort(403, 'Anda tidak memiliki akses ke barang ini.');
            }

            // ==========================================
            // 2. HITUNG STOK SAAT INI
            // ==========================================

            $stokMasuk = (int) $barangs->sum('qty');

            $totalKeluar = (int) DB::table('detail_pengeluarans as dp')
                ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
                ->where('dp.kode_barang', $kodeLama)
                ->where('pg.branch_id', $branchId)
                ->sum('dp.qty');

            $totalPenyesuaian = (int) DB::table('penyesuaian_stoks')
                ->where('kode_barang', $kodeLama)
                ->where('branch_id', $branchId)
                ->sum('qty_koreksi');

            $stokSekarang = $stokMasuk - $totalKeluar + $totalPenyesuaian;

            // ==========================================
            // 3. VALIDASI STOK AKHIR
            // ==========================================

            $stokAkhir = $stokSekarang + $qtyKoreksi;

            if ($stokAkhir < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages(['qty_koreksi' => "Penyesuaian tidak dapat dilakukan. Stok saat ini {$stokSekarang}, sehingga pengurangan maksimal adalah {$stokSekarang}."]);
            }

            // ==========================================
            // 4. VALIDASI PERUBAHAN KODE BARANG
            // ==========================================

            if ($kodeBaru !== $kodeLama) {
                if ($totalKeluar > 0 || $totalPenyesuaian != 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['kode_barang' => 'Kode barang tidak dapat diubah karena sudah memiliki transaksi pengeluaran atau penyesuaian stok.']);
                }

                $kodeSudahAda = $this->detailBarangQuery()
                    ->where('detail_penerimaans.kode_barang', $kodeBaru)
                    ->exists();

                if ($kodeSudahAda) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['kode_barang' => 'Kode barang sudah digunakan.']);
                }

                $kodePengeluaranAda = DB::table('detail_pengeluarans as dp')
                    ->join('pengeluarans as pg', 'dp.pengeluaran_id', '=', 'pg.id')
                    ->where('dp.kode_barang', $kodeBaru)
                    ->where('pg.branch_id', $branchId)
                    ->exists();

                if ($kodePengeluaranAda) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['kode_barang' => 'Kode barang sudah tercatat dalam transaksi pengeluaran.']);
                }

                $kodePenyesuaianAda = DB::table('penyesuaian_stoks')
                    ->where('kode_barang', $kodeBaru)
                    ->where('branch_id', $branchId)
                    ->exists();

                if ($kodePenyesuaianAda) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['kode_barang' => 'Kode barang sudah digunakan dalam penyesuaian stok.']);
                }
            }

            // ==========================================
            // 5. PERBARUI INFORMASI BARANG
            // ==========================================

            foreach ($barangs as $barang) {
                DB::table('detail_penerimaans')
                    ->where('id', $barang->id)
                    ->update([
                        'kode_barang' => $kodeBaru,
                        'nama_barang' => $validated['nama_barang'],
                        'kategori' => $validated['kategori'],
                        'satuan' => $validated['satuan'],
                        'lokasi' => $validated['lokasi'],
                    ]);
            }

            // ==========================================
            // 6. SIMPAN PENYESUAIAN STOK
            // ==========================================

            if ($qtyKoreksi != 0) {
                DB::table('penyesuaian_stoks')->insert([
                    'branch_id' => $branchId,
                    'user_id' => $user->id,
                    'nomor_penyesuaian' => 'ADJ-'.now()->format('YmdHis').'-'.$user->id,
                    'kode_barang' => $kodeBaru,
                    'nama_barang' => $validated['nama_barang'],
                    'qty_koreksi' => $qtyKoreksi,
                    'alasan' => $validated['alasan'] ?? 'Penyesuaian stok barang',
                    'tanggal' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('databarang.barang')
            ->with('success', 'Data barang dan penyesuaian stok berhasil diperbarui!');
    }

    // ==========================================
    // HAPUS BARANG
    // ==========================================

    public function destroy($kode_barang)
    {
        DB::transaction(function () use ($kode_barang) {
            // Ambil barang sesuai hak akses cabang
            $barangs = $this->detailBarangQuery()
                ->where('detail_penerimaans.kode_barang', $kode_barang)
                ->lockForUpdate()
                ->get();

            if ($barangs->isEmpty()) {
                abort(404, 'Barang tidak ditemukan.');
            }

            // Cek seluruh transaksi pengeluaran
            // Tidak dibatasi cabang agar histori tidak terlewat
            $totalKeluar = DB::table('detail_pengeluarans')
                ->where('kode_barang', $kode_barang)
                ->sum('qty');

            $totalKeluar = (int) $totalKeluar;

            // Tolak penghapusan jika pernah ada transaksi keluar
            if ($totalKeluar > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages(['barang' => "Barang tidak dapat dihapus karena sudah memiliki histori pengeluaran sebanyak {$totalKeluar} unit."]);
            }

            // Hapus data penerimaan jika belum pernah dikeluarkan
            foreach ($barangs as $barang) {
                $barang->delete();
            }
        });

        return redirect()
            ->route('databarang.barang')
            ->with('success', 'Data barang berhasil dihapus!');
    }
}
