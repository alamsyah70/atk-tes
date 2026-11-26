@extends('Layouts.main')

@section('container')

@php
    // Pertahankan filter saat berpindah halaman
    $barangs->appends(request()->query());
@endphp

<style>
/* =========================================
   MASTER BARANG
========================================= */

.master-barang-page {
    color: #334155;
    font-size: 13px;
}

.master-barang-page .page-header {
    margin-bottom: 22px;
}

.master-barang-page .page-title {
    color: #1e293b;
    font-size: 23px;
    font-weight: 700;
    margin-bottom: 5px;
}

.master-barang-page .page-subtitle {
    color: #8492a6;
    font-size: 12px;
}

/* CARD */
.master-barang-page .content-card {
    background: #fff;
    border: 1px solid #e8edf4;
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
}

/* BUTTON */
.master-barang-page .btn {
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 12px;
}

.master-barang-page .btn-sm {
    padding: 5px 10px;
}

.master-barang-page .btn-primary {
    background: #356fc5;
    border-color: #356fc5;
}

.master-barang-page .btn-primary:hover {
    background: #285da9;
    border-color: #285da9;
}

/* TOOLBAR */
.master-barang-page .toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 17px;
}

.master-barang-page .total-info {
    color: #64748b;
    font-size: 12px;
}

.master-barang-page .total-info strong {
    color: #334155;
    font-weight: 700;
}

/* SEARCH */
.master-barang-page .search-container {
    width: min(460px, 100%);
}

.master-barang-page .search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.master-barang-page .search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 0;
}

.master-barang-page .search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 19px;
    pointer-events: none;
}

.master-barang-page .search-input {
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

.master-barang-page .search-input:focus {
    border-color: #7ba6df;
    box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
}

.master-barang-page .search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    text-decoration: none;
    font-size: 20px;
}

.master-barang-page .search-clear:hover {
    color: #dc5555;
}

.master-barang-page .search-button {
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

.master-barang-page .search-button:hover {
    background: #285da9;
}

/* TABLE */
.master-barang-page .table-responsive {
    border: 1px solid #e8edf4;
    border-radius: 9px;
}

.master-barang-page .table {
    margin-bottom: 0;
    font-size: 12px;
    color: #475569;
    white-space: nowrap;
}

.master-barang-page .table thead th {
    padding: 12px 10px;
    vertical-align: middle;
    background: #f3f6fa;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .25px;
}

.master-barang-page .table tbody td {
    padding: 10px;
    vertical-align: middle;
    border-color: #edf1f5;
}

.master-barang-page .table tbody tr:hover {
    background: #f8fbff;
}

.master-barang-page .table tbody tr:last-child td {
    border-bottom: 0;
}

/* FILTER */
.master-barang-page .filter-form {
    margin: 0;
    min-width: 125px;
}

.master-barang-page .filter-select {
    min-width: 125px;
    height: 31px;
    padding: 4px 25px 4px 8px;
    border: 1px solid #dce4ee;
    border-radius: 6px;
    background-color: #fff;
    color: #475569;
    font-size: 11px;
    cursor: pointer;
}

.master-barang-page .filter-select:focus {
    border-color: #7ba6df;
    box-shadow: 0 0 0 2px rgba(53, 111, 197, .10);
}

/* NILAI */
.master-barang-page .stock-value {
    font-weight: 700;
    color: #334155;
}

.master-barang-page .money-value {
    font-weight: 600;
    color: #475569;
}

/* ALERT */
.master-barang-page .alert {
    border-radius: 9px;
    font-size: 13px;
}

/* PAGINATION */
.master-barang-page .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 18px;
    margin-bottom: 2px;
}

.master-barang-page .pagination-info {
    color: #8492a6;
    font-size: 12px;
}

.master-barang-page .pagination-links {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.master-barang-page .page-btn {
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

.master-barang-page .page-btn:hover {
    background: #edf4fd;
    border-color: #b8cce7;
    color: #285da9;
}

.master-barang-page .page-btn.active {
    background: #356fc5;
    border-color: #356fc5;
    color: #fff;
    font-weight: 700;
}

.master-barang-page .page-btn.disabled {
    color: #b6c0cc;
    background: #f7f9fb;
    border-color: #e8edf3;
    cursor: not-allowed;
    pointer-events: none;
}

/* FORM MODAL */
.master-barang-page .modal-content {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(15, 23, 42, .15);
}

.master-barang-page .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e8edf4;
    padding: 15px 20px;
}

.master-barang-page .modal-title {
    color: #334155;
    font-size: 15px;
    font-weight: 700;
}

.master-barang-page .modal-body {
    padding: 20px;
}

.master-barang-page .modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e8edf4;
    padding: 12px 20px;
}

