@extends('Layouts.main')

@section('container')
<!-- STYLE POPUP EDIT/DELETE -->
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

    <h2 class="mb-4">Kelola Barang</h2>

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

    {{-- NOTIFIKASI ERROR VALIDASI --}}
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

    {{-- TABEL DAFTAR BARANG --}}
    <div class="table-responsive">

    {{-- FORM PENCARIAN --}}
<form id="filterBarangForm" method="GET" action="{{ request()->url() }}">
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
                form="filterBarangForm"
                class="btn btn-primary btn-sm">
            <i class="bi bi-search"></i> Cari
        </button>
    </div>
</div>

<table class="table table-bordered table-striped">

    <thead>
        {{-- JUDUL KOLOM --}}
        <tr>
            <th>KODE</th>
            <th>NAMA BARANG</th>
            <th>KATEGORI</th>
            <th>STOK</th>
            <th>LOKASI</th>
            <th>STATUS</th>
            @hasanyrole('Super Admin|Admin Pusat')
            <th>AKSI</th>
            @endhasanyrole
        </tr>

        {{-- INPUT PENCARIAN PER KOLOM --}}
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
                        <td>{{ $barang->kode_barang }}</td>

                        <td>{{ $barang->nama_barang }}</td>

                        <td>{{ $barang->kategori }}</td>

                        <td>
                            {{ $barang->stok }}
                            {{ $barang->satuan }}
                        </td>

                        <td>
                            {{ $barang->nama_cabang ?? '-' }}
                            {{ $barang->lokasi ? ' - ' . $barang->lokasi : '' }}
                        </td>

                        <td>
                            @if ($barang->stok > 0)
                                <span class="badge bg-success">
                                    Tersedia
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Habis
                                </span>
                            @endif
                        </td>

                        @hasanyrole('Super Admin|Admin Pusat')
                        <td>
                            {{-- TOMBOL EDIT --}}
                            <button type="button"
                                class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $loop->index }}">
                                Edit
                            </button>

                            <button type="button"
                                class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal{{ $loop->index }}">
                                Hapus
                            </button>
                        </td>
                        @endhasanyrole
                    </tr>

                    {{-- ================================= --}}
                    {{-- POPUP EDIT BARANG --}}
                    {{-- ================================= --}}

                    <div class="modal fade"
                        id="editModal{{ $loop->index }}"
                        tabindex="-1"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <form
                                    action="{{ route('databarang.barang.update', ['kode_barang' => $barang->kode_barang]) }}"
                                    method="POST">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Edit Data Barang
                                        </h5>

                                        <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <div class="modal-body">

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

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nama Barang
                                            </label>

                                            <input type="text"
                                                name="nama_barang"
                                                class="form-control"
                                                value="{{ $barang->nama_barang }}"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Kategori
                                            </label>

                                            <input type="text"
                                                name="kategori"
                                                class="form-control"
                                                value="{{ $barang->kategori }}"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Satuan
                                            </label>

                                            <input type="text"
                                                name="satuan"
                                                class="form-control"
                                                value="{{ $barang->satuan }}"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">
                                                Lokasi
                                            </label>

                                            <input type="text"
                                                name="lokasi"
                                                class="form-control"
                                                value="{{ $barang->lokasi }}"
                                                required>
                                        </div>                  
                                        {{-- TOTAL STOK (TERKUNCI) --}}
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

                                        {{-- ALASAN PENYESUAIAN --}}
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Alasan Penyesuaian
                                            </label>

                                            <textarea name="alasan"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Contoh: Koreksi hasil stok opname"></textarea>
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
                    {{-- POPUP DELETE BARANG --}}
                    {{-- ================================= --}}

                    <div class="modal fade"
                        id="deleteModal{{ $loop->index }}"
                        tabindex="-1"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">
                                        Hapus Barang
                                    </h5>

                                    <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>

                                </div>

                                <div class="modal-body">

                                    Apakah Anda yakin ingin menghapus
                                    seluruh data penerimaan untuk barang:

                                    <br><br>

                                    <strong>
                                        {{ $barang->nama_barang }}
                                    </strong>

                                    <br>

                                    Kode:
                                    {{ $barang->kode_barang }}

                                    <br><br>

                                    <span class="text-danger">
                                        Perhatian: seluruh detail transaksi
                                        penerimaan dengan kode barang ini
                                        akan ikut terhapus.
                                    </span>

                                </div>

                                <div class="modal-footer">

                                    <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                        Batal
                                    </button>

                                    <form
                                        action="{{ route('databarang.barang.destroy', ['kode_barang' => $barang->kode_barang]) }}"
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
                        <td colspan="7" class="text-center">
                            Belum ada data barang.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
