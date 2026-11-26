@extends('Layouts.main')

@section('container')

<style>
    /* =========================================
       KELOLA BARANG - SUPPLIER STYLE
    ========================================= */

    .kelola-barang-page {
        padding: 22px;
        color: #344054;
    }

    .kelola-barang-page .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }

    .kelola-barang-page .page-title {
        margin: 0;
        color: #263b59;
        font-size: 23px;
        font-weight: 700;
    }

    /* CARD */
    .kelola-barang-page .content-card {
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(31, 55, 85, .045);
    }

    /* TOOLBAR */
    .kelola-barang-page .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }

    .kelola-barang-page .toolbar-info {
        color: #7b8798;
        font-size: 13px;
    }

    .kelola-barang-page .toolbar-actions {
        display: flex;
        gap: 8px;
    }

    /* INPUT */
    .kelola-barang-page .form-control,
    .kelola-barang-page .form-select {
        border: 1px solid #dce3ed;
        border-radius: 7px;
        color: #344054;
        font-size: 13px;
        min-height: 36px;
        box-shadow: none;
    }

    .kelola-barang-page .form-control:focus,
    .kelola-barang-page .form-select:focus {
        border-color: #80a9df;
        box-shadow: 0 0 0 3px rgba(53, 111, 197, .10);
    }

    .kelola-barang-page input::placeholder {
        color: #a0aaba;
    }

    /* TABLE */
    .kelola-barang-page .table-responsive {
        border-radius: 9px;
    }

    .kelola-barang-page .table {
        margin-bottom: 0;
        border-color: #e5eaf1;
        font-size: 13px;
        vertical-align: middle;
    }

    .kelola-barang-page .table thead th {
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

    .kelola-barang-page .table thead tr:nth-child(2) th {
        background: #f8fafc;
        padding: 8px;
    }

    .kelola-barang-page .table tbody td {
        padding: 11px 10px;
        color: #475467;
        border-color: #edf0f4;
    }

    .kelola-barang-page .table tbody tr:hover {
        background: #f8fbff;
    }

    /* STATUS */
    .kelola-barang-page .badge {
        font-size: 11px;
        font-weight: 600;
        padding: 6px 9px;
        border-radius: 5px;
    }

    .kelola-barang-page .badge.bg-success {
        background: #e6f5ed !important;
        color: #218653 !important;
    }

    .kelola-barang-page .badge.bg-danger {
        background: #fff0ef !important;
        color: #c44740 !important;
    }

    /* BUTTON */
    .kelola-barang-page .btn {
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        padding: 7px 12px;
        transition: .2s ease;
    }

    .kelola-barang-page .btn-primary {
        background: #356fc5;
        border-color: #356fc5;
        color: #fff;
    }

    .kelola-barang-page .btn-primary:hover {
        background: #285da9;
        border-color: #285da9;
    }

    .kelola-barang-page .btn-secondary {
        background: #eef1f5;
        border-color: #e0e5ec;
        color: #526174;
    }

    .kelola-barang-page .btn-secondary:hover {
        background: #e1e6ed;
        color: #344054;
    }

    /* TOMBOL AKSI BIRU */
    .kelola-barang-page .btn-warning,
    .kelola-barang-page .btn-danger {
        background: #356fc5;
        border-color: #356fc5;
        color: #fff;
    }

    .kelola-barang-page .btn-warning:hover,
    .kelola-barang-page .btn-danger:hover {
        background: #285da9;
        border-color: #285da9;
        color: #fff;
    }

    /* ALERT */
    .kelola-barang-page .alert {
        border: 0;
        border-radius: 9px;
        font-size: 13px;
    }

    /* PAGINATION */
    .kelola-barang-page .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .kelola-barang-page .pagination-info {
        font-size: 13px;
        color: #7b8798;
    }

    .kelola-barang-page .pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .kelola-barang-page .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #e1e7ef;
        border-radius: 6px;
        background: #fff;
        color: #356fc5;
        text-decoration: none;
        font-size: 12px;
        transition: .2s ease;
    }

    .kelola-barang-page .page-btn:hover,
    .kelola-barang-page .page-btn.active {
        background: #356fc5;
        border-color: #356fc5;
        color: #fff;
    }

    .kelola-barang-page .page-btn.active {
        font-weight: 600;
    }

    .kelola-barang-page .page-btn.disabled {
        color: #aab3c0;
        background: #f8fafc;
        border-color: #e1e7ef;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* MODAL */
    .kelola-barang-page .barang-modal {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(31, 55, 85, .16);
    }

    .kelola-barang-page .barang-modal .modal-header {
        background: #f3f6fa;
        border-bottom: 1px solid #e5eaf1;
        padding: 17px 20px;
    }

    .kelola-barang-page .barang-modal .modal-title {
        color: #263b59;
        font-size: 16px;
        font-weight: 700;
    }

    .kelola-barang-page .barang-modal .modal-body {
        padding: 20px;
    }

    .kelola-barang-page .barang-modal .modal-body label {
        color: #52647c;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .kelola-barang-page .barang-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e5eaf1;
        padding: 13px 20px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .kelola-barang-page {
            padding: 12px;
        }

        .kelola-barang-page .content-card {
            padding: 12px;
        }

        .kelola-barang-page .page-title {
            font-size: 19px;
        }

        .kelola-barang-page .table-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .kelola-barang-page .toolbar-actions {
            width: 100%;
        }

        .kelola-barang-page .toolbar-actions .btn {
            flex: 1;
        }

        .kelola-barang-page .pagination-container {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>


<div class="kelola-barang-page container-fluid">

    {{-- HEADER --}}
    <div class="page-header">

        <h2 class="page-title">
            <i class="bi bi-box-seam me-2"></i>
            Kelola Barang
        </h2>

    </div>


    {{-- NOTIFIKASI BERHASIL --}}
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


    {{-- NOTIFIKASI ERROR --}}
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
        <form id="filterBarangForm"
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
                    form="filterBarangForm"
                    class="btn btn-primary btn-sm">

                    <i class="bi bi-search me-1"></i>
                    Cari

                </button>

            </div>

        </div>


        {{-- TABEL BARANG --}}
        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>KODE</th>
                        <th>NAMA BARANG</th>
                        <th>KATEGORI</th>
                        <th>STOK</th>
                        <th>LOKASI</th>
                        <th>STATUS</th>

                        @hasanyrole('Super Admin|Admin Pusat')
                            <th width="150" class="text-center">AKSI</th>
                        @endhasanyrole
                    </tr>


                    {{-- FILTER PER KOLOM --}}
                    <tr>

                        <th>
                            <input type="text"
                                name="q_kode"
                                form="filterBarangForm"
                                class="form-control form-control-sm"
                                placeholder="Cari kode..."
                                value="{{ request('q_kode') }}">
                        </th>

                        <th>
                            <input type="text"
                                name="q_nama"
                                form="filterBarangForm"
                                class="form-control form-control-sm"
                                placeholder="Cari nama..."
                                value="{{ request('q_nama') }}">
                        </th>

                        <th>
                            <input type="text"
                                name="q_kategori"
                                form="filterBarangForm"
                                class="form-control form-control-sm"
                                placeholder="Cari kategori..."
                                value="{{ request('q_kategori') }}">
                        </th>

                        <th>
                            <input type="number"
                                name="q_stok"
                                form="filterBarangForm"
                                class="form-control form-control-sm"
                                placeholder="Stok ="
                                min="0"
                                value="{{ request('q_stok') }}">
                        </th>

                        <th>
                            <input type="text"
                                name="q_lokasi"
                                form="filterBarangForm"
                                class="form-control form-control-sm"
                                placeholder="Cari lokasi..."
                                value="{{ request('q_lokasi') }}">
                        </th>

                        <th>
                            <select name="q_status"
                                form="filterBarangForm"
                                class="form-select form-select-sm">

                                <option value="">Semua</option>

                                <option value="tersedia"
                                    {{ request('q_status') == 'tersedia' ? 'selected' : '' }}>
                                    Tersedia
                                </option>

                                <option value="habis"
                                    {{ request('q_status') == 'habis' ? 'selected' : '' }}>
                                    Habis
                                </option>

                            </select>
                        </th>

                        @hasanyrole('Super Admin|Admin Pusat')
                            <th></th>
                        @endhasanyrole

                    </tr>

                </thead>


                <tbody id="productRows">

                    @forelse ($barangs as $barang)

                        <tr>

                            <td class="fw-semibold">
                                {{ $barang->kode_barang }}
                            </td>

                            <td>
                                {{ $barang->nama_barang }}
                            </td>

                            <td>
                                {{ $barang->kategori }}
                            </td>

                            <td>
                                <strong>{{ $barang->stok }}</strong>
                                {{ $barang->satuan }}
                            </td>

                            <td>
                                {{ $barang->nama_cabang ?? '-' }}
                                {{ $barang->lokasi ? ' - ' . $barang->lokasi : '' }}
                            </td>

                            <td>
                                @if ($barang->stok > 0)

                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Tersedia
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Habis
                                    </span>

                                @endif
                            </td>

                            @hasanyrole('Super Admin|Admin Pusat')
                                <td class="text-center">

                                    {{-- TOMBOL ADJUST --}}
                                    <button type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $barang->id }}">

                                        <i class="bi bi-sliders me-1"></i>
                                        Adjust

                                    </button>

                                    {{-- TOMBOL HAPUS --}}
                                    <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $barang->id }}">

                                        <i class="bi bi-trash me-1"></i>
                                        Hapus

                                    </button>

                                </td>
                            @endhasanyrole

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                Belum ada data barang.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($barangs->hasPages())

            @php
                $current = $barangs->currentPage();
                $last = $barangs->lastPage();

                $start = max(1, $current - 1);
                $end = min($last, $current + 1);
            @endphp

            <div class="pagination-container">

                <div class="pagination-info">

                    Menampilkan
                    {{ $barangs->firstItem() ?? 0 }}
                    sampai
                    {{ $barangs->lastItem() ?? 0 }}
                    dari
                    {{ $barangs->total() }}
                    barang

                </div>


                <div class="pagination-links">

                    {{-- PREVIOUS --}}
                    @if ($barangs->onFirstPage())

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
                    @if ($start > 1)

                        <a href="{{ $barangs->url(1) }}"
                            class="page-btn">
                            1
                        </a>

                        @if ($start > 2)
                            <span class="page-btn disabled">...</span>
                        @endif

                    @endif


                    {{-- HALAMAN AKTIF --}}
                    @foreach (range($start, $end) as $page)

                        @if ($page == $current)

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
                    @if ($end < $last)

                        @if ($end < $last - 1)
                            <span class="page-btn disabled">...</span>
                        @endif

                        <a href="{{ $barangs->url($last) }}"
                            class="page-btn">
                            {{ $last }}
                        </a>

                    @endif


                    {{-- NEXT --}}
                    @if ($barangs->hasMorePages())

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

