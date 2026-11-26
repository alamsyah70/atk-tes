@extends('Layouts.main')

@section('container')

@php
    $suppliers->appends(request()->query());
@endphp

<style>
/* =========================================
   SUPPLIER
   Mengikuti gaya tabel Master Barang
========================================= */

.supplier-page {
    color: #334155;
    font-size: 13px;
}

.supplier-page .page-header {
    margin-bottom: 22px;
}

.supplier-page .page-title {
    color: #1e293b;
    font-size: 23px;
    font-weight: 700;
    margin-bottom: 5px;
}

.supplier-page .page-subtitle {
    color: #8492a6;
    font-size: 12px;
}

/* CARD */
.supplier-page .content-card {
    background: #fff;
    border: 1px solid #e8edf4;
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
}

/* BUTTON */
.supplier-page .btn {
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 12px;
}

.supplier-page .btn-sm {
    padding: 5px 10px;
}

.supplier-page .btn-primary {
    background: #356fc5;
    border-color: #356fc5;
}

.supplier-page .btn-primary:hover {
    background: #285da9;
    border-color: #285da9;
}

/* TOOLBAR */
.supplier-page .toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 17px;
}

.supplier-page .total-info {
    color: #64748b;
    font-size: 12px;
}

.supplier-page .total-info strong {
    color: #334155;
    font-weight: 700;
}

/* SEARCH */
.supplier-page .search-container {
    width: min(460px, 100%);
}

.supplier-page .search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.supplier-page .search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 0;
}

.supplier-page .search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 19px;
    pointer-events: none;
}

.supplier-page .search-input {
    width: 100%;
    height: 39px;
    padding: 0 36px;
    border: 1px solid #dce4ee;
    border-radius: 8px;
    outline: none;
    background: #fff;
    color: #334155;
    font-size: 12px;
    transition: .2s;
}

.supplier-page .search-input:focus {
    border-color: #7ba6df;
    box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
}

.supplier-page .search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    text-decoration: none;
    font-size: 20px;
}

.supplier-page .search-clear:hover {
    color: #dc5555;
}

.supplier-page .search-button {
    height: 39px;
    padding: 0 18px;
    border: 1px solid #356fc5;
    border-radius: 8px;
    background: #356fc5;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.supplier-page .search-button:hover {
    background: #285da9;
}

/* TABLE */
.supplier-page .table-responsive {
    border: 1px solid #e8edf4;
    border-radius: 9px;
}

.supplier-page .table {
    margin-bottom: 0;
    font-size: 12px;
    color: #475569;
    white-space: nowrap;
}

.supplier-page .table thead th {
    padding: 12px 10px;
    vertical-align: middle;
    background: #f3f6fa;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .25px;
}

.supplier-page .table tbody td {
    padding: 10px;
    vertical-align: middle;
    border-color: #edf1f5;
}

.supplier-page .table tbody tr:hover {
    background: #f8fbff;
}

.supplier-page .table tbody tr:last-child td {
    border-bottom: 0;
}

/* BADGE / TEXT */
.supplier-page .supplier-code {
    font-weight: 700;
    color: #334155;
}

.supplier-page .supplier-name {
    font-weight: 600;
    color: #334155;
}

/* ALERT */
.supplier-page .alert {
    border-radius: 9px;
    font-size: 13px;
}

/* PAGINATION */
.supplier-page .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 18px;
    margin-bottom: 2px;
}

.supplier-page .pagination-info {
    color: #8492a6;
    font-size: 12px;
}

.supplier-page .pagination-links {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.supplier-page .page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 9px;
    border: 1px solid #e0e6ee;
    border-radius: 7px;
    background: #fff;
    color: #5276a5;
    text-decoration: none;
    font-size: 12px;
    transition: .2s;
}

.supplier-page .page-btn:hover {
    background: #edf4fd;
    border-color: #b8cce7;
    color: #285da9;
}

.supplier-page .page-btn.active {
    background: #356fc5;
    border-color: #356fc5;
    color: #fff;
    font-weight: 700;
}

.supplier-page .page-btn.disabled {
    color: #b6c0cc;
    background: #f7f9fb;
    border-color: #e8edf3;
    cursor: not-allowed;
    pointer-events: none;
}

