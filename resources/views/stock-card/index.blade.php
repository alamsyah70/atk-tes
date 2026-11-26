@extends('Layouts.main')

@section('container')

<style>
    /* Samakan tinggi area filter */
    .stock-card-page .filter-row {
        align-items: end;
    }

    .stock-card-page .filter-action {
        display: flex;
        align-items: end;
    }

    .stock-card-page .filter-action .btn-filter {
        min-height: 39px;
        white-space: nowrap;
    }
    
    .stock-card-page {
        color: #26364a;
    }

    .stock-card-page .page-title {
        font-size: 23px;
        font-weight: 700;
        color: #243b53;
    }

    .stock-card-page .page-subtitle {
        color: #8291a5;
        font-size: 13px;
    }

    .stock-card-page .panel {
        background: #fff;
        border: 1px solid #e7edf4;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(31, 55, 80, .04);
        margin-bottom: 18px;
    }

    .stock-card-page .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #526579;
        margin-bottom: 7px;
    }

    .stock-card-page .form-control,
    .stock-card-page .form-select {
        min-height: 39px;
        border-color: #dce5ee;
        border-radius: 7px;
        font-size: 13px;
    }

    .stock-card-page .form-control:focus,
    .stock-card-page .form-select:focus {
        border-color: #7da5cf;
        box-shadow: 0 0 0 3px rgba(49, 95, 145, .10);
    }

    .stock-card-page .btn-filter {
        background: #315f91;
        color: white;
        border: none;
        border-radius: 7px;
        padding: 9px 17px;
        font-size: 13px;
    }

    .stock-card-page .btn-filter:hover {
        background: #244b75;
        color: white;
    }

    .stock-card-page .btn-reset {
        background: #edf2f7;
        color: #526579;
        border-radius: 7px;
        padding: 9px 15px;
        font-size: 13px;
        text-decoration: none;
    }

    .stock-card-page .summary-card {
        border: 1px solid #e7edf4;
        border-radius: 10px;
        padding: 15px;
        height: 100%;
        background: #fff;
    }

    .stock-card-page .summary-label {
        color: #8291a5;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .stock-card-page .summary-value {
        color: #263b53;
        font-size: 21px;
        font-weight: 700;
    }

    .stock-card-page .table-responsive {
        border-radius: 8px;
    }

    .stock-card-page table {
        min-width: 950px;
        margin-bottom: 0;
    }

    .stock-card-page thead th {
        background: #f3f6fa;
        color: #53677e;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 12px 10px;
        white-space: nowrap;
        border-bottom: 1px solid #e4eaf1;
    }

    .stock-card-page tbody td {
        font-size: 12px;
        padding: 11px 10px;
        vertical-align: middle;
        border-color: #edf1f5;
    }

    .stock-card-page .badge-type {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
    }

    .stock-card-page .badge-in {
        background: #e7f5ed;
        color: #287a4b;
    }

    .stock-card-page .badge-out {
        background: #fff0e9;
        color: #b85b32;
    }

    .stock-card-page .badge-adjust {
        background: #edf1fb;
        color: #4c64a2;
    }

    .stock-card-page .text-in {
        color: #287a4b;
        font-weight: 700;
    }

    .stock-card-page .text-out {
        color: #bd603a;
        font-weight: 700;
    }

    .stock-card-page .text-adjust {
        color: #4c64a2;
        font-weight: 700;
    }

    .stock-card-page .empty-state {
        padding: 35px 15px;
        text-align: center;
        color: #8795a7;
        font-size: 13px;
    }

    /* AUTOCOMPLETE BARANG */
    .stock-card-page .barang-autocomplete {
        position: relative;
        width: 100%;
    }

    .stock-card-page .barang-suggestions {
        display: none;
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        z-index: 1100;
        background: #fff;
        border: 1px solid #dce5ee;
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(31, 55, 80, .12);
        max-height: 250px;
        overflow-y: auto;
    }

    .stock-card-page .barang-suggestion-item {
        display: block;
        width: 100%;
        border: 0;
        border-bottom: 1px solid #edf1f5;
        background: #fff;
        padding: 10px 12px;
        text-align: left;
        cursor: pointer;
        font-size: 12px;
        color: #34495e;
    }

    .stock-card-page .barang-suggestion-item:last-child {
        border-bottom: 0;
    }

    .stock-card-page .barang-suggestion-item:hover,
    .stock-card-page .barang-suggestion-item.active {
        background: #f0f5fb;
    }

    .stock-card-page .barang-suggestion-item strong {
        display: block;
        color: #315f91;
        font-size: 12px;
        margin-bottom: 3px;
    }

    .stock-card-page .barang-suggestion-item small {
        color: #8291a5;
        font-size: 11px;
    }

    .stock-card-page .barang-no-result {
        padding: 12px;
        color: #8795a7;
        font-size: 12px;
        text-align: center;
    }

    .stock-card-page .barang-hint {
        color: #8998aa;
        font-size: 10px;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .stock-card-page .panel {
            padding: 14px;
        }

        .stock-card-page .page-title {
            font-size: 20px;
        }
    }
