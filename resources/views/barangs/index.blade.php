@extends('Layouts.main')

@section('container')

<style>
    thead th {
    vertical-align: middle;
    white-space: nowrap;
    }

    thead th form {
        margin: 0;
        min-width: 110px;
    }

    thead th select.form-select {
        min-width: 110px;
        font-size: 12px;
        cursor: pointer;
        border-radius: 6px;
    }
    
    thead th {
    vertical-align: middle;
    white-space: nowrap;
    }

    thead th select.form-select {
        min-width: 150px;
        font-size: 12px;
        cursor: pointer;
    }

    thead th form {
        margin: 0;
    }

    /* PAGINATION */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .pagination-info {
        font-size: 13px;
        color: #6c757d;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .page-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .page-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .page-btn.disabled {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 576px) {
        .pagination-container {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    .search-container {
        width: 50%;
        margin: 15px 0 20px auto;
    }

    .search-form {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        width: 100%;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        font-size: 23px;
        color: #94a3b8;
        pointer-events: none;
    }

    .search-input {
        width: 99%;
        height: 44px;
        padding: 0 42px;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #334155;
        font-size: 13px;
        transition: .2s;
    }

    .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    }

    .search-clear {
        position: absolute;
        right: 13px;
        color: #94a3b8;
        font-size: 22px;
        text-decoration: none;
    }

    .search-clear:hover {
        color: #ef4444;
    }

    .search-button {
        height: 44px;
        padding: 0 22px;
        border: none;
        border-radius: 9px;
        background: #2563eb;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .search-button:hover {
        background: #1d4ed8;
    }

    .modal {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1055;
        background-color: rgba(0, 0, 0, .05);
        backdrop-filter: blur(1px);
    }

    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .search-container {
            width: 100%;
            margin: 15px 0 20px;
        }
    }

    @media (max-width: 500px) {
        .search-form {
            flex-wrap: wrap;
        }

        .search-input-wrapper {
            flex-basis: 100%;
        }

        .search-button {
            flex: 1;
        }
    }
</style>

