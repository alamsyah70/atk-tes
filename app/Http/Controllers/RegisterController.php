<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $query = User::with(['roles', 'branch']);

        if ($request->filled('q_user')) {
            $query->where('user', 'like', '%' . $request->q_user . '%');
        }

        if ($request->filled('q_role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q_role . '%');
            });
        }

        if ($request->filled('q_cabang')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('nama_cabang', 'like', '%' . $request->q_cabang . '%');
            });
        }

        return view('Register.index', [
            'title' => 'Daftar User',
            'users' => $query->orderBy('id', 'desc')->paginate(10)->withQueryString(),
            'branches' => Branch::all(),
            'roles' => [
                'Super Admin',
                'Admin Pusat',
                'Admin Cabang',
                'Petugas Gudang',
                'Viewer',
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Hanya Super Admin yang boleh membuat user
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $validated = $request->validate([
            'user' => ['required', 'string', 'max:255', 'unique:users,user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => [
                'required',
                Rule::in([
                    'Super Admin',
                    'Admin Pusat',
                    'Admin Cabang',
                    'Petugas Gudang',
                    'Viewer',
                ]),
            ],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        // Admin Cabang wajib memiliki cabang
        if (
            in_array($validated['role'], [
                'Admin Cabang',
                'Petugas Gudang',
                'Viewer',
            ]) && empty($validated['branch_id'])
        ) {
            return back()
                ->withErrors(['branch_id' => 'Cabang wajib dipilih untuk role ini.'])
                ->withInput();
        }

        // Buat user baru
        $newUser = new User();
        $newUser->user = $validated['user'];
        $newUser->password = Hash::make($validated['password']);

        // Super Admin dan Admin Pusat menggunakan branch_id NULL
        $newUser->branch_id = in_array($validated['role'], [
            'Super Admin',
            'Admin Pusat',
        ]) ? null : $validated['branch_id'];

        $newUser->save();

        // Tetapkan role menggunakan Spatie
        $newUser->assignRole($validated['role']);

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        // Hanya Super Admin yang boleh mengubah user
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'user' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'user')->ignore($user->id),
            ],

            // Password boleh dikosongkan jika tidak ingin mengubahnya
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'Super Admin',
                    'Admin Pusat',
                    'Admin Cabang',
                    'Petugas Gudang',
                    'Viewer',
                ]),
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id',
            ],
        ]);

        // Admin Cabang, Petugas Gudang, dan Viewer wajib memiliki cabang
        if (
            in_array($validated['role'], [
                'Admin Cabang',
                'Petugas Gudang',
                'Viewer',
            ]) && empty($validated['branch_id'])
        ) {
            return back()
                ->withErrors([
                    'branch_id' => 'Cabang wajib dipilih untuk role ini.'
                ])
                ->withInput();
        }

        // Update username
        $user->user = $validated['user'];

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Super Admin dan Admin Pusat tidak terikat cabang
        $user->branch_id = in_array($validated['role'], [
            'Super Admin',
            'Admin Pusat',
        ]) ? null : $validated['branch_id'];

        $user->save();

        // Ganti role lama dengan role baru
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Hanya Super Admin yang boleh menghapus user
        abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $user = User::findOrFail($id);

        // Mencegah Super Admin menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('create-user')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        // Hapus hubungan role Spatie
        $user->syncRoles([]);

        // Hapus user
        $user->delete();

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil dihapus!');
    }
}
