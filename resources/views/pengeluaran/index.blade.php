
@extends('Layouts.main')

@section('container')
<style>
    .pengeluaran-page .search-container {
    width: 50%;
    margin: 0px 0 20px auto;
    }

    .pengeluaran-page .search-form {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .pengeluaran-page .search-input-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
    }

    .pengeluaran-page .search-icon {
        position: absolute;
        left: 15px;
        font-size: 23px;
        color: #94a3b8;
        pointer-events: none;
    }

    .pengeluaran-page .search-input {
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

    .pengeluaran-page .search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    }

    .pengeluaran-page .search-input::placeholder {
        color: #94a3b8;
    }

    .pengeluaran-page .search-clear {
        position: absolute;
        right: 13px;
        color: #94a3b8;
        font-size: 22px;
        text-decoration: none;
    }

    .pengeluaran-page .search-clear:hover {
        color: #ef4444;
    }

    .pengeluaran-page .search-button {
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

    .pengeluaran-page .search-button:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .pengeluaran-page .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 18px;
        padding: 12px 0;
    }

    .pengeluaran-page .pagination-info {
        font-size: 13px;
        color: #64748b;
    }

    .pengeluaran-page .pagination-links {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .pengeluaran-page .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #dbe2ea;
        border-radius: 6px;
        color: #334155;
        background: #fff;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
    }

    .pengeluaran-page .page-btn:hover {
        background: #eff6ff;
        color: #2563eb;
    }

    .pengeluaran-page .page-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .pengeluaran-page .page-btn.disabled {
        color: #94a3b8;
        background: #f1f5f9;
        cursor: not-allowed;
    }

    @media (max-width: 600px) {
        .pengeluaran-page .search-container {
            width: 100%;
        }

        .pengeluaran-page .search-form {
            flex-direction: column;
            align-items: stretch;
        }

        .pengeluaran-page .search-button {
            justify-content: center;
        }
    }
    .autocomplete-wrapper {
        position: relative;
        width: 100%;
    }

    .autocomplete-list {
        position: fixed !important;
        z-index: 999999 !important;
        background: #fff;
        border: 1px solid #d5dce5;
        border-radius: 7px;
        max-height: 220px;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.18);
        display: none;
    }

    .autocomplete-item {
        padding: 10px 12px;
        cursor: pointer;
        font-size: 13px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    .autocomplete-item:hover {
        background: #edf4ff;
    }

    .autocomplete-empty {
        padding: 10px 12px;
        color: #888;
        font-size: 13px;
    }

    /* =====================================
    PENGELUARAN BARANG - ATK MDI
    ===================================== */

    .pengeluaran-page {
        color: #24344d;
        font-size: 13px;
    }

    /* JUDUL HALAMAN */
    .pengeluaran-page h3 {
        color: #17365f;
        font-size: 21px;
        font-weight: 700;
    }

    .pengeluaran-page h5 {
        color: #17365f;
        font-size: 15px;
        font-weight: 650;
    }

    .pengeluaran-page .text-muted {
        color: #8291a8 !important;
        font-size: 12px;
    }

    /* CARD */
    .pengeluaran-page .card {
        border: 1px solid #e5ebf3;
        border-radius: 11px;
        box-shadow: 0 3px 12px rgba(25, 55, 95, .05) !important;
        overflow: hidden;
    }

    .pengeluaran-page .card-header {
        padding: 14px 18px;
        background: #fff;
        border-bottom: 1px solid #edf1f6;
    }

    .pengeluaran-page .card-body {
        padding: 18px;
    }

    /* LABEL FORM */
    .pengeluaran-page .form-label {
        color: #435570;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    /* INPUT DAN SELECT */
    .pengeluaran-page .form-control,
    .pengeluaran-page .form-select {
        min-height: 39px;
        border: 1px solid #dce4ef;
        border-radius: 7px;
        color: #34445d;
        background-color: #fff;
        font-size: 12px;
        box-shadow: none;
    }

    .pengeluaran-page textarea.form-control {
        min-height: 65px;
    }

    .pengeluaran-page .form-control:focus,
    .pengeluaran-page .form-select:focus {
        border-color: #4388df;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .09);
    }

    /* TOMBOL */
    .pengeluaran-page .btn {
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 14px;
        transition: .2s ease;
    }

    .pengeluaran-page .btn-primary {
        background: #2563c9;
        border-color: #2563c9;
    }

    .pengeluaran-page .btn-primary:hover {
        background: #174fae;
        border-color: #174fae;
    }

    .pengeluaran-page .btn-success {
        background: #16865b;
        border-color: #16865b;
    }

    .pengeluaran-page .btn-danger {
        background: #dc4c4c;
        border-color: #dc4c4c;
    }

    .pengeluaran-page .btn:hover {
        transform: translateY(-1px);
    }

    /* TABEL */
    .pengeluaran-page .table-responsive {
        border-radius: 8px;
    }

    .pengeluaran-page .table {
        margin-bottom: 0;
        font-size: 12px;
        color: #34445d;
        vertical-align: middle;
    }

    .pengeluaran-page .table thead th {
        padding: 12px 13px;
        background: #f4f7fc;
        color: #60738f;
        border-bottom: 1px solid #e4eaf2;
        border-top: none;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .35px;
        white-space: nowrap;
    }

    .pengeluaran-page .table tbody td {
        padding: 11px 13px;
        border-color: #edf1f6;
    }

    .pengeluaran-page .table tbody tr:hover {
        background: #f8fbff;
    }

    /* INPUT BARANG */
    .pengeluaran-page .kode-label,
    .pengeluaran-page .stok-label {
        font-size: 12px;
    }

    .pengeluaran-page .stok-label {
        color: #1769c2;
    }

    /* NOTIFIKASI */
    .pengeluaran-page .alert {
        border: none;
        border-radius: 8px;
        font-size: 12px;
        padding: 12px 15px;
    }

    /* GARIS PEMBATAS */
    .pengeluaran-page hr {
        border-color: #e8edf4;
        opacity: 1;
        margin: 18px 0;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .pengeluaran-page .card-body {
            padding: 13px;
        }

        .pengeluaran-page h3 {
            font-size: 18px;
        }

        .pengeluaran-page .table {
            min-width: 650px;
        }

        .pengeluaran-page .btn {
            padding: 7px 11px;
        }
    }
</style>

</style>
<div class="container-fluid py-4 pengeluaran-page">

    <!-- <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Pengeluaran Barang</h3>
            <p class="text-muted mb-0">Pencatatan barang keluar dari gudang cabang</p>
        </div>
    </div> -->

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PILIH CABANG UNTUK USER GLOBAL --}}
    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('pengeluaran.index') }}" method="GET">
                    <label class="form-label fw-semibold">Pilih Cabang Gudang</label>

                    <div class="row">
                        <div class="col-md-5">
                            <select name="branch_id"
                                    class="form-select"
                                    onchange="this.form.submit()">
                                <option value="">-- Pilih Cabang --</option>

                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ (string) $branchId === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- FORM PENGELUARAN --}}
    @can('pengeluaran.create')
        @if($branchId)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Form Pengeluaran Barang</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('pengeluaran.store') }}" method="POST">
                        @csrf

                        @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                            <input type="hidden" name="branch_id" value="{{ $branchId }}">
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Pengeluaran</label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Divisi / Bagian</label>
                                <input type="text"
                                       name="divisi"
                                       class="form-control"
                                       value="{{ old('divisi') }}"
                                       placeholder="Contoh: Divisi Keuangan"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nama Pengambil</label>
                                <input type="text"
                                       name="nama_pengambil"
                                       class="form-control"
                                       value="{{ old('nama_pengambil') }}"
                                       placeholder="Nama yang mengambil barang"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Keterangan tambahan">{{ old('keterangan') }}</textarea>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Barang Keluar</h5>

                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    id="btnTambahBarang">
                                + Tambah Barang
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="tabelBarang">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 28%">Nama Barang</th>
                                        <th>Kode Barang</th>
                                        <th style="width: 13%">Stok Tersedia</th>
                                        <th style="width: 13%">Jumlah Keluar</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="daftarBarang">
                                    <tr class="baris-barang">
                                        <td>
                                            <div class="autocomplete-wrapper">
                                                <input type="text"
                                                    class="form-control nama-barang-input"
                                                    placeholder="Ketik nama barang..."
                                                    autocomplete="off"
                                                    required>

                                                <div class="autocomplete-list"></div>

                                                <input type="hidden"
                                                    name="kode_barang[]"
                                                    class="kode-barang-input">
                                            </div>
                                        </td>

                                        <td>
                                            <span class="kode-label text-muted">-</span>
                                        </td>

                                        <td>
                                            <span class="stok-label fw-semibold">0</span>
                                        </td>

                                        <td>
                                            <input type="number"
                                                name="qty[]"
                                                class="form-control input-qty"
                                                min="1"
                                                value="1"
                                                required>
                                        </td>

                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm hapus-barang">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="alert alert-info py-2">
                            Pastikan jumlah pengeluaran tidak melebihi stok tersedia.
                            Stok akan berkurang setelah transaksi berhasil disimpan.
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                Simpan Pengeluaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
            <div class="alert alert-warning">
                Silakan pilih cabang terlebih dahulu untuk menampilkan stok barang.
            </div>
        @endif
    @endcan

    {{-- RIWAYAT PENGELUARAN --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Riwayat Pengeluaran Barang</h5>
        </div>
        <div class="card-body">
        {{-- SEARCH RIWAYAT PENGELUARAN --}}
        <div class="search-container">
            <form action="{{ url('/pengeluaran') }}" method="GET" class="search-form">

                <div class="search-input-wrapper">
                    <span class="search-icon">⌕</span>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Cari nomor, divisi, pengambil, kode atau nama barang..."
                        class="search-input"
                    >

                    @if(request('search'))
                        <a href="{{ url('/pengeluaran') }}"
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

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor Pengeluaran</th>
                            <th>Tanggal</th>
                            <th>Divisi</th>
                            <th>Nama Pengambil</th>
                            <th>Cabang</th>
                            <th>Total Jenis Barang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengeluarans as $index => $pengeluaran)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pengeluaran->nomor_pengeluaran }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-m-Y') }}
                                </td>
                                <td>{{ $pengeluaran->divisi }}</td>
                                <td>{{ $pengeluaran->nama_pengambil }}</td>
                                <td>{{ $pengeluaran->branch->nama_cabang ?? '-' }}</td>
                                <td>{{ $pengeluaran->details_count }} jenis</td>
                                <td>
                                    @can('pengeluaran.view')
                                        <a href="{{ route('pengeluaran.pdf', $pengeluaran->id) }}"
                                           target="_blank"
                                           class="btn btn-danger btn-sm">
                                            Cetak PDF
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada data pengeluaran barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- PAGINATION --}}
                @if ($pengeluarans->hasPages())
                    <div class="pagination-container">

                        <div class="pagination-info">
                            Menampilkan {{ $pengeluarans->firstItem() ?? 0 }}
                            sampai {{ $pengeluarans->lastItem() ?? 0 }}
                            dari {{ $pengeluarans->total() }} pengeluaran
                        </div>

                        <div class="pagination-links">

                            {{-- Previous --}}
                            @if ($pengeluarans->onFirstPage())
                                <span class="page-btn disabled">‹ Previous</span>
                            @else
                                <a href="{{ $pengeluarans->previousPageUrl() }}" class="page-btn">
                                    ‹ Previous
                                </a>
                            @endif

                            {{-- Nomor halaman --}}
                            @foreach ($pengeluarans->getUrlRange(1, $pengeluarans->lastPage()) as $page => $url)
                                @if ($page == $pengeluarans->currentPage())
                                    <span class="page-btn active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-btn">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($pengeluarans->hasMorePages())
                                <a href="{{ $pengeluarans->nextPageUrl() }}" class="page-btn">
                                    Next ›
                                </a>
                            @else
                                <span class="page-btn disabled">Next ›</span>
                            @endif

                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- JAVASCRIPT --}}