.master-barang-page .form-label {
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
}

.master-barang-page .form-control,
.master-barang-page .form-select {
    min-height: 38px;
    border-color: #dce4ee;
    border-radius: 7px;
    font-size: 12px;
}

.master-barang-page .form-control:focus,
.master-barang-page .form-select:focus {
    border-color: #7ba6df;
    box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .master-barang-page .toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .master-barang-page .search-container {
        width: 100%;
    }

    .master-barang-page .pagination-container {
        align-items: flex-start;
        flex-direction: column;
    }

    .master-barang-page .page-title {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .master-barang-page .content-card {
        padding: 11px;
    }

    .master-barang-page .search-form {
        flex-wrap: wrap;
    }

    .master-barang-page .search-input-wrapper {
        flex-basis: 100%;
    }

    .master-barang-page .search-button {
        width: 100%;
    }

    .master-barang-page .page-btn {
        min-width: 29px;
        height: 30px;
        padding: 0 7px;
    }
}
</style>

<div class="container-fluid master-barang-page">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="page-title">Master Barang</h2>
            <div class="page-subtitle">
                Katalog barang / Master barang.
            </div>
        </div>

        @can('barang.create')
            <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah Barang
            </button>
        @endcan
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
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

        {{-- INFORMASI DAN PENCARIAN --}}
        <div class="toolbar">

            <div class="total-info">
                Total: <strong>{{ $barangs->total() }}</strong> barang
            </div>

            <div class="search-container">
                <form action="{{ route('barangs.index') }}"
                    method="GET"
                    class="search-form">

                    @if(request('branch_id'))
                        <input type="hidden"
                            name="branch_id"
                            value="{{ request('branch_id') }}">
                    @endif

                    @if(request('saldo'))
                        <input type="hidden"
                            name="saldo"
                            value="{{ request('saldo') }}">
                    @endif

                    <div class="search-input-wrapper">
                        <span class="search-icon">⌕</span>

                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari kode atau nama barang..."
                            class="search-input">

                        @if(request('q'))
                            <a href="{{ route('barangs.index', request()->except('q', 'page')) }}"
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

        {{-- TABEL BARANG --}}
        <div class="table-responsive">

            <table class="table table-bordered table-striped align-middle">

                <thead>
                    <tr>
                        <th>KODE BARANG</th>
                        <th>NAMA BARANG</th>
                        <th>KATEGORI</th>
                        <th>SATUAN</th>
                        <th>LOKASI</th>
                        <th>STOK AKHIR</th>
                        <th>HARGA RATA-RATA</th>

                        <th>
                            <form method="GET"
                                action="{{ route('barangs.index') }}"
                                class="filter-form">

                                <input type="hidden"
                                    name="q"
                                    value="{{ request('q') }}">

                                @if(request('branch_id'))
                                    <input type="hidden"
                                        name="branch_id"
                                        value="{{ request('branch_id') }}">
                                @endif

                                <select name="saldo"
                                    class="form-select form-select-sm filter-select"
                                    onchange="this.form.submit()">

                                    <option value="">Semua Saldo</option>

                                    <option value="minus"
                                        @selected(request('saldo') == 'minus')>
                                        Saldo Minus
                                    </option>

                                    <option value="nol"
                                        @selected(request('saldo') == 'nol')>
                                        Saldo Nol
                                    </option>

                                    <option value="positif"
                                        @selected(request('saldo') == 'positif')>
                                        Saldo Positif
                                    </option>

                                </select>
                            </form>
                        </th>

                        @if($isGlobalUser)
                            <th>
                                <form action="{{ route('barangs.index') }}"
                                    method="GET"
                                    class="filter-form">

                                    <input type="hidden"
                                        name="q"
                                        value="{{ request('q') }}">

                                    <input type="hidden"
                                        name="saldo"
                                        value="{{ request('saldo') }}">

                                    <select name="branch_id"
                                        class="form-select form-select-sm filter-select"
                                        onchange="this.form.submit()">

                                        <option value="">Semua Cabang</option>

                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                @selected(request('branch_id') == $branch->id)>
                                                {{ $branch->kode_cabang }} -
                                                {{ $branch->nama_cabang }}
                                            </option>
                                        @endforeach

                                    </select>
                                </form>
                            </th>
                        @endif

                        <th width="150">AKSI</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($barangs as $barang)

                        <tr>
                            <td>
                                <strong>{{ $barang->kode_barang }}</strong>
                            </td>

                            <td>{{ $barang->nama_barang }}</td>

                            <td>{{ $barang->kategori ?? '-' }}</td>

                            <td>{{ $barang->satuan ?? '-' }}</td>

                            <td>{{ $barang->lokasi ?? '-' }}</td>

                            <td>
                                <span class="stock-value">
                                    {{ number_format($barang->stok ?? 0, 0, ',', '.') }}
                                    {{ $barang->satuan }}
                                </span>
                            </td>

                            <td>
                                <span class="money-value">
                                    Rp {{ number_format($barang->harga_rata_rata ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                <span class="money-value">
                                    Rp {{ number_format($barang->nilai_saldo ?? 0, 0, ',', '.') }}
                                </span>
                            </td>

                            @if($isGlobalUser)
                                <td>
                                    {{ $barang->branch?->nama_cabang ?? 'Belum ditentukan' }}
                                </td>
                            @endif

                            <td>
                                <div class="d-flex gap-1">

                                    @can('barang.update')
                                        <button type="button"
                                            class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $barang->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </button>
                                    @endcan

                                    @can('barang.delete')
                                        <button type="button"
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $barang->id }}">
                                            <i class="bi bi-trash"></i>
                                            Hapus
                                        </button>
                                    @endcan

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $isGlobalUser ? 10 : 9 }}"
                                class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada Master Barang.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

        {{-- PAGINATION --}}
        @if($barangs->hasPages())

            @php
                $current = $barangs->currentPage();
                $last = $barangs->lastPage();

                $start = max(1, $current - 1);
                $end = min($last, $current + 1);
            @endphp

            <div class="pagination-container">

                <div class="pagination-info">
                    Menampilkan {{ $barangs->firstItem() ?? 0 }}
                    sampai {{ $barangs->lastItem() ?? 0 }}
                    dari {{ $barangs->total() }} barang
                </div>

                <div class="pagination-links">

                    {{-- PREVIOUS --}}
                    @if($barangs->onFirstPage())
                        <span class="page-btn disabled">
                            ‹ Previous
                        </span>
                    @else
                        <a href="{{ $barangs->previousPageUrl() }}"
                            class="page-btn">
                            ‹ Previous
                        </a>
                    @endif

                    {{-- HALAMAN PERTAMA --}}
                    @if($start > 1)

                        <a href="{{ $barangs->url(1) }}"
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
                            <a href="{{ $barangs->url($page) }}"
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

                        <a href="{{ $barangs->url($last) }}"
                            class="page-btn">
                            {{ $last }}
                        </a>

                    @endif

                    {{-- NEXT --}}
                    @if($barangs->hasMorePages())
                        <a href="{{ $barangs->nextPageUrl() }}"
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
    @foreach($barangs as $barang)

        {{-- MODAL EDIT --}}
        @can('barang.update')
            <div class="modal fade"
                id="editModal{{ $barang->id }}"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <form action="{{ route('barangs.update', $barang->id) }}"
                            method="POST">

                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Edit Master Barang
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
                                                    @selected($barang->branch_id == $branch->id)>
                                                    {{ $branch->kode_cabang }} -
                                                    {{ $branch->nama_cabang }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                @endif

                                <input type="hidden"
                                    name="edit_barang_id"
                                    value="{{ $barang->id }}">

                                <input type="hidden"
                                    name="kode_barang"
                                    value="{{ $barang->kode_barang }}">

                                <div class="mb-3">
                                    <label class="form-label">
                                        Kode Barang
                                    </label>

                                    <input type="text"
                                        class="form-control bg-light"
                                        value="{{ $barang->kode_barang }}"
                                        readonly
                                        disabled>

                                    <small class="text-muted">
                                        Kode barang tidak dapat diubah setelah master barang dibuat.
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Nama Barang
                                    </label>

                                    <input type="text"
                                        name="nama_barang"
                                        class="form-control"
                                        value="{{ $barang->nama_barang }}"
                                        maxlength="255"
                                        oninput="this.value=this.value.toUpperCase()"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Kategori
                                    </label>

                                    <select name="kategori"
                                        class="form-select">

                                        <option value="">
                                            -- Pilih Kategori --
                                        </option>

                                        @if($barang->kategori && !in_array($barang->kategori, $kategoriOptions))
                                            <option value="{{ $barang->kategori }}" selected>
                                                {{ $barang->kategori }}
                                            </option>
                                        @endif

                                        @foreach($kategoriOptions as $kategori)
                                            <option value="{{ $kategori }}"
                                                @selected($barang->kategori == $kategori)>
                                                {{ $kategori }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Satuan
                                    </label>

                                    <select name="satuan"
                                        class="form-select">

                                        <option value="">
                                            -- Pilih Satuan --
                                        </option>

                                        @if($barang->satuan && !in_array($barang->satuan, $satuanOptions))
                                            <option value="{{ $barang->satuan }}" selected>
                                                {{ $barang->satuan }}
                                            </option>
                                        @endif

                                        @foreach($satuanOptions as $satuan)
                                            <option value="{{ $satuan }}"
                                                @selected($barang->satuan == $satuan)>
                                                {{ $satuan }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Lokasi
                                    </label>

                                    <input type="text"
                                        name="lokasi"
                                        class="form-control"
                                        value="{{ $barang->lokasi }}"
                                        maxlength="100"
                                        oninput="this.value=this.value.toUpperCase()">
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
        @can('barang.delete')
            <div class="modal fade"
                id="deleteModal{{ $barang->id }}"
                tabindex="-1"
                aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Hapus Master Barang
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <p class="mb-3">
                                Apakah Anda yakin ingin menghapus barang ini?
                            </p>

                            <div class="p-3 rounded"
                                style="background:#f8fafc; border:1px solid #e8edf4;">

                                <strong>
                                    {{ $barang->nama_barang }}
                                </strong>

                                <div class="text-muted mt-1">
                                    Kode: {{ $barang->kode_barang }}
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                Batal
                            </button>

                            <form action="{{ route('barangs.destroy', $barang->id) }}"
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

    {{-- MODAL TAMBAH BARANG --}}
    @can('barang.create')
        <div class="modal fade"
            id="createModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form action="{{ route('barangs.store') }}"
                        method="POST">

                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">
                                Tambah Master Barang
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
                                    Kode Barang
                                </label>

                                <input type="text"
                                    name="kode_barang"
                                    class="form-control"
                                    value="{{ old('kode_barang') }}"
                                    maxlength="50"
                                    oninput="this.value=this.value.toUpperCase()"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Barang
                                </label>

                                <input type="text"
                                    name="nama_barang"
                                    class="form-control"
                                    value="{{ old('nama_barang') }}"
                                    maxlength="255"
                                    oninput="this.value=this.value.toUpperCase()"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Kategori
                                </label>

                                <select name="kategori"
                                    class="form-select">

                                    <option value="">
                                        -- Pilih Kategori --
                                    </option>

                                    @foreach($kategoriOptions as $kategori)
                                        <option value="{{ $kategori }}"
                                            @selected(old('kategori') == $kategori)>
                                            {{ $kategori }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Satuan
                                </label>

                                <select name="satuan"
                                    class="form-select">

                                    <option value="">
                                        -- Pilih Satuan --
                                    </option>

                                    @foreach($satuanOptions as $satuan)
                                        <option value="{{ $satuan }}"
                                            @selected(old('satuan') == $satuan)>
                                            {{ $satuan }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Lokasi
                                </label>

                                <input type="text"
                                    name="lokasi"
                                    class="form-control"
                                    value="{{ old('lokasi') }}"
                                    maxlength="100"
                                    placeholder="Contoh: A.1"
                                    oninput="this.value=this.value.toUpperCase()">
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
                                Simpan Barang
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endcan

</div>
{{-- END MASTER BARANG PAGE --}}

{{-- BUKA KEMBALI MODAL JIKA VALIDASI GAGAL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if($errors->any())

        const oldMethod = @json(old('_method', 'POST'));
        let modalId = 'createModal';

        if (oldMethod === 'PUT' || oldMethod === 'PATCH') {

            const editId = @json(session('edit_barang_id'));

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
