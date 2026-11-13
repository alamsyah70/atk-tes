@extends('Layouts.main')

@section('container')

<style>
    /* SEARCH SUPPLIER */
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

    .search-input::placeholder {
        color: #94a3b8;
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
        display: flex;
        align-items: center;
        gap: 7px;
        transition: .2s;
    }

    .search-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .search-reset {
        height: 44px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 9px;
        font-size: 13px;
        white-space: nowrap;
        
    }

    /* RESPONSIVE */
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

        .search-button,
        .search-reset {
            flex: 1;
        }
    }
    
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

{{-- NOTIFIKASI ERROR --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>
    </div>
@endif

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Supplier</h2>

        @can('supplier.create')
        <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </button>
        @endcan
    </div>

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    <div class="table-responsive">

        {{-- FORM PENCARIAN --}}
        <form id="filterSupplierForm"
            method="GET"
            action="{{ request()->url() }}">
        </form>
            {{-- INFORMASI DAN SEARCH SUPPLIER --}}

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                {{-- TOTAL SUPPLIER --}}
                <span class="text-muted" style="font-size: 12px;">
                    Total: {{ $suppliers->total() }} supplier
                </span>

                {{-- SEARCH DI SEBELAH KANAN --}}
                <div class="search-container">

                    <form action="{{ request()->url() }}"
                        method="GET"
                        class="search-form">

                        <div class="search-input-wrapper">

                            <span class="search-icon">⌕</span>

                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Cari supplier..."
                                class="search-input"
                            >

                            @if(request('q'))
                                <a href="{{ request()->url() }}"
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

        </div>

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>KODE SUPPLIER</th>
                    <th>NAMA SUPPLIER</th>
                    <th>ALAMAT</th>
                    <th>NO. TELEPON</th>
                    <th>EMAIL</th>

                    @if ($isGlobalUser)
                        <th>CABANG</th>
                    @endif

                    <th>AKSI</th>
                </tr>

                {{-- PENCARIAN --}}
            </thead>

            <tbody>

                @forelse ($suppliers as $supplier)

                    <tr>
                        <td>{{ $supplier->kode_supplier }}</td>
                        <td>{{ $supplier->nama_supplier }}</td>
                        <td>{{ $supplier->alamat ?? '-' }}</td>
                        <td>{{ $supplier->no_telepon ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>

                        @if ($isGlobalUser)
                            <td>
                                {{ $supplier->branch?->nama_cabang ?? 'Belum ditentukan' }}
                            </td>
                        @endif

                        <td>
                            @can('supplier.update')
                            <button type="button"
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $supplier->id }}">
                                Edit
                            </button>
                            @endcan

                            @can('supplier.delete')
                            <button type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal{{ $supplier->id }}">
                                Hapus
                            </button>
                            @endcan
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
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
                                        <h5 class="modal-title">Edit Supplier</h5>
                                        <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label class="form-label">Kode Supplier</label>
                                            <input type="text"
                                                name="kode_supplier"
                                                class="form-control bg-light"
                                                value="{{ $supplier->kode_supplier }}"
                                                readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nama Supplier</label>
                                            <input type="text"
                                                name="nama_supplier"
                                                class="form-control"
                                                value="{{ $supplier->nama_supplier }}"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Alamat</label>
                                            <textarea name="alamat"
                                                class="form-control"
                                                rows="3">{{ $supplier->alamat }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">No. Telepon</label>
                                            <input type="text"
                                                name="no_telepon"
                                                class="form-control"
                                                value="{{ $supplier->no_telepon }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email"
                                                name="email"
                                                class="form-control"
                                                value="{{ $supplier->email }}">
                                        </div>

                                        @if ($isGlobalUser)
                                        <div class="mb-3">
                                            <label class="form-label">Cabang</label>
                                            <select name="branch_id"
                                                class="form-select">

                                                <option value="">
                                                    Belum ditentukan
                                                </option>

                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $supplier->branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->nama_cabang }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>
                                        @endif

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

                    {{-- MODAL HAPUS --}}
                    <div class="modal fade"
                        id="deleteModal{{ $supplier->id }}"
                        tabindex="-1"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Hapus Supplier</h5>
                                    <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus supplier:

                                    <br><br>

                                    <strong>{{ $supplier->nama_supplier }}</strong>

                                    <br>
                                    Kode: {{ $supplier->kode_supplier }}
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
                                            Ya, Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                @empty

                    <tr>
                        <td colspan="{{ $isGlobalUser ? 7 : 6 }}"
                            class="text-center">
                            Belum ada data supplier.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="d-flex justify-content-end">
            {{ $suppliers->links() }}
        </div>

    </div>
</div>

{{-- MODAL TAMBAH SUPPLIER --}}
@can('supplier.create')
<div class="modal fade"
    id="createModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('supplier.store') }}" method="POST">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Supplier</h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Kode Supplier</label>
                        <input type="text"
                            name="kode_supplier"
                            class="form-control"
                            value="{{ old('kode_supplier') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text"
                            name="nama_supplier"
                            class="form-control"
                            value="{{ old('nama_supplier') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                            class="form-control"
                            rows="3">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text"
                            name="no_telepon"
                            class="form-control"
                            value="{{ old('no_telepon') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}">
                    </div>

                    @if ($isGlobalUser)
                    <div class="mb-3">
                        <label class="form-label">Cabang</label>

                        <select name="branch_id" class="form-select">
                            <option value="">Belum ditentukan</option>

                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Simpan Supplier
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endcan

@endsection