</div>


{{-- =========================================
     MODAL ADJUST DAN HAPUS
     Diletakkan di luar tabel
========================================= --}}

@hasanyrole('Super Admin|Admin Pusat')

    @foreach ($barangs as $barang)

        {{-- =================================
             MODAL ADJUST STOK
        ================================== --}}

        <div class="modal fade"
            id="editModal{{ $barang->id }}"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content barang-modal">

                    <form action="{{ route('databarang.barang.update', $barang->id) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-header">

                            <h5 class="modal-title">
                                <i class="bi bi-sliders me-2"></i>
                                Adjust Stok Barang
                            </h5>

                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>

                        </div>


                        <div class="modal-body">

                            {{-- KODE BARANG --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Kode Barang
                                </label>

                                <input type="text"
                                    name="kode_barang"
                                    class="form-control bg-light"
                                    value="{{ $barang->kode_barang }}"
                                    readonly>

                            </div>


                            {{-- DATA TERSEMBUNYI --}}
                            <input type="hidden"
                                name="nama_barang"
                                value="{{ $barang->nama_barang }}">

                            <input type="hidden"
                                name="kategori"
                                value="{{ $barang->kategori }}">

                            <input type="hidden"
                                name="satuan"
                                value="{{ $barang->satuan }}">

                            <input type="hidden"
                                name="lokasi"
                                value="{{ $barang->lokasi }}">


                            {{-- TOTAL STOK --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Total Stok Saat Ini
                                </label>

                                <input type="number"
                                    class="form-control bg-light"
                                    value="{{ $barang->stok }}"
                                    readonly>

                                <small class="text-muted">
                                    Total stok tidak dapat diedit secara langsung.
                                </small>

                            </div>


                            {{-- PENYESUAIAN STOK --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Penyesuaian Stok (+/-)
                                </label>

                                <input type="number"
                                    name="qty_koreksi"
                                    class="form-control"
                                    value="0"
                                    step="1"
                                    placeholder="Contoh: 10 atau -5">

                                <small class="text-muted">
                                    Gunakan angka positif untuk menambah stok
                                    dan angka negatif untuk mengurangi stok.
                                </small>

                            </div>


                            {{-- ALASAN --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Alasan Penyesuaian
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea name="alasan"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Contoh: Koreksi hasil stok opname"
                                    required
                                    maxlength="255">{{ old('alasan') }}</textarea>

                                @error('alasan')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror

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


        {{-- =================================
             MODAL HAPUS BARANG
        ================================== --}}

        <div class="modal fade"
            id="deleteModal{{ $barang->id }}"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content barang-modal">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            <i class="bi bi-trash me-2"></i>
                            Hapus Barang
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <p>
                            Apakah Anda yakin ingin menghapus
                            seluruh data penerimaan untuk barang berikut?
                        </p>

                        <div class="p-3 rounded"
                            style="background:#f3f6fa;">

                            <strong class="text-dark">
                                {{ $barang->nama_barang }}
                            </strong>

                            <div class="text-muted small mt-1">
                                Kode: {{ $barang->kode_barang }}
                            </div>

                        </div>

                        <div class="text-danger small mt-3">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            Perhatian: seluruh detail transaksi penerimaan
                            dengan kode barang ini akan ikut terhapus.

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Batal

                        </button>

                        <form action="{{ route('databarang.barang.destroy', $barang->id) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-trash me-1"></i>
                                Ya, Hapus

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    @endforeach

@endhasanyrole

@endsection