<div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Master Barang</h2>
            <small class="text-muted">
                Katalog barang / Master barang.
            </small>
        </div>

        @can('barang.create')
            <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </button>
        @endcan
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INFORMASI DAN PENCARIAN --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <span class="text-muted" style="font-size: 12px;">
            Total: {{ $barangs->total() }} barang
        </span>

        <div class="search-container">
            <form action="{{ request()->url() }}"
                method="GET"
                class="search-form">

                @if($isGlobalUser && request('branch_id'))
                    <input type="hidden" name="branch_id"
                        value="{{ request('branch_id') }}">
                @endif

                <div class="search-input-wrapper">
                    <span class="search-icon">⌕</span>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kode atau nama barang..."
                        class="search-input"
                    >

                    @if(request('q'))
                        <a href="{{ request()->url() }}{{ request('branch_id') ? '?branch_id='.request('branch_id') : '' }}"
                            class="search-clear"
                            title="Hapus pencarian">
                            &times;
                        </a>
                    @endif
                </div>

                <button type="submit" class="search-button">
                    Search
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
                    <th>Stok Akhir</th>
                    <th>Harga Rata-rata</th>
                    <th>
                        <form method="GET" action="{{ route('barangs.index') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="saldo"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <option value="">Semua Saldo</option>
                                <option value="minus" {{ request('saldo') == 'minus' ? 'selected' : '' }}>
                                    Saldo Minus
                                </option>
                                <option value="nol" {{ request('saldo') == 'nol' ? 'selected' : '' }}>
                                    Saldo Nol
                                </option>
                                <option value="positif" {{ request('saldo') == 'positif' ? 'selected' : '' }}>
                                    Saldo Positif
                                </option>
                            </select>
                        </form>
                    </th>
                    @if($isGlobalUser)
                    <th>
                        <div style="min-width: 170px;">
                            <form action="{{ request()->url() }}" method="GET">
                                {{-- Pertahankan pencarian --}}
                                @if(request('q'))
                                    <input type="hidden" name="q" value="{{ request('q') }}">
                                @endif

                                {{-- Pertahankan filter lainnya jika ada --}}
                                @if(request('q_status'))
                                    <input type="hidden" name="q_status" value="{{ request('q_status') }}">
                                @endif

                                <select name="branch_id"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">

                                    <option value="">Cabang</option>

                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            @selected(request('branch_id') == $branch->id)>
                                            {{ $branch->kode_cabang }} -
                                            {{ $branch->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </th>
                    @endif
                    <th width="150">AKSI</th>
                </tr>
            </thead>

            <tbody>

                @forelse($barangs as $barang)

                    <tr>
                        <td>{{ $barang->kode_barang }}</td>
                        <td>{{ $barang->nama_barang }}</td>
                        <td>{{ $barang->kategori ?? '-' }}</td>
                        <td>{{ $barang->satuan ?? '-' }}</td>
                        <td>{{ $barang->lokasi ?? '-' }}</td>
                        <td>
                            {{ number_format($barang->stok ?? 0, 0, ',', '.') }}
                            {{ $barang->satuan }}
                        </td>

                        <td>
                            Rp {{ number_format($barang->harga_rata_rata ?? 0, 0, ',', '.') }}
                        </td>

                        <td>
                            <strong>
                                Rp {{ number_format($barang->nilai_saldo ?? 0, 0, ',', '.') }}
                            </strong>
                        </td>

                        @if($isGlobalUser)
                            <td>
                                {{ $barang->branch?->nama_cabang ?? 'Belum ditentukan' }}
                            </td>
                        @endif

                        <td>
                            @can('barang.update')
                                <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $barang->id }}">
                                    Edit
                                </button>
                            @endcan

                            @can('barang.delete')
                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $barang->id }}">
                                    Hapus
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty

                    <tr>
                        <td colspan="{{ $isGlobalUser ? 10 : 9 }}"
                            class="text-center">
                            Belum ada Master Barang.
                        </td>
                    </tr>

                @endforelse
            </tbody>
        </table>
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
                                        <h5 class="modal-title">Edit Master Barang</h5>

                                        <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        @if($isGlobalUser)
                                            <div class="mb-3">
                                                <label class="form-label">Cabang</label>

                                                <select name="branch_id"
                                                    class="form-select" required>

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
                                        <input type="hidden" name="edit_barang_id" value="{{ $barang->id }}">
                                        <input type="hidden" name="kode_barang" value="{{ $barang->kode_barang }}">

                                        <div class="mb-3">
                                            <label class="form-label">Kode Barang</label>

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
                                            <label class="form-label">Nama Barang</label>

                                            <input type="text"
                                                name="nama_barang"
                                                class="form-control"
                                                value="{{ $barang->nama_barang }}"
                                                maxlength="255"
                                                oninput="this.value=this.value.toUpperCase()"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>

                                            <select name="kategori" class="form-select">
                                                <option value="">-- Pilih Kategori --</option>

                                                @if($barang->kategori && !in_array($barang->kategori, $kategoriOptions))
                                                    <option value="{{ $barang->kategori }}" selected>
                                                        {{ $barang->kategori }}
                                                    </option>
                                                @endif

                                                @foreach($kategoriOptions as $kategori)
                                                    <option value="{{ $kategori }}"
                                                        @selected(old('kategori', $barang->kategori) == $kategori)>
                                                        {{ $kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Satuan</label>

                                            <select name="satuan" class="form-select">
                                                <option value="">-- Pilih Satuan --</option>

                                                @if($barang->satuan && !in_array($barang->satuan, $satuanOptions))
                                                    <option value="{{ $barang->satuan }}" selected>
                                                        {{ $barang->satuan }}
                                                    </option>
                                                @endif

                                                @foreach($satuanOptions as $satuan)
                                                    <option value="{{ $satuan }}"
                                                        @selected(old('satuan', $barang->satuan) == $satuan)>
                                                        {{ $satuan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Lokasi</label>

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
                                    <h5 class="modal-title">Hapus Master Barang</h5>

                                    <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    Apakah Anda yakin ingin menghapus barang ini?

                                    <br><br>

                                    <strong>{{ $barang->nama_barang }}</strong>

                                    <br>

                                    Kode: {{ $barang->kode_barang }}

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
                                            Ya, Hapus
                                        </button>

                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>
                    @endcan
        @endforeach

        
    @if ($barangs->hasPages())
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
            @if ($barangs->onFirstPage())
                <span class="page-btn disabled">‹ Previous</span>
            @else
                <a href="{{ $barangs->previousPageUrl() }}"
                   class="page-btn">
                    ‹ Previous
                </a>
            @endif

            {{-- HALAMAN PERTAMA --}}
            @if ($start > 1)
                <a href="{{ $barangs->url(1) }}"
                   class="page-btn">1</a>

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

{{-- MODAL TAMBAH BARANG --}}
@can('barang.create')
<div class="modal fade"
    id="createModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('barangs.store') }}" method="POST">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Master Barang</h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    @if($isGlobalUser)
                        <div class="mb-3">
                            <label class="form-label">Cabang</label>

                            <select name="branch_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Cabang --</option>

                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">
                                        {{ $branch->kode_cabang }} -
                                        {{ $branch->nama_cabang }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>

                        <input type="text"
                            name="kode_barang"
                            class="form-control"
                            maxlength="50"
                            oninput="this.value=this.value.toUpperCase()"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>

                        <input type="text"
                            name="nama_barang"
                            class="form-control"
                            maxlength="255"
                            oninput="this.value=this.value.toUpperCase()"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoriOptions as $kategori)
                                <option value="{{ $kategori }}"
                                    {{ old('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan</label>
                        <select name="satuan" id="satuan" class="form-select">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($satuanOptions as $satuan)
                                <option value="{{ $satuan }}"
                                    {{ old('satuan') == $satuan ? 'selected' : '' }}>
                                    {{ $satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>

                        <input type="text"
                            name="lokasi"
                            class="form-control"
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
                        Simpan Barang
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endcan

<script>
    document.addEventListener('DOMContentLoaded', function () {

        @if($errors->any())
            const oldMethod = @json(old('_method', 'POST'));

            let modalId = 'createModal';

            if (oldMethod === 'PUT' || oldMethod === 'PATCH') {
                // Modal edit dibuka kembali melalui penanda session.
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
