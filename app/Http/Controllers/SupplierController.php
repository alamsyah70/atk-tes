<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // Menentukan apakah user boleh melihat seluruh cabang
    private function isGlobalUser(): bool
    {
        return auth()->user()->hasAnyRole([
            'Super Admin',
            'Admin Pusat',
        ]);
    }

    // Query supplier sesuai cabang user
    private function supplierQuery()
    {
        $query = Supplier::with('branch');

        if (!$this->isGlobalUser()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        return $query;
    }

    // READ
    public function index(Request $request)
    {
        $query = $this->supplierQuery();

        // Pencarian seluruh kolom
        if ($request->filled('q')) {
            $keyword = trim($request->q);

            $query->where(function ($q) use ($keyword) {
                $q->where('kode_supplier', 'like', "%{$keyword}%")
                    ->orWhere('nama_supplier', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%")
                    ->orWhere('no_telepon', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");

                // Pencarian nama cabang untuk user global
                if ($this->isGlobalUser()) {
                    $q->orWhereHas('branch', function ($branchQuery) use ($keyword) {
                        $branchQuery->where(
                            'nama_cabang',
                            'like',
                            "%{$keyword}%"
                        );
                    });
                }
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        $isGlobalUser = $this->isGlobalUser();

        $branches = $isGlobalUser
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        return view('supplier.index', compact(
            'suppliers',
            'branches',
            'isGlobalUser'
        ));
    }

    // CREATE
    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('supplier.create'), 403);

        $validated = $request->validate([
            'kode_supplier' => [
                'required',
                'string',
                'max:50',
                'unique:suppliers,kode_supplier',
            ],
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        // User cabang hanya boleh membuat supplier di cabangnya
        if (!$this->isGlobalUser()) {
            abort_unless(auth()->user()->branch_id, 403);

            $validated['branch_id'] = auth()->user()->branch_id;
        }

        Supplier::create($validated);

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->can('supplier.update'), 403);

        $supplier = $this->supplierQuery()->findOrFail($id);

        $validated = $request->validate([
            'kode_supplier' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'kode_supplier')
                    ->ignore($supplier->id),
            ],
            'nama_supplier' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        // Admin cabang tidak boleh memindahkan supplier ke cabang lain
        if (!$this->isGlobalUser()) {
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        $supplier->update($validated);

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    // DELETE
    public function destroy($id)
    {
        abort_unless(
            auth()->user()->can('supplier.delete'),
            403
        );

        $supplier = $this->supplierQuery()->findOrFail($id);

        // Periksa transaksi penerimaan
        $sudahDigunakan = \App\Models\Penerimaan::where(
            'supplier_id',
            $supplier->id
        )->exists();

        if ($sudahDigunakan) {
            return redirect()
                ->route('supplier.index')
                ->with(
                    'error',
                    'Supplier "'.$supplier->nama_supplier.'" tidak dapat dihapus karena sudah digunakan dalam transaksi penerimaan barang.'
                );
        }

        try {
            $supplier->delete();

            return redirect()
                ->route('supplier.index')
                ->with('success', 'Supplier berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()
                ->route('supplier.index')
                ->with(
                    'error',
                    'Supplier tidak dapat dihapus karena masih memiliki relasi dengan data transaksi lainnya.'
                );
        }
    }
}
