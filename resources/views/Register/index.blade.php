@extends('Layouts.main')

@section('container')

<style>
    /* =========================================
       USER PAGE
    ========================================= */

    .user-page {
        padding: 22px;
        color: #344054;
    }

    .user-page .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .user-page .page-title {
        margin: 0;
        color: #263b59;
        font-size: 23px;
        font-weight: 700;
    }

    /* CARD */
    .user-page .content-card {
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(31, 55, 85, .045);
    }

    /* TOOLBAR */
    .user-page .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .user-page .toolbar-info {
        color: #7b8798;
        font-size: 13px;
    }

    .user-page .toolbar-actions {
        display: flex;
        gap: 8px;
    }

    /* INPUT */
    .user-page .form-control,
    .user-page .form-select {
        border: 1px solid #dce3ed;
        border-radius: 7px;
        color: #344054;
        font-size: 13px;
        min-height: 36px;
        box-shadow: none;
    }

    .user-page .form-control:focus,
    .user-page .form-select:focus {
        border-color: #80a9df;
        box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
    }

    .user-page input::placeholder {
        color: #a0aaba;
    }

    /* TABLE */
    .user-page .table-responsive {
        border-radius: 9px;
    }

    .user-page .table {
        margin-bottom: 0;
        border-color: #e5eaf1;
        font-size: 13px;
        vertical-align: middle;
    }

    .user-page .table thead th {
        background: #f3f6fa;
        color: #52647c;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .35px;
        padding: 12px 10px;
        white-space: nowrap;
        vertical-align: middle;
        border-bottom: 1px solid #e1e7ef;
    }

    .user-page .table thead tr:nth-child(2) th {
        background: #f8fafc;
        padding: 8px;
    }

    .user-page .table tbody td {
        padding: 11px 10px;
        color: #475467;
        border-color: #edf0f4;
    }

    .user-page .table tbody tr:hover {
        background: #f8fbff;
    }

    /* BADGE ROLE */
    .user-page .badge.bg-primary {
        background: #e8f0fc !important;
        color: #356fc5 !important;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 9px;
        border-radius: 5px;
    }

    /* TOMBOL EDIT DAN HAPUS - BIRU */
    .user-page .btn-warning,
    .user-page .btn-danger {
        background: #356fc5;
        border-color: #356fc5;
        color: #fff;
    }

    .user-page .btn-warning:hover,
    .user-page .btn-danger:hover {
        background: #285da9;
        border-color: #285da9;
        color: #fff;
    }

    /* ALERT */
    .user-page .alert {
        border: 0;
        border-radius: 9px;
        font-size: 13px;
    }

    /* PAGINATION */
    .user-page .pagination {
        gap: 4px;
        margin-top: 16px;
        margin-bottom: 0;
    }

    .user-page .pagination .page-link {
        border: 1px solid #e1e7ef;
        border-radius: 6px !important;
        color: #52647c;
        font-size: 12px;
        padding: 6px 11px;
    }

    .user-page .pagination .active .page-link {
        background: #356fc5;
        border-color: #356fc5;
        color: #fff;
    }

    .user-page .pagination .disabled .page-link {
        color: #aab3c0;
    }

    /* MODAL */
    .user-modal {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(31, 55, 85, .16);
    }

    .user-modal .modal-header {
        background: #f3f6fa;
        border-bottom: 1px solid #e5eaf1;
        padding: 17px 20px;
    }

    .user-modal .modal-title {
        color: #263b59;
        font-size: 16px;
        font-weight: 700;
    }

    .user-modal .modal-body {
        padding: 20px;
    }

    .user-modal .modal-body label {
        color: #52647c;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .user-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e5eaf1;
        padding: 13px 20px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .user-page {
            padding: 12px;
        }

        .user-page .content-card {
            padding: 12px;
        }

        .user-page .page-header {
            align-items: flex-start;
        }

        .user-page .page-title {
            font-size: 19px;
        }

        .user-page .table-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .user-page .toolbar-actions {
            width: 100%;
        }

        .user-page .toolbar-actions .btn {
            flex: 1;
        }
    }
</style>

<div class="user-page container-fluid">

    {{-- HEADER --}}
    <div class="page-header">

        <h2 class="page-title">
            <i class="bi bi-people-fill me-2"></i>
            Daftar User
        </h2>

        <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createModal">

            <i class="bi bi-person-plus me-1"></i>
            Tambah User

        </button>

    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Terjadi kesalahan!
            </strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD TABEL --}}
    <div class="content-card">

        {{-- FORM FILTER --}}
        <form id="filterUserForm"
            method="GET"
            action="{{ request()->url() }}">
        </form>

        {{-- TOOLBAR --}}
        <div class="table-toolbar">

            <span class="toolbar-info">
                <i class="bi bi-info-circle me-1"></i>
                Gunakan kotak pencarian pada setiap kolom.
            </span>

            <div class="toolbar-actions">

                <a href="{{ request()->url() }}"
                    class="btn btn-secondary btn-sm">

                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset

                </a>

                <button type="submit"
                    form="filterUserForm"
                    class="btn btn-primary btn-sm">

                    <i class="bi bi-search me-1"></i>
                    Cari

                </button>

            </div>

        </div>

        {{-- TABEL USER --}}
        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th width="60">NO</th>
                        <th>USERNAME</th>
                        <th>ROLE</th>
                        <th>CABANG</th>
                        <th width="150" class="text-center">AKSI</th>
                    </tr>

                    {{-- FILTER PER KOLOM --}}
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

                            <td>
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <td class="fw-semibold">
                                {{ $user->user }}
                            </td>

                            <td>
                                @foreach ($user->getRoleNames() as $role)
                                    <span class="badge bg-primary me-1">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </td>

                            <td>
                                {{ $user->branch?->nama_cabang ?? 'Pusat' }}
                            </td>

                            <td class="text-center">

                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $user->id }}">

                                    <i class="bi bi-pencil-square"></i>
                                    Edit

                                </button>

                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $user->id }}">

                                    <i class="bi bi-trash"></i>
                                    Hapus

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada data user.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end">
            {{ $users->appends(request()->query())->links() }}
        </div>

    </div>

</div>


{{-- =========================================
     MODAL EDIT DAN HAPUS USER
========================================= --}}

@foreach ($users as $user)

    {{-- MODAL EDIT --}}
    <div class="modal fade"
        id="editModal{{ $user->id }}"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content user-modal">

                <form action="{{ route('create-user.update', $user->id) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-header">

                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>
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

                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- MODAL HAPUS --}}
    <div class="modal fade"
        id="deleteModal{{ $user->id }}"
        tabindex="-1"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content user-modal">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-trash me-2"></i>
                        Hapus User
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus user berikut?
                    </p>

                    <div class="p-3 rounded"
                        style="background:#f3f6fa;">

                        <strong class="text-dark">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ $user->user }}
                        </strong>

                    </div>

                    <div class="text-danger small mt-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Akun ini tidak dapat digunakan lagi setelah dihapus.
                    </div>

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

                                <i class="bi bi-trash me-1"></i>
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

@endforeach


{{-- =========================================
     MODAL TAMBAH USER
========================================= --}}

<div class="modal fade"
    id="createModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content user-modal">

            <form action="{{ route('create-user.store') }}"
                method="POST">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>
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
                            placeholder="Masukkan username"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input type="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Konfirmasi Password
                        </label>

                        <input type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi password"
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

                        <i class="bi bi-save me-1"></i>
                        Simpan User

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
