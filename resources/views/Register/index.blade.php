@extends('Layouts.main')

@section('container')

<style>
    .modal {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1055;
        background-color: rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(1px);
    }

    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar User</h2>

        <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            <i class="bi bi-person-plus"></i> Tambah User
        </button>
    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM PENCARIAN --}}
    <div class="table-responsive">

        <form id="filterUserForm"
            method="GET"
            action="{{ request()->url() }}">
        </form>

        <div class="d-flex justify-content-between align-items-center mb-2">

            <span class="text-muted">
                Gunakan kotak pencarian pada setiap kolom.
            </span>

            <div>
                <a href="{{ request()->url() }}"
                    class="btn btn-secondary btn-sm">
                    Reset
                </a>

                <button type="submit"
                    form="filterUserForm"
                    class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>

        </div>

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>NO</th>
                    <th>USERNAME</th>
                    <th>ROLE</th>
                    <th>CABANG</th>
                    <th>AKSI</th>
                </tr>

                {{-- PENCARIAN --}}
                <tr>
                    <th></th>

                    <th>
                        <input type="text"
                            name="q_user"
                            form="filterUserForm"
                            class="form-control form-control-sm"
                            placeholder="Cari username..."
                            value="{{ request('q_user') }}">
                    </th>

                    <th>
                        <input type="text"
                            name="q_role"
                            form="filterUserForm"
                            class="form-control form-control-sm"
                            placeholder="Cari role..."
                            value="{{ request('q_role') }}">
                    </th>

                    <th>
                        <input type="text"
                            name="q_cabang"
                            form="filterUserForm"
                            class="form-control form-control-sm"
                            placeholder="Cari cabang..."
                            value="{{ request('q_cabang') }}">
                    </th>

                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse ($users as $user)

                    <tr>
                        <td>{{ $users->firstItem() + $loop->index }}</td>

                        <td>{{ $user->user }}</td>

                        <td>
                            @foreach ($user->getRoleNames() as $role)
                                <span class="badge bg-primary">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </td>

                        <td>
                            {{ $user->branch?->nama_cabang ?? 'Pusat' }}
                        </td>

                        <td>

                            {{-- TOMBOL EDIT --}}
                            <button type="button"
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $user->id }}">
                                Edit
                            </button>

                            {{-- TOMBOL HAPUS --}}
                            <button type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal{{ $user->id }}">
                                Hapus
                            </button>

                        </td>
                    </tr>

                    {{-- ================================= --}}
                    {{-- MODAL EDIT USER --}}
                    {{-- ================================= --}}

                    <div class="modal fade"
                        id="editModal{{ $user->id }}"
                        tabindex="-1"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">


                            <form action="{{ route('create-user.update', $user->id) }}"
                            method="POST">

                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Edit User
                                </h5>

                                <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal">
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">
                                        Username
                                    </label>

                                    <input type="text"
                                        name="user"
                                        class="form-control"
                                        value="{{ $user->user }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Password Baru
                                    </label>

                                    <input type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Kosongkan jika tidak diubah">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Konfirmasi Password Baru
                                    </label>

                                    <input type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        placeholder="Ulangi password baru">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Role
                                    </label>

                                    <select name="role"
                                        class="form-select"
                                        required>

                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}"
                                                {{ $user->hasRole($role) ? 'selected' : '' }}>
                                                {{ $role }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Cabang
                                    </label>

                                    <select name="branch_id"
                                        class="form-select">

                                        <option value="">
                                            Pusat / Tidak ada cabang
                                        </option>

                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->nama_cabang }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                    Batal
                                </button>

                                <button type="submit"
                                    class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                            </div>

                        </form>

                            </div>
                        </div>
                    </div>

                    {{-- ================================= --}}
                    {{-- MODAL DELETE USER --}}
                    {{-- ================================= --}}

                    <div class="modal fade"
    id="deleteModal{{ $user->id }}"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Hapus User
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                Apakah Anda yakin ingin menghapus user:

                <br><br>

                <strong>{{ $user->user }}</strong>

                <br><br>

                <span class="text-danger">
                    Akun ini tidak dapat digunakan lagi
                    setelah dihapus.
                </span>

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                @if ($user->id !== auth()->id())

                    <form action="{{ route('create-user.destroy', $user->id) }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="btn btn-danger">
                            Ya, Hapus
                        </button>
                        </form>
                        @else

                            <button type="button"
                                class="btn btn-danger"
                                disabled>
                                Tidak dapat menghapus akun sendiri
                            </button>

                        @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @empty

                    <tr>
                        <td colspan="5" class="text-center">
                            Belum ada data user.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>

    </div>
</div>

{{-- ================================= --}}
{{-- MODAL TAMBAH USER --}}
{{-- ================================= --}}

<div class="modal fade"
    id="createModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="{{ route('create-user.store') }}"
                method="POST">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        Tambah User Baru
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Username
                        </label>

                        <input type="text"
                            name="user"
                            class="form-control"
                            value="{{ old('user') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Password
                        </label>

                        <input type="password"
                            name="password"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Konfirmasi Password
                        </label>

                        <input type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Role
                        </label>

                        <select name="role"
                            class="form-select"
                            required>

                            <option value="">
                                -- Pilih Role --
                            </option>

                            @foreach ($roles as $role)
                                <option value="{{ $role }}"
                                    {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ $role }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Cabang
                        </label>

                        <select name="branch_id"
                            class="form-select">

                            <option value="">
                                Pusat / Tidak ada cabang
                            </option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nama_cabang }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        Simpan User
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endsection
