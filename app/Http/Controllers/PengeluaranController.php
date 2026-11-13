<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DetailPengeluaran;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengeluaranController extends Controller
{
    private function isGlobalUser(): bool
    {
        return Auth::user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
        ]);
    }

    private function branchId(): int
    {
        $user = Auth::user();

        abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');

        return (int) $user->branch_id;
    }

    private function pengeluaranQuery()
    {
        $query = Pengeluaran::with(['branch', 'user', 'details']);

        if (!$this->isGlobalUser()) {
            $query->where('branch_id', $this->branchId());
        }

        return $query;
    }

    /**
     * Halaman daftar pengeluaran dan pilihan barang.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->can('pengeluaran.view'), 403);

        // =========================================
        // 1. DATA CABANG
        // =========================================
        $branches = $this->isGlobalUser()
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        if ($this->isGlobalUser()) {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);

            $branchId = $request->branch_id
                ? (int) $request->branch_id
                : null;
        } else {
            $branchId = $this->branchId();
        }

        // =========================================
        // 2. RIWAYAT PENGELUARAN
        // =========================================
        $search = $request->input('search');

        $queryPengeluaran = $this->pengeluaranQuery()
            ->withCount('details');

        $queryPengeluaran->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengeluaran', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%")
                    ->orWhere('nama_pengambil', 'like', "%{$search}%")

                    // Cari berdasarkan cabang
                    ->orWhereHas('branch', function ($branch) use ($search) {
                        $branch->where(
                            'nama_cabang',
                            'like',
                            "%{$search}%"
                        );
                    })

                    // Cari berdasarkan detail barang
                    ->orWhereHas('details', function ($detail) use ($search) {
                        $detail->where('kode_barang', 'like', "%{$search}%")
                            ->orWhere('nama_barang', 'like', "%{$search}%")
                            ->orWhere('kategori', 'like', "%{$search}%");
                    });
            });
        });

        // Filter cabang untuk Super Admin / Admin Pusat
        if ($this->isGlobalUser() && $branchId) {
            $queryPengeluaran->where('branch_id', $branchId);
        }

        $pengeluarans = $queryPengeluaran
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // =========================================
        // 3. DATA BARANG BERDASARKAN CABANG
        // =========================================
        $barang = collect();

        if ($branchId) {
            $barang = DB::table('detail_penerimaans as dp')
                ->join('penerimaans as p', 'dp.penerimaan_id', '=', 'p.id')
                ->where('p.branch_id', $branchId)
                ->select(
                    'dp.kode_barang',
                    DB::raw('MAX(dp.nama_barang) as nama_barang'),
                    DB::raw('MAX(dp.kategori) as kategori'),
                    DB::raw('MAX(dp.satuan) as satuan'),
                    DB::raw('MAX(dp.lokasi) as lokasi'),
                    DB::raw('SUM(dp.qty) as total_masuk')
                )
                ->groupBy('dp.kode_barang')
                ->get()
                ->map(function ($item) use ($branchId) {
                    $totalKeluar = DB::table('detail_pengeluarans as dp')
                        ->join('pengeluarans as p', 'dp.pengeluaran_id', '=', 'p.id')
                        ->where('p.branch_id', $branchId)
                        ->where('dp.kode_barang', $item->kode_barang)
                        ->sum('dp.qty');

                    $item->stok = (int) $item->total_masuk - (int) $totalKeluar;

                    return $item;
                })
                ->filter(fn ($item) => $item->stok > 0)
                ->values();
        }

        // =========================================
        // 4. DATA AUTOCOMPLETE BARANG
        // =========================================
        $dataBarang = $barang->map(function ($item) {
            return [
                'kode' => $item->kode_barang,
                'nama' => $item->nama_barang,
                'stok' => (int) $item->stok,
                'satuan' => $item->satuan,
            ];
        })->values();

        // =========================================
        // 5. TAMPILKAN VIEW
        // =========================================
        return view('pengeluaran.index', compact(
            'pengeluarans',
            'branches',
            'barang',
            'branchId',
            'dataBarang',
            'search'
        ));
    }

    /**
     * Simpan transaksi pengeluaran dan kurangi stok tersedia.
     */
    public function store(Request $request)
    {
        abort_unless(
            Auth::user()->can('pengeluaran.create'),
            403
        );

        $validated = $request->validate([
            'branch_id' => $this->isGlobalUser()
                ? 'required|exists:branches,id'
                : 'nullable',

            'divisi' => 'required|string|max:255',
            'nama_pengambil' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',

            'kode_barang' => 'required|array|min:1',
            'kode_barang.*' => 'required|string',
            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
        ]);

        $branchId = $this->isGlobalUser()
            ? (int) $validated['branch_id']
            : $this->branchId();

        // Pastikan jumlah kode barang dan qty sama.
        if (count($validated['kode_barang']) !== count($validated['qty'])) {
            return back()
                ->withInput()
                ->withErrors(['kode_barang' => 'Data barang tidak sesuai.']);
        }

        try {
            $pengeluaran = DB::transaction(function () use (
                $validated,
                $branchId
            ) {
                $nomor = 'PG-'.now()->format('Ymd')
                    .'-'.strtoupper(Str::random(4));

                $pengeluaran = Pengeluaran::create([
                    'branch_id' => $branchId,
                    'user_id' => Auth::id(),
                    'nomor_pengeluaran' => $nomor,
                    'divisi' => $validated['divisi'],
                    'nama_pengambil' => $validated['nama_pengambil'],
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                // Gabungkan jika kode barang yang sama dikirim lebih dari sekali.
                $items = [];

                foreach ($validated['kode_barang'] as $index => $kode) {
                    $items[$kode] = ($items[$kode] ?? 0)
                        + (int) $validated['qty'][$index];
                }

                foreach ($items as $kode => $qtyKeluar) {
                    // Kunci baris penerimaan untuk mengurangi risiko
                    // dua transaksi mengeluarkan stok yang sama bersamaan.
                    $penerimaanItems = DB::table('detail_penerimaans as dp')
                        ->join(
                            'penerimaans as p',
                            'dp.penerimaan_id',
                            '=',
                            'p.id'
                        )
                        ->where('p.branch_id', $branchId)
                        ->where('dp.kode_barang', $kode)
                        ->select('dp.*')
                        ->lockForUpdate()
                        ->get();

                    if ($penerimaanItems->isEmpty()) {
                        throw new \RuntimeException("Barang dengan kode {$kode} tidak ditemukan di cabang ini.");
                    }

                    $totalMasuk = (int) $penerimaanItems->sum('qty');

                    $totalKeluar = (int) DB::table('detail_pengeluarans as dpo')
                        ->join(
                            'pengeluarans as po',
                            'dpo.pengeluaran_id',
                            '=',
                            'po.id'
                        )
                        ->where('po.branch_id', $branchId)
                        ->where('dpo.kode_barang', $kode)
                        ->sum('dpo.qty');

                    $stokTersedia = $totalMasuk - $totalKeluar;

                    if ($qtyKeluar > $stokTersedia) {
                        throw new \RuntimeException("Stok {$kode} tidak mencukupi. Stok tersedia: {$stokTersedia}.");
                    }

                    $dataBarang = $penerimaanItems->first();

                    DetailPengeluaran::create([
                        'pengeluaran_id' => $pengeluaran->id,
                        'kode_barang' => $dataBarang->kode_barang,
                        'nama_barang' => $dataBarang->nama_barang,
                        'kategori' => $dataBarang->kategori,
                        'satuan' => $dataBarang->satuan,
                        'lokasi' => $dataBarang->lokasi,
                        'qty' => $qtyKeluar,
                    ]);
                }

                return $pengeluaran;
            });

            return redirect()
                ->route('pengeluaran.index')
                ->with('success', 'Pengeluaran barang berhasil disimpan.')
                ->with('pengeluaran_id', $pengeluaran->id);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['pengeluaran' => $e->getMessage()]);
        }
    }

    /**
     * Cetak bukti pengeluaran PDF.
     */
    public function pdf($id)
    {
        abort_unless(
            Auth::user()->can('pengeluaran.view'),
            403
        );

        $pengeluaran = $this->pengeluaranQuery()
            ->findOrFail($id);

        $pdf = Pdf::loadView(
            'pengeluaran.pdf',
            compact('pengeluaran')
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            $pengeluaran->nomor_pengeluaran.'.pdf'
        );
    }
}