/* MODAL */
.supplier-page .modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(15, 23, 42, .15);
}

.supplier-page .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e8edf4;
    padding: 15px 20px;
}

.supplier-page .modal-title {
    color: #334155;
    font-size: 15px;
    font-weight: 700;
}

.supplier-page .modal-body {
    padding: 20px;
}

.supplier-page .modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e8edf4;
    padding: 12px 20px;
}

.supplier-page .form-label {
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
}

.supplier-page .form-control,
.supplier-page .form-select {
    min-height: 38px;
    border-color: #dce4ee;
    border-radius: 7px;
    font-size: 12px;
}

.supplier-page .form-control:focus,
.supplier-page .form-select:focus {
    border-color: #7ba6df;
    box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .supplier-page .toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .supplier-page .search-container {
        width: 100%;
    }

    .supplier-page .pagination-container {
        align-items: flex-start;
        flex-direction: column;
    }

    .supplier-page .page-title {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .supplier-page .content-card {
        padding: 11px;
    }

    .supplier-page .search-form {
        flex-wrap: wrap;
    }

    .supplier-page .search-input-wrapper {
        flex-basis: 100%;
    }

    .supplier-page .search-button {
        width: 100%;
    }

    .supplier-page .page-btn {
        min-width: 29px;
        height: 30px;
        padding: 0 7px;
    }
}
</style>

<div class="container-fluid supplier-page">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h2 class="page-title">Master Supplier</h2>

            <div class="page-subtitle">
                Data pemasok / Supplier barang.
            </div>
        </div>

        @can('supplier.create')
            <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#createModal">

                <i class="bi bi-plus-circle me-1"></i>
                Tambah Supplier

            </button>
        @endcan

    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="content-card">

        {{-- TOOLBAR --}}
        <div class="toolbar">

            <div class="total-info">
                Total: <strong>{{ $suppliers->total() }}</strong> supplier
            </div>

            <div class="search-container">

                <form action="{{ route('supplier.index') }}"
                    method="GET"
                    class="search-form">

                    @if(request('branch_id'))
                        <input type="hidden"
                            name="branch_id"
                            value="{{ request('branch_id') }}">
                    @endif

                    <div class="search-input-wrapper">

                        <span class="search-icon">⌕</span>

                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari kode atau nama supplier..."
                            class="search-input">

                        @if(request('q'))
                            <a href="{{ route('supplier.index', request()->except('q', 'page')) }}"
                                class="search-clear"
                                title="Hapus pencarian">
                                &times;
                            </a>
                        @endif

                    </div>

                    <button type="submit" class="search-button">
                        <i class="bi bi-search me-1"></i>
                        Cari
                    </button>

                </form>

            </div>

        </div>

        {{-- TABEL SUPPLIER --}}
        <div class="table-responsive">

            <table class="table table-bordered table-striped align-middle">

                <thead>
                    <tr>
                        <th width="55">NO</th>
                        <th>KODE SUPPLIER</th>
                        <th>NAMA SUPPLIER</th>
                        <th>ALAMAT</th>
                        <th>NO. TELEPON</th>
                        <th>EMAIL</th>

                        @if($isGlobalUser)
                            <th>CABANG</th>
                        @endif

                        <th width="150">AKSI</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($suppliers as $index => $supplier)

                        <tr>
                            <td>
                                {{ $suppliers->firstItem() + $index }}
                            </td>

                            <td>
                                <span class="supplier-code">
                                    {{ $supplier->kode_supplier }}
                                </span>
                            </td>

                            <td>
                                <span class="supplier-name">
                                    {{ $supplier->nama_supplier }}
                                </span>
                            </td>

                            <td>
                                {{ $supplier->alamat ?: '-' }}
                            </td>

                            <td>
                                {{ $supplier->no_telepon ?: '-' }}
                            </td>

                            <td>
                                {{ $supplier->email ?: '-' }}
                            </td>

                            @if($isGlobalUser)
                                <td>
                                    {{ $supplier->branch?->nama_cabang ?? 'Belum ditentukan' }}
                                </td>
                            @endif

                            <td>
                                <div class="d-flex gap-1">

                                    @can('supplier.update')
                                        <button type="button"
                                            class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $supplier->id }}">

                                            <i class="bi bi-pencil-square"></i>
                                            Edit

                                        </button>
                                    @endcan

                                    @can('supplier.delete')
                                        <button type="button"
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $supplier->id }}">

                                            <i class="bi bi-trash"></i>
                                            Hapus

                                        </button>
                                    @endcan

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $isGlobalUser ? 8 : 7 }}"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                                Belum ada data supplier.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        @if($suppliers->hasPages())

            @php
                $current = $suppliers->currentPage();
                $last = $suppliers->lastPage();

                $start = max(1, $current - 1);
                $end = min($last, $current + 1);
            @endphp

            <div class="pagination-container">

                <div class="pagination-info">
                    Menampilkan {{ $suppliers->firstItem() ?? 0 }}
                    sampai {{ $suppliers->lastItem() ?? 0 }}
                    dari {{ $suppliers->total() }} supplier
                </div>

                <div class="pagination-links">

                    {{-- PREVIOUS --}}
                    @if($suppliers->onFirstPage())
                        <span class="page-btn disabled">
                            ‹ Previous
                        </span>
                    @else
                        <a href="{{ $suppliers->previousPageUrl() }}"
                            class="page-btn">
                            ‹ Previous
                        </a>
                    @endif

                    {{-- HALAMAN PERTAMA --}}
                    @if($start > 1)

                        <a href="{{ $suppliers->url(1) }}"
                            class="page-btn">
                            1
                        </a>

                        @if($start > 2)
                            <span class="page-btn disabled">...</span>
                        @endif

                    @endif

                    {{-- HALAMAN AKTIF --}}
                    @foreach(range($start, $end) as $page)

                        @if($page == $current)
                            <span class="page-btn active">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $suppliers->url($page) }}"
                                class="page-btn">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach

                    {{-- HALAMAN TERAKHIR --}}
                    @if($end < $last)

                        @if($end < $last - 1)
                            <span class="page-btn disabled">...</span>
                        @endif

                        <a href="{{ $suppliers->url($last) }}"
                            class="page-btn">
                            {{ $last }}
                        </a>

                    @endif

                    {{-- NEXT --}}
                    @if($suppliers->hasMorePages())
                        <a href="{{ $suppliers->nextPageUrl() }}"
                            class="page-btn">
                            Next ›
                        </a>
                    @else
                        <span class="page-btn disabled">
                            Next ›
                        </span>
                    @endif

                </div>

            </div>

        @endif

    </div>
    {{-- END CONTENT CARD --}}

    {{-- MODAL EDIT DAN HAPUS --}}
    @foreach($suppliers as $supplier)

        {{-- MODAL EDIT --}}
        @can('supplier.update')
            <div class="modal fade"
                id="editModal{{ $supplier->id }}"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <form action="{{ route('supplier.update', $supplier->id) }}"
                            method="POST">

                            @csrf
                            @method('PUT')

                            <div class="modal-header">

                                <h5 class="modal-title">
                                    Edit Supplier
                                </h5>

                                <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal">
                                </button>

                            </div>

                            <div class="modal-body">

                                @if($isGlobalUser)
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Cabang
                                        </label>

                                        <select name="branch_id"
                                            class="form-select"
                                            required>

                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    @selected($supplier->branch_id == $branch->id)>

                                                    {{ $branch->kode_cabang }} -
                                                    {{ $branch->nama_cabang }}

                                                </option>
                                            @endforeach

                                        </select>

                                    </div>
                                @endif

                                <input type="hidden"
                                    name="kode_supplier"
                                    value="{{ $supplier->kode_supplier }}">

                                <div class="mb-3">

                                    <label class="form-label">
                                        Kode Supplier
                                    </label>

                                    <input type="text"
                                        class="form-control bg-light"
                                        value="{{ $supplier->kode_supplier }}"
                                        readonly
                                        disabled>

                                        <small class="text-muted">
                                        Kode supplier tidak dapat diubah setelah master supplier dibuat.
                                    </small>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Nama Supplier
                                    </label>

                                    <input type="text"
                                        name="nama_supplier"
                                        class="form-control"
                                        value="{{ $supplier->nama_supplier }}"
                                        maxlength="255"
                                        oninput="this.value=this.value.toUpperCase()"
                                        required>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Alamat
                                    </label>

                                    <textarea name="alamat"
                                        class="form-control"
                                        rows="3"
                                        maxlength="255">{{ $supplier->alamat }}</textarea>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        No. Telepon
                                    </label>

                                    <input type="text"
                                        name="no_telepon"
                                        class="form-control"
                                        value="{{ $supplier->no_telepon }}"
                                        maxlength="30">

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input type="email"
                                        name="email"
                                        class="form-control"
                                        value="{{ $supplier->email }}"
                                        maxlength="255">

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
        @endcan

        {{-- MODAL HAPUS --}}
        @can('supplier.delete')
            <div class="modal fade"
                id="deleteModal{{ $supplier->id }}"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Hapus Supplier
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <p class="mb-3">
                                Apakah Anda yakin ingin menghapus supplier ini?
                            </p>

                            <div class="p-3 rounded"
                                style="background:#f8fafc; border:1px solid #e8edf4;">

                                <strong>
                                    {{ $supplier->nama_supplier }}
                                </strong>

                                <div class="text-muted mt-1">
                                    Kode: {{ $supplier->kode_supplier }}
                                </div>

                            </div>

                            <div class="text-muted mt-3"
                                style="font-size:12px;">
                                Supplier yang sudah digunakan pada transaksi penerimaan
                                tidak dapat dihapus.
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Batal
                            </button>

                            <form action="{{ route('supplier.destroy', $supplier->id) }}"
                                method="POST">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="btn btn-danger">

                                    <i class="bi bi-trash me-1"></i>
                                    Ya, Hapus

                                </button>

                            </form>

                        </div>

                    </div>
                </div>
            </div>
        @endcan

    @endforeach

    {{-- MODAL TAMBAH SUPPLIER --}}
    @can('supplier.create')
        <div class="modal fade"
            id="createModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('supplier.store') }}"
                        method="POST">

                        @csrf

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Tambah Supplier
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            @if($isGlobalUser)
                                <div class="mb-3">

                                    <label class="form-label">
                                        Cabang
                                    </label>

                                    <select name="branch_id"
                                        class="form-select"
                                        required>

                                        <option value="">
                                            -- Pilih Cabang --
                                        </option>

                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                @selected(old('branch_id') == $branch->id)>

                                                {{ $branch->kode_cabang }} -
                                                {{ $branch->nama_cabang }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>
                            @endif

                            <div class="mb-3">

                                <label class="form-label">
                                    Kode Supplier
                                </label>

                                <input type="text"
                                    name="kode_supplier"
                                    class="form-control"
                                    value="{{ old('kode_supplier') }}"
                                    maxlength="50"
                                    oninput="this.value=this.value.toUpperCase()"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Nama Supplier
                                </label>

                                <input type="text"
                                    name="nama_supplier"
                                    class="form-control"
                                    value="{{ old('nama_supplier') }}"
                                    maxlength="255"
                                    oninput="this.value=this.value.toUpperCase()"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Alamat
                                </label>

                                <textarea name="alamat"
                                    class="form-control"
                                    rows="3"
                                    maxlength="255"
                                    placeholder="Alamat supplier">{{ old('alamat') }}</textarea>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    No. Telepon
                                </label>

                                <input type="text"
                                    name="no_telepon"
                                    class="form-control"
                                    value="{{ old('no_telepon') }}"
                                    maxlength="30"
                                    placeholder="Contoh: 08123456789">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    placeholder="supplier@email.com">

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
                                Simpan Supplier

                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endcan

</div>
{{-- END SUPPLIER PAGE --}}

{{-- BUKA KEMBALI MODAL JIKA VALIDASI GAGAL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if($errors->any())

        const oldMethod = @json(old('_method', 'POST'));
        let modalId = 'createModal';

        if (oldMethod === 'PUT' || oldMethod === 'PATCH') {

            const editId = @json(session('edit_supplier_id'));

            if (editId) {
                modalId = 'editModal' + editId;
            }
        }

        const modalElement = document.getElementById(modalId);

        if (modalElement && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }

    @endif

});
</script>

@endsection
