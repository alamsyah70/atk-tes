<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Branch;
use App\Models\StokOpname;
use App\Models\DetailStokOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class StokOpnameController extends Controller
{
    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
            'IT Cabang',
        ]);
    }

    private function resolveBranchId(Request $request): int
    {
        $user = Auth::user();

        if ($this->isGlobalUser()) {
            $request->validate([
                'branch_id' => 'required|exists:branches,id',
            ]);

            return (int) $request->branch_id;
        }

        abort_if(
            !$user->branch_id,
            403,
            'User belum memiliki cabang.'
        );

        return (int) $user->branch_id;
    }

    /**
     * Mengambil stok seluruh barang berdasarkan transaksi.
     * Mendukung detail lama yang barang_id-nya masih NULL.
     */
    private function getStockMap(int $branchId): array
    {
        // TOTAL PENERIMAAN
        $masuk = DB::table('detail_penerimaans as d')
            ->join('penerimaans as p', 'p.id', '=', 'd.penerimaan_id')
            ->join('barangs as b', function ($join) {
                $join->on('b.branch_id', '=', 'p.branch_id')
                    ->where(function ($query) {
                        $query->whereColumn('d.barang_id', 'b.id')
                            ->orWhere(function ($legacy) {
                                $legacy->whereNull('d.barang_id')
                                    ->whereColumn('d.kode_barang', 'b.kode_barang');
                            });
                    });
            })
            ->where('p.branch_id', $branchId)
            ->select('b.id')
            ->selectRaw('SUM(d.qty) as total')
            ->groupBy('b.id')
            ->pluck('total', 'id')
            ->toArray();

        // TOTAL PENGELUARAN
        $keluar = DB::table('detail_pengeluarans as d')
            ->join('pengeluarans as p', 'p.id', '=', 'd.pengeluaran_id')
            ->join('barangs as b', function ($join) {
                $join->on('b.branch_id', '=', 'p.branch_id')
                    ->where(function ($query) {
                        $query->whereColumn('d.barang_id', 'b.id')
                            ->orWhere(function ($legacy) {
                                $legacy->whereNull('d.barang_id')
                                    ->whereColumn('d.kode_barang', 'b.kode_barang');
                            });
                    });
            })
            ->where('p.branch_id', $branchId)
            ->select('b.id')
            ->selectRaw('SUM(d.qty) as total')
            ->groupBy('b.id')
            ->pluck('total', 'id')
            ->toArray();

        // TOTAL PENYESUAIAN
        $koreksi = DB::table('penyesuaian_stoks as ps')
            ->join('barangs as b', function ($join) {
                $join->on('b.branch_id', '=', 'ps.branch_id')
                    ->where(function ($query) {
                        $query->whereColumn('ps.barang_id', 'b.id')
                            ->orWhere(function ($legacy) {
                                $legacy->whereNull('ps.barang_id')
                                    ->whereColumn('ps.kode_barang', 'b.kode_barang');
                            });
                    });
            })
            ->where('ps.branch_id', $branchId)
            ->select('b.id')
            ->selectRaw('SUM(ps.qty_koreksi) as total')
            ->groupBy('b.id')
            ->pluck('total', 'id')
            ->toArray();

        $hasil = [];

        $barangs = Barang::where('branch_id', $branchId)
            ->select('id')
            ->get();

        foreach ($barangs as $barang) {
            $id = $barang->id;

            $hasil[$id] =
                (int) ($masuk[$id] ?? 0)
                - (int) ($keluar[$id] ?? 0)
                + (int) ($koreksi[$id] ?? 0);
        }

        return $hasil;
    }

    /**
     * DAFTAR SESI OPNAME
     */
    public function index()
    {
        $user = Auth::user();
        $isGlobal = $this->isGlobalUser();

        $branches = $isGlobal
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        $query = StokOpname::with(['branch', 'user'])
            ->withCount('details')
            ->latest('id');

        if (!$isGlobal) {
            abort_if(!$user->branch_id, 403, 'User belum memiliki cabang.');

            $query->where('branch_id', $user->branch_id);
        }

        $stokOpnames = $query->paginate(10)->withQueryString();

        return view('stok-opname.index', compact(
            'stokOpnames',
            'branches',
            'isGlobal'
        ));
    }

    /**
     * MEMBUAT SESI OPNAME DAN SNAPSHOT STOK
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'branch_id' => $this->isGlobalUser()
                ? 'required|exists:branches,id'
                : 'nullable',
        ]);

        $branchId = $this->isGlobalUser()
            ? (int) $request->branch_id
            : (int) Auth::user()->branch_id;

        abort_if(!$branchId, 403, 'Cabang tidak valid.');

        return DB::transaction(function () use (
            $request,
            $branchId
        ) {
            $snapshotAt = now();

            $barangs = Barang::where('branch_id', $branchId)
                ->orderBy('kode_barang')
                ->get();

            if ($barangs->isEmpty()) {
                throw ValidationException::withMessages([
                    'branch_id' => 'Belum ada master barang pada cabang ini.',
                ]);
            }

            $stockMap = $this->getStockMap($branchId);

            $opname = StokOpname::create([
                'nomor_opname' => 'OPN-' .
                    $branchId . '-' .
                    now()->format('YmdHis'),

                'branch_id' => $branchId,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'status' => 'draft',
                'snapshot_at' => $snapshotAt,
                'user_id' => Auth::id(),
            ]);

            $details = [];

            foreach ($barangs as $barang) {
                $details[] = [
                    'stok_opname_id' => $opname->id,
                    'barang_id' => $barang->id,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'stok_sistem' => $stockMap[$barang->id] ?? 0,
                    'stok_fisik' => null,
                    'selisih' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DetailStokOpname::insert($details);

            return redirect()
                ->route('stok-opname.show', $opname->id)
                ->with('success', 'Sesi stok opname berhasil dibuat. Silakan isi stok fisik.');
        });
    }

    /**
     * DETAIL SESI OPNAME
     */
    public function show(StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        $stokOpname->load([
            'branch',
            'user',
            'details' => function ($query) {
                $query->orderBy('kode_barang');
            },
        ]);

        $totalBarang = $stokOpname->details->count();

        $sudahDihitung = $stokOpname->details
            ->whereNotNull('stok_fisik')
            ->count();

        $totalSelisih = $stokOpname->details
            ->whereNotNull('selisih')
            ->where('selisih', '!=', 0)
            ->count();

        return view('stok-opname.show', compact(
            'stokOpname',
            'totalBarang',
            'sudahDihitung',
            'totalSelisih'
        ));
    }

    /**
     * SIMPAN INPUT STOK FISIK
     */
    public function update(Request $request, StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        abort_if(
            $stokOpname->status !== 'draft',
            403,
            'Stok opname sudah selesai dan tidak dapat diubah.'
        );

        $request->validate([
            'stok_fisik' => 'required|array',
            'stok_fisik.*' => 'required|integer|min:0',
        ], [
            'stok_fisik.*.required' => 'Semua stok fisik wajib diisi.',
            'stok_fisik.*.integer' => 'Stok fisik harus berupa angka bulat.',
            'stok_fisik.*.min' => 'Stok fisik tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($request, $stokOpname) {

            $opname = StokOpname::whereKey($stokOpname->id)
                ->lockForUpdate()
                ->firstOrFail();
        
            if ($opname->status !== 'draft') {
                throw ValidationException::withMessages([
                    'opname' => 'Stok opname sudah tidak dapat diubah.',
                ]);
            }
        
            $details = DetailStokOpname::where(
                'stok_opname_id',
                $opname->id
            )->lockForUpdate()->get();

            foreach ($details as $detail) {
                $key = $detail->id;

                if (!array_key_exists($key, $request->stok_fisik)) {
                    throw ValidationException::withMessages([
                        'stok_fisik' => 'Semua barang harus diisi.',
                    ]);
                }

                $fisik = (int) $request->stok_fisik[$key];

                $detail->update([
                    'stok_fisik' => $fisik,
                    'selisih' => $fisik - $detail->stok_sistem,
                ]);
            }
        });

        return back()->with(
            'success',
            'Hasil penghitungan stok fisik berhasil disimpan.'
        );
    }

    /**
     * FINALISASI OPNAME
     */
    public function finalize(StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        DB::transaction(function () use ($stokOpname) {

            // Kunci data opname agar tidak difinalisasi bersamaan
            $opname = StokOpname::whereKey($stokOpname->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan masih berstatus draft
            if ($opname->status !== 'draft') {
                throw ValidationException::withMessages([
                    'opname' => 'Stok opname sudah pernah difinalisasi.',
                ]);
            }

            // Ambil seluruh detail barang
            $details = DetailStokOpname::where(
                'stok_opname_id',
                $opname->id
            )
                ->lockForUpdate()
                ->get();

            if ($details->isEmpty()) {
                throw ValidationException::withMessages([
                    'opname' => 'Detail barang tidak ditemukan.',
                ]);
            }

            // Pastikan semua barang sudah dihitung
            if ($details->contains(fn ($detail) => $detail->stok_fisik === null)) {
                throw ValidationException::withMessages([
                    'opname' => 'Masih ada barang yang belum dihitung.',
                ]);
            }

            // Nomor urut untuk setiap penyesuaian
            $nomorUrut = 1;

            foreach ($details as $detail) {

                // Hitung selisih stok fisik dengan stok sistem
                $selisih = (int) $detail->stok_fisik
                    - (int) $detail->stok_sistem;

                // Jika tidak ada selisih, tidak perlu membuat penyesuaian
                if ($selisih === 0) {
                    $detail->update([
                        'selisih' => 0,
                    ]);

                    continue;
                }

                // Nomor penyesuaian harus unik untuk setiap barang
                $nomorPenyesuaian = $opname->nomor_opname
                    . '-'
                    . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

                // Masukkan selisih ke tabel penyesuaian_stoks
                DB::table('penyesuaian_stoks')->insert([
                    'branch_id' => $opname->branch_id,
                    'barang_id' => $detail->barang_id,
                    'user_id' => Auth::id(),
                    'stok_opname_id' => $opname->id,
                
                    'nomor_penyesuaian' => $nomorPenyesuaian,
                
                    'kode_barang' => $detail->kode_barang,
                    'nama_barang' => $detail->nama_barang,
                    'qty_koreksi' => $selisih,
                
                    'alasan' => 'Stok Opname ' . $opname->nomor_opname,
                
                    'tanggal' => $opname->tanggal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Simpan nilai selisih pada detail opname
                $detail->update([
                    'selisih' => $selisih,
                ]);

                $nomorUrut++;
            }

            // Tandai opname sudah selesai
            $opname->update([
                'status' => 'selesai',
                'finalized_at' => now(),
            ]);
        });

        return redirect()
            ->route('stok-opname.show', $stokOpname->id)
            ->with(
                'success',
                'Stok opname berhasil difinalisasi. Selisih sudah masuk ke penyesuaian stok.'
            );
    }

    /**
     * PEMBATASAN AKSES CABANG
     */
    private function authorizeBranch(StokOpname $stokOpname): void
    {
        $user = Auth::user();

        if (!$this->isGlobalUser()) {
            abort_if(
                !$user->branch_id ||
                (int) $stokOpname->branch_id !== (int) $user->branch_id,
                403,
                'Anda tidak memiliki akses ke stok opname cabang ini.'
            );
        }
    }

    public function exportPdf(StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        $stokOpname->load(['branch', 'user']);

        $details = DetailStokOpname::where(
            'stok_opname_id',
            $stokOpname->id
        )
            ->orderBy('kode_barang')
            ->get();

        $totalBarang = $details->count();

        $totalSesuai = $details->where('selisih', 0)->count();

        $totalSelisih = $details
            ->filter(fn ($item) => (int) $item->selisih !== 0)
            ->count();

        $totalLebih = $details
            ->filter(fn ($item) => (int) $item->selisih > 0)
            ->count();

        $totalKurang = $details
            ->filter(fn ($item) => (int) $item->selisih < 0)
            ->count();

        $pdf = Pdf::loadView('stok-opname.export-pdf', compact(
            'stokOpname',
            'details',
            'totalBarang',
            'totalSesuai',
            'totalSelisih',
            'totalLebih',
            'totalKurang'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            'Hasil-Stok-Opname-' . $stokOpname->nomor_opname . '.pdf'
        );
    }

    public function exportExcel(StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        $details = DetailStokOpname::where(
            'stok_opname_id',
            $stokOpname->id
        )
            ->orderBy('kode_barang')
            ->get();

        $filename = 'Hasil-Stok-Opname-' .
            $stokOpname->nomor_opname . '.csv';

        return response()->streamDownload(function () use (
            $stokOpname,
            $details
        ) {
            $output = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 terbaca dengan baik oleh Excel
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, ['HASIL STOK OPNAME']);
            fputcsv($output, ['Nomor Opname', $stokOpname->nomor_opname]);
            fputcsv($output, ['Cabang', $stokOpname->branch->nama_cabang ?? '-']);
            fputcsv($output, [
                'Tanggal',
                $stokOpname->tanggal->format('d-m-Y'),
            ]);
            fputcsv($output, ['Keterangan', $stokOpname->keterangan ?? '-']);
            fputcsv($output, []);

            fputcsv($output, [
                'No',
                'Kode Barang',
                'Nama Barang',
                'Stok Sistem',
                'Stok Fisik',
                'Selisih',
                'Keterangan',
            ]);

            foreach ($details as $index => $detail) {
                $selisih = (int) $detail->selisih;

                $keterangan = $detail->stok_fisik === null
                    ? 'Belum dihitung'
                    : ($selisih === 0 ? 'Sesuai' : 'Selisih');

                fputcsv($output, [
                    $index + 1,
                    $detail->kode_barang,
                    $detail->nama_barang,
                    $detail->stok_sistem,
                    $detail->stok_fisik,
                    $detail->selisih,
                    $keterangan,
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function printSheet(StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        $stokOpname->load(['branch', 'user']);

        $details = DetailStokOpname::where(
            'stok_opname_id',
            $stokOpname->id
        )
            ->orderBy('kode_barang')
            ->get();

        return view('stok-opname.print-sheet', compact(
            'stokOpname',
            'details'
        ));
    }

    
    public function cancel(Request $request, StokOpname $stokOpname)
    {
        $this->authorizeBranch($stokOpname);

        // Pembatalan hanya untuk Super Admin dan Admin Pusat
        abort_unless(
            $this->isGlobalUser(),
            403,
            'Hanya Super Admin dan Admin Pusat yang dapat membatalkan opname.'
        );

        $request->validate([
            'alasan_pembatalan' => 'required|string|min:5|max:1000',
        ]);

        DB::transaction(function () use ($request, $stokOpname) {

            $opname = StokOpname::whereKey($stokOpname->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($opname->status !== 'selesai') {
                throw ValidationException::withMessages([
                    'opname' => 'Hanya opname yang sudah selesai yang dapat dibatalkan.',
                ]);
            }

            if ($opname->dibatalkan_at !== null) {
                throw ValidationException::withMessages([
                    'opname' => 'Stok opname ini sudah pernah dibatalkan.',
                ]);
            }

            // Ambil transaksi penyesuaian asli milik opname
            $penyesuaianAsli = DB::table('penyesuaian_stoks')
                ->whereNull('pembalikan_dari_id')
                ->where(function ($query) use ($opname) {
                    $query->where('stok_opname_id', $opname->id)
                        ->orWhere(function ($legacy) use ($opname) {
                            $legacy->whereNull('stok_opname_id')
                                ->where(
                                    'alasan',
                                    'Stok Opname ' . $opname->nomor_opname
                                );
                        });
                })
                ->lockForUpdate()
                ->get();

            // Hitung jumlah detail yang seharusnya memiliki transaksi koreksi
            $jumlahSeharusnya = DetailStokOpname::where(
                'stok_opname_id',
                $opname->id
            )
                ->whereNotNull('selisih')
                ->where('selisih', '!=', 0)
                ->count();

            // Pastikan transaksi asli tidak hilang atau kurang
            if ($penyesuaianAsli->count() !== $jumlahSeharusnya) {
                throw ValidationException::withMessages([
                    'opname' => 'Jumlah transaksi penyesuaian tidak sesuai dengan hasil opname. Pembatalan dihentikan untuk mencegah kesalahan stok.',
                ]);
            }

            foreach ($penyesuaianAsli as $asli) {

                // Cegah transaksi asli dibalik lebih dari satu kali
                $sudahDibalik = DB::table('penyesuaian_stoks')
                    ->where('pembalikan_dari_id', $asli->id)
                    ->exists();

                if ($sudahDibalik) {
                    throw ValidationException::withMessages([
                        'opname' => 'Transaksi penyesuaian ' .
                            $asli->nomor_penyesuaian .
                            ' sudah pernah dibalik.',
                    ]);
                }

                // Nomor transaksi pembalik harus unik
                $nomorPembalik = $opname->nomor_opname
                    . '-REV-'
                    . $asli->id;

                DB::table('penyesuaian_stoks')->insert([
                    'branch_id' => $asli->branch_id,
                    'barang_id' => $asli->barang_id,
                    'user_id' => Auth::id(),

                    'stok_opname_id' => $opname->id,
                    'pembalikan_dari_id' => $asli->id,

                    'nomor_penyesuaian' => $nomorPembalik,

                    'kode_barang' => $asli->kode_barang,
                    'nama_barang' => $asli->nama_barang,

                    // Membalik nilai transaksi asli
                    'qty_koreksi' => -1 * (int) $asli->qty_koreksi,

                    'alasan' => 'Pembatalan Stok Opname '
                        . $opname->nomor_opname
                        . ' | Membalik transaksi '
                        . $asli->nomor_penyesuaian,

                    'tanggal' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Simpan status dan riwayat pembatalan
            DB::table('stok_opnames')
                ->where('id', $opname->id)
                ->update([
                    'status' => 'dibatalkan',
                    'dibatalkan_at' => now(),
                    'dibatalkan_oleh' => Auth::id(),
                    'alasan_pembatalan' => $request->alasan_pembatalan,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('stok-opname.show', $stokOpname->id)
            ->with(
                'success',
                'Stok opname berhasil dibatalkan. Transaksi pembalik telah dibuat dan riwayat tetap tersimpan.'
            );
    }



}
