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
use App\Models\Barang;

class PengeluaranController extends Controller
{
    // =====================================================
    // 1. HELPER HAK AKSES DAN CABANG
    // =====================================================

    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
            'IT Cabang',
        ]);
    }

    private function branchId(): int
    {
        $user = Auth::user();

        abort_if(
            !$user->branch_id,
            403,
            'User belum memiliki cabang.'
        );

        return (int) $user->branch_id;
    }

    private function pengeluaranQuery()
    {
        $query = Pengeluaran::with([
            'branch',
            'user',
            'details',
        ]);

        if (!$this->isGlobalUser()) {
            $query->where('branch_id', $this->branchId());
        }

        return $query;
    }

    // =====================================================
    // 2. HITUNG STOK BARANG
    //
    // Stok = Penerimaan - Pengeluaran + Penyesuaian
    //
    // Mendukung:
    // - barang_id baru
    // - barang_id NULL pada data historis
    // =====================================================

    private function hitungStokBarang(
        $barang,
        int $branchId
    ): float {

        // ---------------------------------------------
        // TOTAL PENERIMAAN
        // ---------------------------------------------

        $totalMasuk = DB::table('detail_penerimaans as dp')
            ->join(
                'penerimaans as p',
                'dp.penerimaan_id',
                '=',
                'p.id'
            )
            ->where('p.branch_id', $branchId)
            ->where(function ($query) use ($barang) {
                $query->where(
                    'dp.barang_id',
                    $barang->id
                )->orWhere(function ($legacy) use ($barang) {
                    $legacy->whereNull('dp.barang_id')
                        ->where(
                            'dp.kode_barang',
                            $barang->kode_barang
                        );
                });
            })
            ->sum('dp.qty');

        // ---------------------------------------------
        // TOTAL PENGELUARAN
        // ---------------------------------------------

        $totalKeluar = DB::table('detail_pengeluarans as dpo')
            ->join(
                'pengeluarans as po',
                'dpo.pengeluaran_id',
                '=',
                'po.id'
            )
            ->where('po.branch_id', $branchId)
            ->where(function ($query) use ($barang) {
                $query->where(
                    'dpo.barang_id',
                    $barang->id
                )->orWhere(function ($legacy) use ($barang) {
                    $legacy->whereNull('dpo.barang_id')
                        ->where(
                            'dpo.kode_barang',
                            $barang->kode_barang
                        );
                });
            })
            ->sum('dpo.qty');

        // ---------------------------------------------
        // TOTAL PENYESUAIAN STOK
        //
        // Asumsi tabel penyesuaian_stoks menggunakan:
        // branch_id, kode_barang, qty_koreksi
        // ---------------------------------------------

        $totalPenyesuaian = DB::table('penyesuaian_stoks')
            ->where('branch_id', $branchId)
            ->where(
                'kode_barang',
                $barang->kode_barang
            )
            ->sum('qty_koreksi');

        return (float) $totalMasuk
            - (float) $totalKeluar
            + (float) $totalPenyesuaian;
    }

    // =====================================================
    // 3. HITUNG AVERAGE PEROLEHAN
    //
    // Prioritas:
    // 1. barangs.average_perolehan
    // 2. Hitung rata-rata tertimbang dari penerimaan
    // =====================================================

    private function hitungAveragePerolehan(
        Barang $barang,
        int $branchId
    ): float {
        $average = (float) $barang->average_perolehan;
    
        if ($average <= 0) {
            throw new \RuntimeException(
                "Average perolehan {$barang->kode_barang} belum valid."
            );
        }
    
        return $average;
    }

    // =====================================================
    // 4. INDEX
    // =====================================================

    public function index(Request $request)
    {
        abort_unless(
            Auth::user()->can('pengeluaran.view'),
            403
        );

        // ---------------------------------------------
        // DATA CABANG
        // ---------------------------------------------

        $branches = $this->isGlobalUser()
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        if ($this->isGlobalUser()) {
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);

            $branchId = $request->filled('branch_id')
                ? (int) $request->branch_id
                : null;
        } else {
            $branchId = $this->branchId();
        }

        // ---------------------------------------------
        // RIWAYAT PENGELUARAN
        // ---------------------------------------------

        $search = $request->input('search');

        $queryPengeluaran = $this->pengeluaranQuery()
            ->withCount('details');

        $queryPengeluaran->when(
            $search,
            function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'nomor_pengeluaran',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'tanggal',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'divisi',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'nama_pengambil',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'branch',
                        function ($branch) use ($search) {
                            $branch->where(
                                'nama_cabang',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'details',
                        function ($detail) use ($search) {
                            $detail->where(
                                'kode_barang',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nama_barang',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'kategori',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                });
            }
        );

        if ($this->isGlobalUser() && $branchId) {
            $queryPengeluaran->where(
                'branch_id',
                $branchId
            );
        }

        $pengeluarans = $queryPengeluaran
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // ---------------------------------------------
        // MASTER BARANG DAN STOK
        // ---------------------------------------------

        $barang = collect();

        if ($branchId) {
            $masterBarang = DB::table('barangs')
                ->where('branch_id', $branchId)
                ->orderBy('kode_barang')
                ->get();

            $barang = $masterBarang
                ->map(function ($item) use ($branchId) {
                    $item->stok = $this->hitungStokBarang(
                        $item,
                        $branchId
                    );

                    return $item;
                })
                ->filter(function ($item) {
                    return $item->stok > 0;
                })
                ->values();
        }

        // ---------------------------------------------
        // DATA AUTOCOMPLETE
        // ---------------------------------------------

        $dataBarang = $barang->map(function ($item) {
            return [
                'id'           => $item->id,
                'kode'         => $item->kode_barang,
                'nama'         => $item->nama_barang,
                'stok'         => $item->stok,
                'satuan'       => $item->satuan,
                'harga_pokok'  => (float) ($item->average_perolehan ?? 0),
            ];
        });

        return view('pengeluaran.index', compact(
            'pengeluarans',
            'branches',
            'barang',
            'branchId',
            'dataBarang',
            'search'
        ));
    }

    // =====================================================
    // 5. STORE
    // =====================================================

    public function store(Request $request)
    {
        abort_unless(
            Auth::user()->can('pengeluaran.create'),
            403
        );

        // ---------------------------------------------
        // VALIDASI INPUT
        // ---------------------------------------------

        $validated = $request->validate([
            'branch_id' => $this->isGlobalUser()
                ? 'required|exists:branches,id'
                : 'nullable',

            'divisi' => 'required|string|max:255',
            'nama_pengambil' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',

            'kode_barang' => 'required|array|min:1',
            'kode_barang.*' => 'required|string|max:50',

            'qty' => 'required|array|min:1',
            'qty.*' => 'required|integer|min:1',
        ]);

        // ---------------------------------------------
        // TENTUKAN CABANG
        // ---------------------------------------------

        $branchId = $this->isGlobalUser()
            ? (int) $validated['branch_id']
            : $this->branchId();

        $kodeBarang = array_values(
            $validated['kode_barang']
        );

        $qtyBarang = array_values(
            $validated['qty']
        );

        if (count($kodeBarang) !== count($qtyBarang)) {
            return back()
                ->withInput()
                ->withErrors([
                    'kode_barang' => 'Data barang dan qty tidak sesuai.',
                ]);
        }

        try {

            $pengeluaran = DB::transaction(function () use (
                $validated,
                $branchId,
                $kodeBarang,
                $qtyBarang
            ) {

                // -------------------------------------
                // KUNCI CABANG
                // -------------------------------------

                $branch = DB::table('branches')
                    ->where('id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (!$branch) {
                    throw new \RuntimeException(
                        'Cabang tidak ditemukan.'
                    );
                }

                // -------------------------------------
                // GENERATE NOMOR PENGELUARAN
                // KLR-MMYY-000001
                // -------------------------------------

                $urutanTerakhir = DB::table('pengeluarans')
                    ->where('branch_id', $branchId)
                    ->whereRaw(
                        "nomor_pengeluaran REGEXP ?",
                        ['^KLR-[0-9]{4}-[0-9]+$']
                    )
                    ->selectRaw("
                        COALESCE(
                            MAX(
                                CAST(
                                    SUBSTRING_INDEX(
                                        nomor_pengeluaran,
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

                $nomorPengeluaran = 'KLR-' .
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

                // -------------------------------------
                // SIMPAN HEADER
                // -------------------------------------

                $pengeluaran = Pengeluaran::create([
                    'branch_id' => $branchId,
                    'user_id' => Auth::id(),
                    'nomor_pengeluaran' => $nomorPengeluaran,
                    'divisi' => $validated['divisi'],
                    'nama_pengambil' => $validated['nama_pengambil'],
                    'tanggal' => $validated['tanggal'],
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                // -------------------------------------
                // GABUNGKAN BARANG DENGAN KODE SAMA
                // -------------------------------------

                $items = [];

                foreach ($kodeBarang as $index => $kode) {
                    $kode = trim($kode);

                    if ($kode === '') {
                        throw new \RuntimeException(
                            'Kode barang tidak boleh kosong.'
                        );
                    }

                    $items[$kode] = ($items[$kode] ?? 0)
                        + (int) $qtyBarang[$index];
                }

                // -------------------------------------
                // PROSES SETIAP BARANG
                // -------------------------------------

                foreach ($items as $kode => $qtyKeluar) {

                    // Cari master barang berdasarkan cabang
                    $masterBarang = Barang::query()
                        ->where('branch_id', $branchId)
                        ->where('kode_barang', $kode)
                        ->lockForUpdate()
                        ->get();

                    if ($masterBarang->isEmpty()) {
                        throw new \RuntimeException(
                            "Barang {$kode} tidak ditemukan di master barang cabang ini."
                        );
                    }

                    if ($masterBarang->count() > 1) {
                        throw new \RuntimeException(
                            "Kode {$kode} terdaftar lebih dari satu kali pada master barang cabang ini. Periksa master barang."
                        );
                    }

                    $dataBarang = $masterBarang->first();

                    // ---------------------------------
                    // HITUNG STOK AKTUAL
                    // ---------------------------------

                    $stokTersedia = $this->hitungStokBarang(
                        $dataBarang,
                        $branchId
                    );

                    if ($stokTersedia < 0) {
                        throw new \RuntimeException(
                            "Stok {$kode} negatif ({$stokTersedia}). Periksa transaksi dan penyesuaian stok."
                        );
                    }

                    if ($qtyKeluar > $stokTersedia) {
                        throw new \RuntimeException(
                            "Stok {$kode} tidak mencukupi. Stok tersedia: {$stokTersedia}."
                        );
                    }

                    // ---------------------------------
                    // HITUNG AVERAGE PEROLEHAN
                    // ---------------------------------

                    $hargaPokok = $this->hitungAveragePerolehan(
                        $dataBarang,
                        $branchId
                    );

                    $jumlahHargaPokok = round(
                        $qtyKeluar * $hargaPokok,
                        2
                    );

                    // ---------------------------------
                    // SIMPAN DETAIL PENGELUARAN
                    // ---------------------------------

                    DetailPengeluaran::create([
                        'pengeluaran_id' => $pengeluaran->id,

                        'barang_id' => $dataBarang->id,
                        'kode_barang' => $dataBarang->kode_barang,
                        'nama_barang' => $dataBarang->nama_barang,
                        'kategori' => $dataBarang->kategori,
                        'satuan' => $dataBarang->satuan,
                        'lokasi' => $dataBarang->lokasi,

                        'qty' => $qtyKeluar,

                        'harga_pokok' => $hargaPokok,
                        'jumlah_harga_pokok' => $jumlahHargaPokok,
                    ]);
                }

                return $pengeluaran;

            }, 3);

            return redirect()
                ->route('pengeluaran.index')
                ->with(
                    'success',
                    'Pengeluaran barang berhasil disimpan.'
                )
                ->with('pengeluaran_id', $pengeluaran->id);

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'pengeluaran' => $e->getMessage(),
                ]);
        }
    }

    // =====================================================
    // 6. PDF
    // =====================================================

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
            $pengeluaran->nomor_pengeluaran . '.pdf'
        );
    }
}