<script>

    document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // ELEMENT UTAMA
    // ==========================================

    const daftarBarang = document.getElementById('daftarBarang');
    const btnTambah = document.getElementById('btnTambahBarang');

    if (!daftarBarang) {
        console.error('Elemen daftarBarang tidak ditemukan.');
        return;
    }

    const form = daftarBarang.closest('form');

    // Data barang dari Controller
    const dataBarang = @js($dataBarang);

    // ==========================================
    // FUNGSI MENCARI BARANG
    // ==========================================

    function cariBarang(kode) {
        return dataBarang.find(function (item) {
            return String(item.kode) === String(kode);
        });
    }

    // ==========================================
    // MEMBUAT DROPDOWN AUTOCOMPLETE
    // ==========================================

    function buatDropdown(input) {

        // Jika dropdown sudah dibuat, gunakan kembali
        if (input._autocompleteList) {
            return input._autocompleteList;
        }

        const list = document.createElement('div');

        list.className = 'autocomplete-list';

        Object.assign(list.style, {
            position: 'fixed',
            display: 'none',
            zIndex: '999999',
            background: '#fff',
            border: '1px solid #d5dce5',
            borderRadius: '7px',
            maxHeight: '220px',
            overflowY: 'auto',
            overflowX: 'hidden',
            boxShadow: '0 5px 15px rgba(0,0,0,0.18)'
        });

        // Dropdown dipindahkan ke body
        document.body.appendChild(list);

        input._autocompleteList = list;

        return list;
    }

    // ==========================================
    // MENGATUR POSISI DROPDOWN
    // ==========================================

    function aturPosisiDropdown(input, list) {

        const rect = input.getBoundingClientRect();

        // Jika input berada di luar layar, sembunyikan
        if (
            rect.bottom <= 0 ||
            rect.top >= window.innerHeight
        ) {
            list.style.display = 'none';
            return;
        }

        const tinggiMaksimal = 220;
        const ruangBawah = window.innerHeight - rect.bottom;
        const ruangAtas = rect.top;

        let top;
        let maxHeight = tinggiMaksimal;

        // Jika ruang bawah sempit, tampilkan ke atas
        if (
            ruangBawah < 150 &&
            ruangAtas > ruangBawah
        ) {
            maxHeight = Math.min(tinggiMaksimal, ruangAtas - 5);

            list.style.maxHeight = maxHeight + 'px';
            list.style.top = 'auto';
            list.style.bottom = (window.innerHeight - rect.top) + 'px';

        } else {

            maxHeight = Math.min(tinggiMaksimal, ruangBawah - 5);

            top = rect.bottom;

            list.style.top = top + 'px';
            list.style.bottom = 'auto';
        }

        list.style.left = rect.left + 'px';
        list.style.width = rect.width + 'px';
        list.style.maxHeight = Math.max(60, maxHeight) + 'px';
        list.style.zIndex = '999999';
    }

    // ==========================================
    // MENUTUP DROPDOWN
    // ==========================================

    function tutupDropdown(input) {

        if (input && input._autocompleteList) {
            input._autocompleteList.style.display = 'none';
        }
    }

    // ==========================================
    // MENAMPILKAN HASIL AUTOCOMPLETE
    // ==========================================

    function tampilkanAutocomplete(input) {

        const wrapper = input.closest('.autocomplete-wrapper');

        if (!wrapper) return;

        const list = buatDropdown(input);

        const keyword = input.value.trim().toLowerCase();

        const row = input.closest('.baris-barang');

        const kodeInput = row.querySelector('.kode-barang-input');
        const kodeLabel = row.querySelector('.kode-label');
        const stokLabel = row.querySelector('.stok-label');

        // Setiap kali mengetik, pilihan lama dibatalkan
        kodeInput.value = '';
        kodeLabel.textContent = '-';
        stokLabel.textContent = '0';

        const qtyInput = row.querySelector('.input-qty');

        qtyInput.removeAttribute('max');
        qtyInput.setCustomValidity('');

        list.innerHTML = '';

        if (keyword.length === 0) {
            tutupDropdown(input);
            return;
        }

        // Cari berdasarkan nama atau kode
        const hasil = dataBarang.filter(function (item) {

            return (
                String(item.nama).toLowerCase().includes(keyword) ||
                String(item.kode).toLowerCase().includes(keyword)
            );

        });

        if (hasil.length === 0) {

            const empty = document.createElement('div');

            empty.className = 'autocomplete-empty';
            empty.textContent = 'Barang tidak ditemukan';

            list.appendChild(empty);

        } else {

            hasil.forEach(function (item) {

                const option = document.createElement('div');

                option.className = 'autocomplete-item';

                option.style.padding = '10px 12px';
                option.style.cursor = 'pointer';
                option.style.borderBottom = '1px solid #eee';
                option.style.fontSize = '13px';

                option.textContent =
                    item.nama + ' (' + item.kode + ') - Stok: ' +
                    item.stok + ' ' + (item.satuan || '');

                // Hover
                option.addEventListener('mouseenter', function () {
                    option.style.background = '#edf4ff';
                });

                option.addEventListener('mouseleave', function () {
                    option.style.background = '#fff';
                });

                // Pilih barang
                option.addEventListener('mousedown', function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    input.value = item.nama;

                    kodeInput.value = item.kode;

                    kodeLabel.textContent = item.kode;

                    stokLabel.textContent =
                        item.stok + ' ' + (item.satuan || '');

                    qtyInput.max = item.stok;

                    qtyInput.value = 1;

                    qtyInput.setCustomValidity('');

                    tutupDropdown(input);

                    cekBarangSama();

                });

                list.appendChild(option);

            });

        }

        list.style.display = 'block';

        aturPosisiDropdown(input, list);
    }

    // ==========================================
    // VALIDASI JUMLAH BARANG KELUAR
    // ==========================================

    function validasiStok(row) {

        const kodeInput = row.querySelector('.kode-barang-input');
        const qtyInput = row.querySelector('.input-qty');

        const kode = kodeInput.value;

        const barang = cariBarang(kode);

        qtyInput.setCustomValidity('');

        if (!kode || !barang) {
            qtyInput.setCustomValidity(
                'Silakan pilih barang dari daftar autocomplete.'
            );
            return false;
        }

        const qty = Number(qtyInput.value);
        const stok = Number(barang.stok);

        if (!Number.isInteger(qty) || qty < 1) {

            qtyInput.setCustomValidity(
                'Jumlah barang keluar minimal 1.'
            );

            return false;
        }

        if (qty > stok) {

            qtyInput.setCustomValidity(
                'Jumlah keluar melebihi stok tersedia. Stok saat ini: ' +
                stok
            );

            return false;
        }

        return true;
    }

    // ==========================================
    // CEK BARANG DUPLIKAT
    // ==========================================

    function cekBarangSama() {

        const semuaKode = daftarBarang.querySelectorAll(
            '.kode-barang-input'
        );

        const kodeTerpilih = [];

        for (const input of semuaKode) {

            const kode = input.value;

            if (!kode) continue;

            if (kodeTerpilih.includes(kode)) {

                alert(
                    'Barang dengan kode ' + kode +
                    ' sudah ditambahkan. Silakan pilih barang lain.'
                );

                const row = input.closest('.baris-barang');

                const namaInput = row.querySelector(
                    '.nama-barang-input'
                );

                namaInput.value = '';

                input.value = '';

                row.querySelector('.kode-label').textContent = '-';

                row.querySelector('.stok-label').textContent = '0';

                return false;
            }

            kodeTerpilih.push(kode);
        }

        return true;
    }

    // ==========================================
    // TAMBAH BARIS BARANG
    // ==========================================

    if (btnTambah) {

        btnTambah.addEventListener('click', function () {

            const barisPertama = daftarBarang.querySelector(
                '.baris-barang'
            );

            if (!barisPertama) return;

            const barisBaru = barisPertama.cloneNode(true);

            // Reset input nama
            const namaInput = barisBaru.querySelector(
                '.nama-barang-input'
            );

            namaInput.value = '';

            namaInput._autocompleteList = null;

            // Reset kode
            barisBaru.querySelector(
                '.kode-barang-input'
            ).value = '';

            // Reset label
            barisBaru.querySelector(
                '.kode-label'
            ).textContent = '-';

            barisBaru.querySelector(
                '.stok-label'
            ).textContent = '0';

            // Reset jumlah
            const qtyInput = barisBaru.querySelector('.input-qty');

            qtyInput.value = 1;
            qtyInput.removeAttribute('max');
            qtyInput.setCustomValidity('');

            // Hapus dropdown bawaan hasil clone jika ada
            const dropdownLama = barisBaru.querySelector(
                '.autocomplete-list'
            );

            if (dropdownLama) {
                dropdownLama.remove();
            }

            daftarBarang.appendChild(barisBaru);

            namaInput.focus();

        });

    }

    // ==========================================
    // EVENT INPUT AUTOCOMPLETE
    // ==========================================

    daftarBarang.addEventListener('input', function (e) {

        if (e.target.classList.contains('nama-barang-input')) {

            tampilkanAutocomplete(e.target);

        }

        if (e.target.classList.contains('input-qty')) {

            const row = e.target.closest('.baris-barang');

            validasiStok(row);

        }

    });

    // ==========================================
    // EVENT FOCUS INPUT
    // ==========================================

    daftarBarang.addEventListener('focusin', function (e) {

        if (
            e.target.classList.contains('nama-barang-input') &&
            e.target.value.trim() !== ''
        ) {
            tampilkanAutocomplete(e.target);
        }

    });

    // ==========================================
    // HAPUS BARIS BARANG
    // ==========================================

    daftarBarang.addEventListener('click', function (e) {

        if (e.target.classList.contains('hapus-barang')) {

            const semuaBaris = daftarBarang.querySelectorAll(
                '.baris-barang'
            );

            if (semuaBaris.length <= 1) {

                alert('Minimal harus ada satu baris barang.');

                return;
            }

            const row = e.target.closest('.baris-barang');

            const input = row.querySelector('.nama-barang-input');

            tutupDropdown(input);

            if (input._autocompleteList) {
                input._autocompleteList.remove();
            }

            row.remove();

        }

    });

    // ==========================================
    // KLIK DI LUAR DROPDOWN
    // ==========================================

    document.addEventListener('click', function (e) {

        if (
            e.target.closest('.autocomplete-wrapper') ||
            e.target.closest('.autocomplete-list')
        ) {
            return;
        }

        document.querySelectorAll('.nama-barang-input').forEach(
            function (input) {
                tutupDropdown(input);
            }
        );

    });

    // ==========================================
    // UPDATE POSISI SAAT SCROLL
    // ==========================================

    window.addEventListener('scroll', function () {

        document.querySelectorAll('.nama-barang-input').forEach(
            function (input) {

                const list = input._autocompleteList;

                if (list && list.style.display === 'block') {

                    aturPosisiDropdown(input, list);

                }

            }
        );

    }, true);

    // ==========================================
    // UPDATE POSISI SAAT RESIZE
    // ==========================================

    window.addEventListener('resize', function () {

        document.querySelectorAll('.nama-barang-input').forEach(
            function (input) {

                const list = input._autocompleteList;

                if (list && list.style.display === 'block') {

                    aturPosisiDropdown(input, list);

                }

            }
        );

    });

    // ==========================================
    // VALIDASI SEBELUM FORM DIKIRIM
    // ==========================================

    if (form) {

        form.addEventListener('submit', function (e) {

            const semuaBaris = daftarBarang.querySelectorAll(
                '.baris-barang'
            );

            let valid = true;

            for (const row of semuaBaris) {

                const namaInput = row.querySelector(
                    '.nama-barang-input'
                );

                const kodeInput = row.querySelector(
                    '.kode-barang-input'
                );

                if (
                    !namaInput.value.trim() ||
                    !kodeInput.value
                ) {

                    e.preventDefault();

                    alert(
                        'Silakan pilih nama barang dari daftar autocomplete.'
                    );

                    namaInput.focus();

                    valid = false;

                    break;
                }

                if (!validasiStok(row)) {

                    e.preventDefault();

                    row.querySelector('.input-qty').reportValidity();

                    valid = false;

                    break;
                }

            }

            if (valid && !cekBarangSama()) {

                e.preventDefault();

                valid = false;

            }

            if (!valid) return;

            // Tutup semua dropdown sebelum submit
            document.querySelectorAll('.autocomplete-list').forEach(
                function (list) {
                    list.style.display = 'none';
                }
            );

        });

    }

    });

</script>

@endsection
