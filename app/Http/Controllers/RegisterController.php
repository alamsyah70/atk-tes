<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Role yang boleh diberikan melalui menu Manajemen User.
     *
     * Admin Cabang tetap disertakan sebagai kompatibilitas
     * untuk akun lama jika role tersebut masih ada di database.
     */
    private function availableRoleNames(): array
    {
        $allowedRoles = [
            'Super Admin',
            'IT Cabang',
            // 'Admin Pusat',
            'Admin Gerai',
            'Inventory Auditor',
            // 'Petugas Gudang',
            // 'Viewer',
        ];

        return Role::where('guard_name', 'web')
            ->whereIn('name', $allowedRoles)
            ->pluck('name')
            ->all();
    }

    /**
     * Hak akses mengelola user.
     */
    private function authorizeUserManagement(): void
    {
        abort_unless(
            auth()->user()->hasAnyRole([
                'Super Admin',
                'IT Cabang',
            ]),
            403,
            'Anda tidak memiliki akses manajemen user.'
        );
    }

    /**
     * Apakah user boleh memberikan role Super Admin?
     */
    private function canAssignSuperAdmin(): bool
    {
        return auth()->user()->hasRole('Super Admin');
    }

    /**
     * Apakah role menggunakan akses lintas cabang?
     */
    private function isGlobalRole(string $role): bool
    {
        return in_array($role, [
            'Super Admin',
            'IT Cabang',
            'Admin Pusat',
        ], true);
    }

    /**
     * Role yang wajib memiliki cabang.
     */
    private function requiresBranch(string $role): bool
    {
        return !$this->isGlobalRole($role);
    }

    /**
     * Menampilkan daftar user.
     */
    public function index(Request $request)
    {
        $this->authorizeUserManagement();

        $query = User::with(['roles', 'branch']);

        // Pencarian username
        if ($request->filled('q_user')) {
            $query->where(
                'user',
                'like',
                '%' . $request->q_user . '%'
            );
        }

        // Pencarian role
        if ($request->filled('q_role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where(
                    'name',
                    'like',
                    '%' . $request->q_role . '%'
                );
            });
        }

        // Pencarian cabang
        if ($request->filled('q_cabang')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where(
                    'nama_cabang',
                    'like',
                    '%' . $request->q_cabang . '%'
                );
            });
        }

        return view('Register.index', [
            'title' => 'Daftar User',

            'users' => $query
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString(),

            'branches' => Branch::orderBy('nama_cabang')->get(),

            'roles' => $this->availableRoleNames(),

            'isSuperAdmin' => $this->canAssignSuperAdmin(),
        ]);
    }

    /**
     * Membuat user baru.
     */
    public function store(Request $request)
    {
        $this->authorizeUserManagement();

        $availableRoles = $this->availableRoleNames();

        $validated = $request->validate([
            'user' => [
                'required',
                'string',
                'max:255',
                'unique:users,user',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in($availableRoles),
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id',
            ],
        ]);

        // IT Cabang tidak boleh membuat Super Admin.
        if (
            $validated['role'] === 'Super Admin'
            && !$this->canAssignSuperAdmin()
        ) {
            abort(403, 'Hanya Super Admin yang dapat membuat akun Super Admin.');
        }

        // Role non-global wajib memiliki cabang.
        if (
            $this->requiresBranch($validated['role'])
            && empty($validated['branch_id'])
        ) {
            return back()
                ->withErrors([
                    'branch_id' => 'Cabang wajib dipilih untuk role ini.',
                ])
                ->withInput();
        }

        $newUser = new User();

        $newUser->user = $validated['user'];
        $newUser->password = Hash::make($validated['password']);

        // User global tidak terikat cabang.
        $newUser->branch_id = $this->isGlobalRole($validated['role'])
            ? null
            : $validated['branch_id'];

        $newUser->save();

        // Tetapkan role Spatie.
        $newUser->syncRoles([$validated['role']]);

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil dibuat!');
    }

    /**
     * Mengubah user.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeUserManagement();

        $user = User::with('roles')->findOrFail($id);

        $availableRoles = $this->availableRoleNames();

        $targetIsSuperAdmin = $user->hasRole('Super Admin');

        // IT Cabang tidak boleh mengubah akun Super Admin.
        if (
            $targetIsSuperAdmin
            && !$this->canAssignSuperAdmin()
        ) {
            abort(403, 'IT Cabang tidak dapat mengubah akun Super Admin.');
        }

        $validated = $request->validate([
            'user' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'user')->ignore($user->id),
            ],

            // Password boleh kosong jika tidak ingin diubah.
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in($availableRoles),
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id',
            ],
        ]);

        // IT Cabang tidak boleh menaikkan akun menjadi Super Admin.
        if (
            $validated['role'] === 'Super Admin'
            && !$this->canAssignSuperAdmin()
        ) {
            abort(403, 'Hanya Super Admin yang dapat memberikan role Super Admin.');
        }

        // Mencegah Super Admin tidak sengaja mengganti role sendiri.
        if (
            $user->id === auth()->id()
            && $validated['role'] !== 'Super Admin'
        ) {
            return back()
                ->withErrors([
                    'role' => 'Anda tidak dapat menurunkan role akun sendiri.',
                ])
                ->withInput();
        }

        // Role non-global wajib memiliki cabang.
        if (
            $this->requiresBranch($validated['role'])
            && empty($validated['branch_id'])
        ) {
            return back()
                ->withErrors([
                    'branch_id' => 'Cabang wajib dipilih untuk role ini.',
                ])
                ->withInput();
        }

        // Update username.
        $user->user = $validated['user'];

        // Update password jika diisi.
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Atur cabang berdasarkan role.
        $user->branch_id = $this->isGlobalRole($validated['role'])
            ? null
            : $validated['branch_id'];

        $user->save();

        // Ganti role sebelumnya.
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Menghapus user.
     */
    public function destroy($id)
    {
        $this->authorizeUserManagement();

        $user = User::with('roles')->findOrFail($id);

        // Tidak boleh menghapus akun sendiri.
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('create-user')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        // IT Cabang tidak boleh menghapus Super Admin.
        if (
            $user->hasRole('Super Admin')
            && !$this->canAssignSuperAdmin()
        ) {
            abort(403, 'IT Cabang tidak dapat menghapus akun Super Admin.');
        }

        // Jangan sampai Super Admin terakhir terhapus.
        if ($user->hasRole('Super Admin')) {
            $jumlahSuperAdmin = User::role('Super Admin')->count();

            if ($jumlahSuperAdmin <= 1) {
                return redirect()
                    ->route('create-user')
                    ->with('error', 'Super Admin terakhir tidak boleh dihapus!');
            }
        }

        // Hapus hubungan role Spatie.
        $user->syncRoles([]);

        // Hapus user.
        $user->delete();

        return redirect()
            ->route('create-user')
            ->with('success', 'User berhasil dihapus!');
    }
}