</style>

<div class="stock-card-page">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4 class="page-title mb-1">Kartu Stok Barang</h4>
        <div class="page-subtitle">
            Riwayat pergerakan dan saldo stok barang berdasarkan transaksi.
        </div>
    </div>

    {{-- FILTER --}}
    <div class="panel">

        <form method="GET"
              action="{{ route('stock-card.index') }}"
              id="stockCardFilter">

            <div class="row g-3">

                {{-- FILTER CABANG --}}
                @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                    <div class="col-md-3">

                        <label class="form-label">Cabang</label>

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
                @endif

                {{-- FILTER BARANG AUTOCOMPLETE --}}
                <div class="col-md-4">

                    <label class="form-label">Barang</label>

                    <div class="barang-autocomplete">

                        <input type="text"
                               id="barangSearch"
                               class="form-control"
                               placeholder="Ketik kode atau nama barang..."
                               autocomplete="off"
                               value="{{ $barang ? $barang->kode_barang . ' - ' . $barang->nama_barang : '' }}">

                        <input type="hidden"
                               name="barang_id"
                               id="barangId"
                               value="{{ request('barang_id') }}">

                        <div id="barangSuggestions"
                             class="barang-suggestions"></div>

                    </div>

                </div>

                {{-- TANGGAL MULAI --}}
                <div class="col-md-2">

                    <label class="form-label">Dari Tanggal</label>

                    <input type="date"
                           name="start_date"
                           class="form-control"
                           value="{{ $startDate }}">

                </div>

                {{-- TANGGAL AKHIR --}}
                <div class="col-md-2">

                    <label class="form-label">Sampai Tanggal</label>

                    <input type="date"
                           name="end_date"
                           class="form-control"
                           value="{{ $endDate }}">

                </div>

                {{-- TOMBOL CARI --}}
                <div class="col-md-2 filter-action">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-filter">
                            <i class="bi bi-search"></i> Cari
                        </button>

                        <a href="{{ route('stock-card.index') }}" class="btn btn-outline-secondary btn-filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>

            </div>

        </form>

    </div>

    {{-- KONDISI CABANG --}}
    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']) && !$branchId)

        <div class="panel empty-state">

            <i class="bi bi-building fs-2 d-block mb-2"></i>

            Silakan pilih cabang terlebih dahulu untuk melihat Kartu Stok.

        </div>

    {{-- KONDISI BARANG --}}
    @elseif(!$barang)

        <div class="panel empty-state">

            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>

            Pilih barang untuk menampilkan riwayat pergerakan stok.

        </div>

    @else

        {{-- IDENTITAS BARANG --}}
        <div class="panel">

            <div class="row g-3">

                <div class="col-md-3">
                    <div class="summary-label">Kode Barang</div>
                    <div class="fw-bold">{{ $barang->kode_barang }}</div>
                </div>

                <div class="col-md-4">
                    <div class="summary-label">Nama Barang</div>
                    <div class="fw-bold">{{ $barang->nama_barang }}</div>
                </div>

                <div class="col-md-2">
                    <div class="summary-label">Kategori</div>
                    <div>{{ $barang->kategori }}</div>
                </div>

                <div class="col-md-3">
                    <div class="summary-label">Satuan / Lokasi</div>
                    <div>{{ $barang->satuan }} / {{ $barang->lokasi }}</div>
                </div>

            </div>

        </div>

        {{-- RINGKASAN SALDO --}}
        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <div class="summary-card">

                    <div class="summary-label">Saldo Awal</div>

                    <div class="summary-value">
                        {{ number_format($openingBalance, 0, ',', '.') }}
                    </div>

                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">

                    <div class="summary-label">Total Masuk</div>

                    <div class="summary-value text-in">
                        {{ number_format($totalMasuk, 0, ',', '.') }}
                    </div>

                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">

                    <div class="summary-label">Total Keluar</div>

                    <div class="summary-value text-out">
                        {{ number_format($totalKeluar, 0, ',', '.') }}
                    </div>

                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">

                    <div class="summary-label">Saldo Akhir</div>

                    <div class="summary-value">
                        {{ number_format($closingBalance, 0, ',', '.') }}
                    </div>

                </div>
            </div>

        </div>

        {{-- TABEL KARTU STOK --}}
        <div class="panel">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                <div>

                    <h6 class="fw-bold mb-1">
                        Riwayat Pergerakan Stok
                    </h6>

                    <div class="page-subtitle">

                        Periode
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}

                        sampai

                        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}

                    </div>

                </div>

                <div class="small text-muted">

                    Koreksi stok:

                    <strong>
                        {{ number_format($totalKoreksi, 0, ',', '.') }}
                    </strong>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>
                        <tr>
                            <th style="width:45px">No</th>
                            <th>Tanggal</th>
                            <th>Nomor Transaksi</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Koreksi</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- SALDO AWAL --}}
                        <tr class="table-light fw-bold">

                            <td colspan="5">
                                SALDO AWAL PERIODE
                            </td>

                            <td colspan="3"></td>

                            <td class="text-end">
                                {{ number_format($openingBalance, 0, ',', '.') }}
                            </td>

                        </tr>

                        {{-- RIWAYAT TRANSAKSI --}}
                        @forelse($movements as $index => $movement)

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td class="text-nowrap">
                                    {{ \Carbon\Carbon::parse($movement['tanggal'])->format('d/m/Y') }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $movement['nomor'] ?: '-' }}
                                </td>

                                <td>

                                    @if($movement['jenis'] === 'Penerimaan')

                                        <span class="badge-type badge-in">
                                            Penerimaan
                                        </span>

                                    @elseif($movement['jenis'] === 'Pengeluaran')

                                        <span class="badge-type badge-out">
                                            Pengeluaran
                                        </span>

                                    @else

                                        <span class="badge-type badge-adjust">
                                            Penyesuaian
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $movement['keterangan'] ?: '-' }}
                                </td>

                                <td class="text-end text-in">
                                    {{ $movement['masuk'] ? number_format($movement['masuk'], 0, ',', '.') : '-' }}
                                </td>

                                <td class="text-end text-out">
                                    {{ $movement['keluar'] ? number_format($movement['keluar'], 0, ',', '.') : '-' }}
                                </td>

                                <td class="text-end text-adjust">
                                    {{ $movement['koreksi'] ? number_format($movement['koreksi'], 0, ',', '.') : '-' }}
                                </td>

                                <td class="text-end fw-bold">
                                    {{ number_format($movement['saldo'], 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="empty-state">
                                    Tidak ada pergerakan stok pada periode ini.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                    {{-- TOTAL --}}
                    @if($movements->isNotEmpty())

                        <tfoot>

                            <tr class="table-light fw-bold">

                                <td colspan="5" class="text-end">
                                    TOTAL PERIODE
                                </td>

                                <td class="text-end text-in">
                                    {{ number_format($totalMasuk, 0, ',', '.') }}
                                </td>

                                <td class="text-end text-out">
                                    {{ number_format($totalKeluar, 0, ',', '.') }}
                                </td>

                                <td class="text-end text-adjust">
                                    {{ number_format($totalKoreksi, 0, ',', '.') }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($closingBalance, 0, ',', '.') }}
                                </td>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        </div>

    @endif

</div>

{{-- DATA BARANG AUTOCOMPLETE --}}
@php
    $barangAutocomplete = $barangs->map(function ($item) {
        return [
            'id'       => $item->id,
            'kode'     => $item->kode_barang,
            'nama'     => $item->nama_barang,
            'kategori' => $item->kategori,
            'satuan'   => $item->satuan,
            'lokasi'   => $item->lokasi,
        ];
    })->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {

    const barangInput = document.getElementById('barangSearch');
    const barangIdInput = document.getElementById('barangId');
    const suggestionsBox = document.getElementById('barangSuggestions');
    const filterForm = document.getElementById('stockCardFilter');

    if (!barangInput || !barangIdInput || !suggestionsBox || !filterForm) {
        return;
    }

    // DATA BARANG DARI CONTROLLER
    const daftarBarang = @json($barangAutocomplete);

    let activeIndex = -1;
    let hasilPencarian = [];

    // ESCAPE HTML UNTUK KEAMANAN TAMPILAN
    function escapeHtml(value) {

        return String(value ?? '').replace(/[&<>"']/g, function (char) {

            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];

        });

    }

    // TUTUP SARAN
    function tutupSuggestions() {

        suggestionsBox.style.display = 'none';
        suggestionsBox.innerHTML = '';

        activeIndex = -1;

    }

    // PILIH BARANG
    function pilihBarang(item) {

        barangInput.value = item.kode + ' - ' + item.nama;

        barangIdInput.value = item.id;

        tutupSuggestions();

    }

    // TAMPILKAN SARAN BARANG
    function tampilkanSuggestions(keyword) {

        const cari = keyword.trim().toLowerCase();

        if (!cari) {

            tutupSuggestions();

            return;

        }

        hasilPencarian = daftarBarang.filter(function (item) {

            const kode = String(item.kode ?? '').toLowerCase();

            const nama = String(item.nama ?? '').toLowerCase();

            const kategori = String(item.kategori ?? '').toLowerCase();

            return kode.includes(cari)
                || nama.includes(cari)
                || kategori.includes(cari);

        }).slice(0, 20);

        activeIndex = -1;

        // JIKA TIDAK ADA HASIL
        if (hasilPencarian.length === 0) {

            suggestionsBox.innerHTML = `
                <div class="barang-no-result">
                    Barang tidak ditemukan.
                </div>
            `;

            suggestionsBox.style.display = 'block';

            return;

        }

        // BUAT DAFTAR SARAN
        suggestionsBox.innerHTML = hasilPencarian.map(function (item, index) {

            return `
                <button type="button"
                        class="barang-suggestion-item"
                        data-index="${index}">

                    <strong>
                        ${escapeHtml(item.kode)} - ${escapeHtml(item.nama)}
                    </strong>

                    <small>
                        ${escapeHtml(item.kategori || '-')}
                        &nbsp; | &nbsp;
                        ${escapeHtml(item.satuan || '-')}
                        &nbsp; | &nbsp;
                        ${escapeHtml(item.lokasi || '-')}
                    </small>

                </button>
            `;

        }).join('');

        suggestionsBox.style.display = 'block';

    }

    // KETIKA INPUT BERUBAH
    barangInput.addEventListener('input', function () {

        // Batalkan ID barang sebelumnya jika teks berubah.
        barangIdInput.value = '';

        tampilkanSuggestions(this.value);

    });

    // KETIKA INPUT DIKLIK
    barangInput.addEventListener('focus', function () {

        if (this.value.trim() !== '' && !barangIdInput.value) {

            tampilkanSuggestions(this.value);

        }

    });

    // KLIK SALAH SATU SARAN
    suggestionsBox.addEventListener('click', function (event) {

        const tombol = event.target.closest('.barang-suggestion-item');

        if (!tombol) {
            return;
        }

        const index = Number(tombol.dataset.index);

        const item = hasilPencarian[index];

        if (item) {

            pilihBarang(item);

        }

    });

    // NAVIGASI KEYBOARD
    barangInput.addEventListener('keydown', function (event) {

        const items = suggestionsBox.querySelectorAll('.barang-suggestion-item');

        if (event.key === 'ArrowDown' && items.length) {

            event.preventDefault();

            activeIndex = Math.min(activeIndex + 1, items.length - 1);

        } else if (event.key === 'ArrowUp' && items.length) {

            event.preventDefault();

            activeIndex = Math.max(activeIndex - 1, 0);

        } else if (event.key === 'Enter' && suggestionsBox.style.display === 'block') {

            if (activeIndex >= 0 && hasilPencarian[activeIndex]) {

                event.preventDefault();

                pilihBarang(hasilPencarian[activeIndex]);

            }

        } else if (event.key === 'Escape') {

            tutupSuggestions();

        }

        items.forEach(function (item, index) {

            item.classList.toggle('active', index === activeIndex);

        });

        if (items[activeIndex]) {

            items[activeIndex].scrollIntoView({
                block: 'nearest'
            });

        }

    });

    // KLIK DI LUAR AUTOCOMPLETE
    document.addEventListener('click', function (event) {

        if (!event.target.closest('.barang-autocomplete')) {

            tutupSuggestions();

        }

    });

    // VALIDASI SEBELUM FORM DIKIRIM
    filterForm.addEventListener('submit', function (event) {

        if (barangInput.value.trim() !== '' && !barangIdInput.value) {

            event.preventDefault();

            barangInput.focus();

            tampilkanSuggestions(barangInput.value);

            alert('Silakan pilih barang dari daftar saran yang tersedia.');

        }

    });

});
</script>

@endsection
