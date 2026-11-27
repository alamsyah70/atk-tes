<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Branch;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class BarangController extends Controller
{
    /**
     * Memeriksa hak akses berdasarkan permission.
     */
    private function authorizeBarang(string $permission): void
    {
        abort_unless(
            auth()->check() && auth()->user()->can($permission),
            403,
            'Anda tidak memiliki hak akses.'
        );
    }

    /**
     * Menentukan apakah user merupakan pengguna global.
     * IT Cabang tidak termasuk pengguna global.
     */
    private function isGlobal(): bool
    {
        return auth()->user()->isGlobalUser();
    }

    /**
     * Query barang sesuai cakupan cabang pengguna.
     */
    private function barangQuery()
    {
        $query = Barang::query()->with('branch');

        if (!$this->isGlobal()) {
            $branchId = auth()->user()->branch_id;

            abort_if(
                !$branchId,
                403,
                'Akun Anda belum memiliki cabang.'
            );

            $query->where('branch_id', $branchId);
        }

        return $query;
    }

    /**
     * Menentukan branch_id untuk penyimpanan.
     */
    private function resolveBranchId(Request $request): int
    {
        if ($this->isGlobal()) {
            $request->validate([
                'branch_id' => ['required', 'integer', 'exists:branches,id'],
            ]);

            return (int) $request->branch_id;
        }

        $branchId = auth()->user()->branch_id;

        abort_if(
            !$branchId,
            403,
            'Akun Anda belum memiliki cabang.'
        );

        return (int) $branchId;
    }

    /**
     * Daftar Master Barang.
     */

    
    
    
public function index(Request $request)
{
    $user = auth()->user();

    $isGlobalUser = $user->hasAnyRole([
        'Super Admin',
        'Admin Pusat',
    ]);

    $query = Barang::with('branch');

    // ==========================================
    // 1. FILTER CABANG
    // ==========================================
    if (!$isGlobalUser) {
        abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');

        $query->where('branch_id', $user->branch_id);
    } elseif ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }

    // ==========================================
    // 2. PENCARIAN
    // ==========================================
    if ($request->filled('q')) {
        $search = trim($request->q);

        $query->where(function ($q) use ($search) {
            $q->where('kode_barang', 'like', "%{$search}%")
                ->orWhere('nama_barang', 'like', "%{$search}%")
                ->orWhere('kategori', 'like', "%{$search}%")
                ->orWhere('satuan', 'like', "%{$search}%")
                ->orWhere('lokasi', 'like', "%{$search}%");
        });
    }

    // ==========================================
    // 3. FILTER KATEGORI, SATUAN, DAN LOKASI
    // ==========================================
    if ($request->filled('kategori')) {
        $query->where('kategori', $request->kategori);
    }

    if ($request->filled('satuan')) {
        $query->where('satuan', $request->satuan);
    }

    if ($request->filled('lokasi')) {
        $query->where('lokasi', $request->lokasi);
    }

    // ==========================================
    // 4. AMBIL DATA BARANG
    // ==========================================
    $barangs = $query
        ->orderBy('nama_barang')
        ->get();

    // ==========================================
    // 5. HITUNG STOK DAN HARGA SETIAP BARANG
    // ==========================================
    $barangs->transform(function ($barang) {

        // TOTAL PENERIMAAN
        $penerimaan = DB::table('detail_penerimaans as dp')
            ->join('penerimaans as p', 'p.id', '=', 'dp.penerimaan_id')
            ->where('p.branch_id', $barang->branch_id)
            ->where(function ($q) use ($barang) {
                $q->where('dp.barang_id', $barang->id)
                    ->orWhere(function ($legacy) use ($barang) {
                        $legacy->whereNull('dp.barang_id')
                            ->where('dp.kode_barang', $barang->kode_barang);
                    });
            });

        $totalMasuk = (float) (clone $penerimaan)->sum('dp.qty');

        // HARGA RATA-RATA TERTIMBANG PENERIMAAN
        $totalQtyHarga = (float) (clone $penerimaan)
            ->selectRaw('COALESCE(SUM(dp.qty * dp.harga), 0) as total')
            ->value('total');

        $hargaRataRataPenerimaan = $totalMasuk > 0
            ? $totalQtyHarga / $totalMasuk
            : 0;

        // TOTAL PENGELUARAN
        $totalKeluar = (float) DB::table('detail_pengeluarans as dp')
            ->join('pengeluarans as p', 'p.id', '=', 'dp.pengeluaran_id')
            ->where('p.branch_id', $barang->branch_id)
            ->where(function ($q) use ($barang) {
                $q->where('dp.barang_id', $barang->id)
                    ->orWhere(function ($legacy) use ($barang) {
                        $legacy->whereNull('dp.barang_id')
                            ->where('dp.kode_barang', $barang->kode_barang);
                    });
            })
            ->sum('dp.qty');

        // TOTAL PENYESUAIAN
        $totalPenyesuaian = (float) DB::table('penyesuaian_stoks')
            ->where('branch_id', $barang->branch_id)
            ->where('kode_barang', $barang->kode_barang)
            ->sum('qty_koreksi');

        // STOK AKHIR
        $stokAkhir = $totalMasuk - $totalKeluar + $totalPenyesuaian;

        // HARGA RATA-RATA
        $hargaRataRata = (float) $barang->average_perolehan;

        if ($hargaRataRata <= 0) {
            $hargaRataRata = $hargaRataRataPenerimaan;
        }

        // NILAI SALDO
        $nilaiSaldo = $stokAkhir * $hargaRataRata;

        $barang->stok = $stokAkhir;
        $barang->harga_rata_rata = $hargaRataRata;
        $barang->nilai_saldo = $nilaiSaldo;

        return $barang;
    });

    // ==========================================
    // 6. FILTER NILAI SALDO
    // ==========================================
    if ($request->saldo === 'minus') {
        $barangs = $barangs->filter(function ($barang) {
            return $barang->nilai_saldo < 0;
        });
    } elseif ($request->saldo === 'nol') {
        $barangs = $barangs->filter(function ($barang) {
            return $barang->nilai_saldo == 0;
        });
    } elseif ($request->saldo === 'positif') {
        $barangs = $barangs->filter(function ($barang) {
            return $barang->nilai_saldo > 0;
        });
    }

    // ==========================================
    // 7. PAGINATION MANUAL
    // ==========================================
    $perPage = 10;
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

    $currentItems = $barangs
        ->slice(($currentPage - 1) * $perPage, $perPage)
        ->values();

    $barangs = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $barangs->count(),
        $perPage,
        $currentPage,
        [
            'path' => $request->url(),
            'query' => $request->query(),
        ]
    );

    // ==========================================
    // 8. DAFTAR CABANG
    // ==========================================
    $branches = $isGlobalUser
        ? Branch::orderBy('nama_cabang')->get()
        : collect();

    // ==========================================
    // 9. PILIHAN KATEGORI
    // ==========================================
    $kategoriOptions = [
        'Alat Tulis Kantor',
        'Kertas dan Buku',
        'Perlengkapan Komputer',
        'Perangkat Komputer',
        'Aksesoris Komputer',
        'Peralatan Elektronik',
        'Perlengkapan Printer',
        'Tinta dan Toner',
        'Perlengkapan Arsip',
        'Perlengkapan Kantor',
        'Peralatan Kebersihan',
        'Perlengkapan Rumah Tangga',
        'Peralatan Listrik',
        'Perlengkapan Rapat',
        'Perlengkapan Keamanan',
        'Perlengkapan Operasional',
        'Peralatan Kerja',
        'Furniture dan Mebel',
        'Perlengkapan Kesehatan',
        'Lainnya',
    ];

    // ==========================================
    // 10. PILIHAN SATUAN
    // ==========================================
    $satuanOptions = [
        'Pcs', 'Unit', 'Buah', 'Biji', 'Set', 'Paket',
        'Box', 'Dus', 'Rim', 'Pack', 'Lusin', 'Gross',
        'Lembar', 'Helai', 'Batang', 'Roll', 'Meter',
        'Centimeter', 'Kilogram', 'Gram', 'Liter',
        'Mililiter', 'Botol', 'Kaleng', 'Tube', 'Pasang',
        'Karung', 'Sak', 'Karton', 'Lainnya',
    ];

    // ==========================================
    // 11. PILIHAN LOKASI DARI MASTER BARANG
    // ==========================================
    $lokasiOptions = Barang::query()
        ->when(!$isGlobalUser, function ($q) use ($user) {
            $q->where('branch_id', $user->branch_id);
        })
        ->when($isGlobalUser && $request->filled('branch_id'), function ($q) use ($request) {
            $q->where('branch_id', $request->branch_id);
        })
        ->whereNotNull('lokasi')
        ->where('lokasi', '!=', '')
        ->distinct()
        ->orderBy('lokasi')
        ->pluck('lokasi');

    return view('barangs.index', compact(
        'barangs',
        'isGlobalUser',
        'branches',
        'kategoriOptions',
        'satuanOptions',
        'lokasiOptions'
    ));
}


    /**
     * Form tambah barang.
     */
    public function create()
    {
        $this->authorizeBarang('barang.create');

        $branches = $this->isGlobal()
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        return view('barangs.create', compact('branches'));
    }

    /**
     * Simpan barang baru.
     */
    public function store(Request $request)
    {
        $this->authorizeBarang('barang.create');

        $branchId = $this->resolveBranchId($request);

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')
                    ->where(fn ($query) => $query->where('branch_id', $branchId)),
            ],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'lokasi' => ['nullable', 'string', 'max:100'],
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan di cabang ini.',
        ]);

        $validated['branch_id'] = $branchId;

        $barang = Barang::create($validated);

        AuditLogger::log(
            'create',
            'master_barang',
            "Menambahkan master barang {$barang->kode_barang} - {$barang->nama_barang}.",
            $barang,
            null,
            $barang->only([
                'branch_id',
                'kode_barang',
                'nama_barang',
                'kategori',
                'satuan',
                'lokasi',
            ]),
            $barang->branch_id
        );

        return redirect()
            ->route('barangs.index')
            ->with('success', 'Master barang berhasil ditambahkan.');
    }

    /**
     * Form edit barang.
     */
    public function edit(Barang $barang)
    {
        $this->authorizeBarang('barang.update');

        $barang = $this->barangQuery()->findOrFail($barang->id);

        $branches = $this->isGlobal()
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        return view('barangs.edit', compact('barang', 'branches'));
    }

    /**
     * Perbarui barang.
     */

    /**
     * Memperbarui barang melalui modal.
     */
    public function update(Request $request, Barang $barang)
    {
        $this->authorizeBarang('barang.update');

        $barang = $this->barangQuery()->findOrFail($barang->id);

        $branchId = $this->isGlobal()
            ? (int) $request->input('branch_id')
            : (int) auth()->user()->branch_id;

        if (!$branchId) {
            abort(403, 'Cabang belum ditentukan.');
        }

        if ($this->isGlobal()) {
            $request->validate([
                'branch_id' => ['required', 'exists:branches,id'],
            ]);
        }

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')
                    ->where(fn ($query) => $query->where('branch_id', $branchId))
                    ->ignore($barang->id),
            ],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'alasan' => 'nullable|string|max:255',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan di cabang ini.',
        ]);

        $validated['branch_id'] = $branchId;

        $oldValues = $barang->only([
            'branch_id',
            'kode_barang',
            'nama_barang',
            'kategori',
            'satuan',
            'lokasi',
        ]);

        $barang->update($validated);

        $newValues = $barang->only([
            'branch_id',
            'kode_barang',
            'nama_barang',
            'kategori',
            'satuan',
            'lokasi',
        ]);

        AuditLogger::log(
            'update',
            'master_barang',
            "Memperbarui master barang {$barang->kode_barang} - {$barang->nama_barang}.",
            $barang,
            $oldValues,
            $newValues,
            $barang->branch_id
        );

        return redirect()
            ->route('barangs.index', $request->query())
            ->with('success', 'Master barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang melalui modal.
     */
    public function destroy(Request $request, Barang $barang)
    {
        $this->authorizeBarang('barang.delete');

        $barang = $this->barangQuery()->findOrFail($barang->id);

        if (
            $barang->detailPenerimaans()->exists() ||
            $barang->detailPengeluarans()->exists()
        ) {
            return redirect()
                ->route('barangs.index', $request->query())
                ->with('error', 'Barang tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        $oldValues = $barang->only([
            'branch_id',
            'kode_barang',
            'nama_barang',
            'kategori',
            'satuan',
            'lokasi',
        ]);
        
        $branchId = $barang->branch_id;
        
        AuditLogger::log(
            'delete',
            'master_barang',
            "Menghapus master barang {$barang->kode_barang} - {$barang->nama_barang}.",
            $barang,
            $oldValues,
            null,
            $branchId
        );

        $barang->delete();

        return redirect()
            ->route('barangs.index', $request->query())
            ->with('success', 'Master barang berhasil dihapus.');
    }


    /**
     * Pencarian barang untuk autocomplete transaksi.
     */
    public function search(Request $request)
    {
        $this->authorizeBarang('barang.view');

        if ($this->isGlobal()) {
            $validated = $request->validate([
                'branch_id' => ['required', 'integer', 'exists:branches,id'],
            ]);

            $branchId = (int) $validated['branch_id'];
        } else {
            $branchId = auth()->user()->branch_id;

            abort_if(
                !$branchId,
                403,
                'Akun Anda belum memiliki cabang.'
            );
        }

        $keyword = trim((string) $request->query('q', ''));

        if ($keyword === '') {
            return response()->json([]);
        }

        $barangs = Barang::query()
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($keyword) {
                $query->where('kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('nama_barang', 'like', "%{$keyword}%");
            })
            ->orderBy('nama_barang')
            ->limit(10)
            ->get([
                'id',
                'kode_barang',
                'nama_barang',
                'kategori',
                'satuan',
                'lokasi',
            ]);

        return response()->json($barangs);
    }

    public function cekKodeBarang(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $exists = Barang::where('kode_barang', $request->kode_barang)
            ->where('branch_id', $request->branch_id)
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists
                ? 'Kode barang sudah digunakan di cabang ini.'
                : 'Kode barang tersedia.'
        ]);
    }
}
