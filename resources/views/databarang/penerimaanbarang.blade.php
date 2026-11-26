@extends('layouts.main')

@section('container')
<!-- <style>
    
    /* HALAMAN PENERIMAAN BARANG */

    .receipt-page {
        width: 100%;
    }

    .receipt-badge {
        background: #eaf3ff;
        color: #1769d2;
        padding: 10px 15px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .receipt-card {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(23,62,116,.05);
    }

    .receipt-title {
        font-size: 15px;
        font-weight: 700;
        color: #102a56;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .receipt-title span {
        color: #1769d2;
        font-size: 19px;
    }

    .receipt-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .receipt-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .receipt-field label {
        font-size: 12px;
        font-weight: 700;
        color: #33445e;
    }

    .receipt-field label span {
        color: #e05252;
    }

    .receipt-field input,
    .receipt-field select,
    .receipt-field textarea {
        width: 100%;
        padding: 11px 13px;
        border: 1px solid #dce5f0;
        border-radius: 8px;
        outline: none;
        font-size: 12px;
        color: #24344d;
        background: #fff;
    }

    .receipt-field input:focus,
    .receipt-field select:focus,
    .receipt-field textarea:focus {
        border-color: #1769d2;
        box-shadow: 0 0 0 3px rgba(23,105,210,.08);
    }

    .receipt-field textarea {
        resize: vertical;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .receipt-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .receipt-table-header .receipt-title {
        margin-bottom: 5px;
    }

    .receipt-table-header p {
        color: #7c8ba3;
        font-size: 11px;
        margin: 0;
    }

    .receipt-add-btn {
        background: #1769d2;
        color: white;
        border: none;
        padding: 11px 15px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .receipt-add-btn:hover,
    .receipt-save:hover {
        background: #1059b7;
    }

    .receipt-table-scroll {
        overflow-x: auto;
    }

    .receipt-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .receipt-table th {
        background: #f2f7ff;
        color: #60728c;
        padding: 13px 10px;
        text-align: left;
        font-size: 10px;
    }

    .receipt-table td {
        padding: 9px 7px;
        border-bottom: 1px solid #edf1f6;
    }

    .receipt-table td input {
        width: 100%;
        min-width: 65px;
        padding: 9px 8px;
        border: 1px solid #dce5f0;
        border-radius: 6px;
        font-size: 11px;
        outline: none;
    }

    .receipt-table td input:focus {
        border-color: #1769d2;
    }

    .receipt-table td input[readonly] {
        background: #f3f7fc;
        color: #1769d2;
        font-weight: 700;
    }

    .row-delete {
        border: none;
        background: #ffeded;
        color: #dc2626;
        padding: 8px 10px;
        border-radius: 6px;
        cursor: pointer;
    }

    .row-delete:hover {
        background: #dc2626;
        color: white;
    }


    .receipt-total {
    margin-top: 15px;
    padding: 13px 18px;
    background: #f5f9ff;
    border-radius: 9px;

    display: grid;
    grid-template-columns: 1fr 1fr 1.2fr;
    align-items: center;
    gap: 15px;
    width: 100%;
    box-sizing: border-box;
    }

    .receipt-total > div {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
    }

    .receipt-total span {
        color: #7c8ba3;
        font-size: 10px;
        white-space: nowrap;
    }

    .receipt-total strong {
        color: #102a56;
        font-size: 13px;
        font-weight: 600;
    }

    .receipt-total .grand-total {
        border-left: 1px solid #1769d2;
        padding-left: 18px;
    }

    .receipt-total .grand-total h2 {
        margin: 0;
        color: #1769d2;
        font-size: 19px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Tampilan HP */
    @media (max-width: 600px) {
        .receipt-total {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 12px;
        }

        .receipt-total .grand-total {
            grid-column: 1 / -1;
            border-left: none;
            border-top: 1px solid #1769d2;
            padding: 10px 0 0;
        }
    }

    .grand-total h2 {
        color: #1769d2;
        font-size: 22px;
        margin: 0;
    }

    .receipt-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 25px;
    }

    .receipt-actions a,
    .receipt-actions button {
        padding: 11px 17px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .receipt-cancel,
    .receipt-reset {
        border: 1px solid #dce5f0;
        color: #52647c;
        background: white;
    }

    .receipt-save {
        border: none;
        background: #1769d2;
        color: white;
    }

    @media(max-width: 700px) {
        .receipt-info-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: auto;
        }

        .receipt-total {
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .receipt-total .grand-total {
            width: 100%;
            border-left: none;
            border-top: 2px solid #1769d2;
            padding: 15px 0 0;
        }

        .receipt-table-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .receipt-actions {
            flex-wrap: wrap;
        }
    }


        /* =====================================
        SEARCH HISTORI PENERIMAAN
        ===================================== */

        .receipt-page .search-container {
            width: 50%;
            max-width: 600px;
            margin: 10 0 20px auto;
        }

        .receipt-page .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .receipt-page .search-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
        }

        .receipt-page .search-icon {
            position: absolute;
            left: 15px;
            font-size: 23px;
            color: #94a3b8;
            pointer-events: none;
        }

        .receipt-page .search-input {
            box-sizing: border-box;
            width: 100%;
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

        .receipt-page .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        }

        .receipt-page .search-input::placeholder {
            color: #94a3b8;
        }

        .receipt-page .search-clear {
            position: absolute;
            right: 13px;
            color: #94a3b8;
            font-size: 22px;
            text-decoration: none;
        }

        .receipt-page .search-clear:hover {
            color: #ef4444;
        }

        .receipt-page .search-button {
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
            justify-content: center;
            gap: 7px;
            transition: .2s;
            margin-left: 4px;
        }

        .receipt-page .search-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* =====================================
        TABEL HISTORI PENERIMAAN
        ===================================== */

        .receipt-page .table-responsive {
            border-radius: 8px;
        }

        .receipt-page .table {
            margin-bottom: 0;
            font-size: 12px;
            color: #34445d;
            vertical-align: middle;
        }

        .receipt-page .table thead th {
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

        .receipt-page .table tbody td {
            padding: 11px 13px;
            border-color: #edf1f6;
        }

        .receipt-page .table tbody tr:hover {
            background: #f8fbff;
        }

        .receipt-page .table .btn {
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        /* =====================================
        PAGINATION
        ===================================== */

        .receipt-page .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
            padding: 12px 0;
        }

        .receipt-page .pagination-info {
            font-size: 13px;
            color: #64748b;
        }

        .receipt-page .pagination-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
        }

        .receipt-page .page-btn {
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

        .receipt-page .page-btn:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .receipt-page .page-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .receipt-page .page-btn.disabled {
            color: #94a3b8;
            background: #f1f5f9;
            cursor: not-allowed;
        }

        /* =====================================
        RESPONSIVE
        ===================================== */

        @media (max-width: 768px) {
            .receipt-page .receipt-card {
                padding: 13px;
            }

            .receipt-page .receipt-table {
                min-width: 850px;
            }
        }

        @media (max-width: 600px) {
            .receipt-page .search-container {
                width: 100%;
                max-width: none;
            }

            .receipt-page .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .receipt-page .search-button {
                width: 100%;
            }

            .receipt-page .pagination-container {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* AUTOCOMPLETE MASTER BARANG */

    .barang-autocomplete {
        position: relative;
        width: 100%;
        min-width: 190px;
    }

    .barang-autocomplete input {
        width: 100%;
        min-width: 190px;
        padding: 9px 8px;
        border: 1px solid #dce5f0;
        border-radius: 6px;
        font-size: 11px;
        outline: none;
    }

    .barang-autocomplete input:focus {
        border-color: #1769d2;
    }

    .barang-suggestions {
        display: none;
        position: fixed;
        width: 320px;
        max-width: calc(100vw - 20px);
        max-height: 250px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dce5f0;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0,0,0,.18);
        z-index: 999999;
    }

    .barang-suggestion-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #edf1f6;
        background: #fff;
    }

    .barang-suggestion-item:hover {
        background: #f0f6ff;
    }

    .barang-suggestion-item strong {
        display: block;
        color: #1769d2;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .barang-suggestion-item small {
        display: block;
        color: #64748b;
        font-size: 10px;
    }

    .barang-suggestion-empty {
        padding: 12px;
        color: #94a3b8;
        font-size: 11px;
    }

</style> -->
<div class="receipt-page">
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Data belum berhasil disimpan:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@can('pengeluaran.create')
<form action="{{ route('databarang.penerimaan.store') }}"
      method="POST"
      enctype="multipart/form-data"
      id="receiptForm">
    @csrf
        <!-- INFORMASI PENERIMAAN -->
        <div class="receipt-card">
            <div class="receipt-title">
                Informasi Penerimaan
            </div>

            <div class="receipt-info-grid">

                <div class="receipt-field">
                    <label>Supplier <span>*</span></label>
                    <select name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="receipt-field">
                    <label>Tanggal Penerimaan <span>*</span></label>
                    <input type="date"
                           name="tanggal"
                           value="{{ date('Y-m-d') }}"
                           required>
                </div>

                <div class="receipt-field full-width">
                    <label>Remark / Keterangan</label>
                    <textarea name="keterangan"
                    rows="3"
                    placeholder="Keterangan penerimaan barang...">{{ old('keterangan') }}</textarea>
                </div>

                <div class="receipt-field full-width">
                    <label>Upload Bukti PO</label>

                    <input type="file"
                        name="bukti_po"
                        accept="image/jpeg,image/png,image/webp">

                    <small style="color: #7c8ba3;">
                        Format JPG, JPEG, PNG, WEBP. Maksimal 5 MB.
                    </small>

                    @error('bukti_po')
                        <small style="color: #dc2626;">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- DETAIL BARANG -->
        <div class="receipt-card">

            <div class="receipt-table-header">
                <div>
                    <div class="receipt-title">
                        Detail Barang
                    </div>
                    <p>Masukkan barang yang diterima dari supplier.</p>
                </div>

                <button type="button" class="receipt-add-btn" id="addRow">
                    ＋ Tambah Barang
                </button>
            </div>

            <div class="receipt-table-scroll">
                <table class="receipt-table">
                <thead>
                    <tr>
                        <th width="45">NO</th>
                        <th>PILIH BARANG</th>
                        <th>QTY</th>
                        <th>HARGA</th>
                        <th>JUMLAH</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                    <tbody id="receiptRows">
                        <!-- Baris pertama dibuat otomatis oleh JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- TOTAL -->
            <div class="receipt-total">
                <div>
                    <span>Jenis Barang</span>
                    <strong id="totalJenis">0 Barang</strong>
                </div>
                
                <div>
                    <span>Quantity</span>
                    <strong id="totalQty">0</strong>
                </div>

                <div class="grand-total">
                    <span>Total Penerimaan</span>
                    <h2 id="grandTotal">Rp 0</h2>
                    <input type="hidden" name="total" id="totalInput" value="0">
                </div>
            </div>

        </div>

        <!-- TOMBOL AKSI -->
        <div class="receipt-actions">
            <button type="submit" class="receipt-save">
                ✓ Simpan Penerimaan
            </button>
        </div>

        </form>
@endcan

        {{-- RIWAYAT PENERIMAAN BARANG --}}
        <div class="receipt-card">
            <div class="receipt-table-header">
                <div>
                    <div class="receipt-title">
                        Riwayat Penerimaan Barang
                    </div>
                    <p>Daftar transaksi barang masuk yang telah disimpan.</p>
                </div>
            {{-- SEARCH --}}
            <div class="search-container">
                <form action="{{ route('databarang.penerimaan') }}"
                    method="GET"
                    class="search-form">

                    <div class="search-input-wrapper">
                        <span class="search-icon">⌕</span>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nomor, supplier, kode atau nama barang..."
                            class="search-input">

                        @if(request('search'))
                            <a href="{{ route('databarang.penerimaan') }}"
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

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor Penerimaan</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Cabang</th>
                            <th>Total Jenis Barang</th>
                            <th>Bukti PO</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($riwayat as $index => $penerimaan)

                            <tr>
                                <td>
                                    {{ $riwayat->firstItem() + $index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $penerimaan->nomor_penerimaan ?? '-' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $penerimaan->supplier->nama_supplier ?? '-' }}
                                </td>

                                <td>
                                    {{ $penerimaan->branch->nama_cabang ?? '-' }}
                                </td>

                                <td>
                                    {{ $penerimaan->details_count }} jenis
                                </td>

                                <td class="text-center">
                                    @if($penerimaan->bukti_po)
                                        <a href="{{ asset('storage/'.$penerimaan->bukti_po) }}"
                                        target="_blank"
                                        class="btn btn-info btn-sm">
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>

                                <td>
                                    @can('penerimaan.view')
                                        <a href="{{ route('databarang.penerimaan.pdf', $penerimaan->id) }}"
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
                                    Belum ada histori penerimaan barang.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                
{{-- PAGINATION --}}
@if($riwayat->hasPages())

    @php
        $current = $riwayat->currentPage();
        $last = $riwayat->lastPage();

        $start = max(1, $current - 1);
        $end = min($last, $current + 1);
    @endphp

    <div class="pagination-container">

        <div class="pagination-info">
            Menampilkan {{ $riwayat->firstItem() ?? 0 }}
            sampai {{ $riwayat->lastItem() ?? 0 }}
            dari {{ $riwayat->total() }} penerimaan
        </div>

        <div class="pagination-links">

            {{-- Previous --}}
            @if($riwayat->onFirstPage())
                <span class="page-btn disabled">‹</span>
            @else
                <a href="{{ $riwayat->previousPageUrl() }}" class="page-btn">
                    ‹
                </a>
            @endif

            {{-- Halaman pertama --}}
            @if($start > 1)
                <a href="{{ $riwayat->url(1) }}" class="page-btn">1</a>

                @if($start > 2)
                    <span class="page-btn disabled">...</span>
                @endif
            @endif

            {{-- Halaman sekitar halaman aktif --}}
            @foreach(range($start, $end) as $page)
                @if($page == $current)
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $riwayat->url($page) }}" class="page-btn">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Halaman terakhir --}}
            @if($end < $last)
                @if($end < $last - 1)
                    <span class="page-btn disabled">...</span>
                @endif

                <a href="{{ $riwayat->url($last) }}" class="page-btn">
                    {{ $last }}
                </a>
            @endif

            {{-- Next --}}
            @if($riwayat->hasMorePages())
                <a href="{{ $riwayat->nextPageUrl() }}" class="page-btn">
                    ›
                </a>
            @else
                <span class="page-btn disabled">›</span>
            @endif

        </div>

    </div>

@endif


            </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const receiptRows = document.getElementById('receiptRows');
    const addRowBtn = document.getElementById('addRow');

    let rowNumber = 0;

    // Format Rupiah
    function rupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    // URL pencarian Master Barang
    const barangSearchUrl = @json(route('barangs.search'));

    // Mengatur tombol hapus: minimal harus ada satu baris
    function updateTombolHapus() {
        const rows = receiptRows.querySelectorAll('tr');
        const tombolHapus = receiptRows.querySelectorAll('.row-delete');

        tombolHapus.forEach(function(button) {
            const hanyaSatuBaris = rows.length <= 1;

            button.disabled = hanyaSatuBaris;
            button.style.opacity = hanyaSatuBaris ? '0.35' : '1';
            button.style.cursor = hanyaSatuBaris ? 'not-allowed' : 'pointer';
            button.title = hanyaSatuBaris
                ? 'Minimal harus ada satu baris barang'
                : 'Hapus baris';
        });
    }

    // Tambah baris barang
    function addReceiptRow() {
        rowNumber++;

        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td class="row-number">
                ${receiptRows.children.length + 1}
            </td>

            <td>
                <div class="barang-autocomplete">
                    <input type="text"
                        class="barang-search"
                        placeholder="Ketik kode atau nama barang..."
                        autocomplete="off"
                        required>

                    <div class="barang-suggestions"></div>
                </div>

                <input type="hidden" name="barang_id[]" class="barang-id">
                <input type="hidden" name="kode_barang[]" class="kode-barang">
                <input type="hidden" name="nama_barang[]" class="nama-barang">
                <input type="hidden" name="kategori[]" class="kategori-barang">
                <input type="hidden" name="satuan[]" class="satuan-barang">
                <input type="hidden" name="lokasi[]" class="lokasi-barang">
            </td>

            <td>
                <input type="number"
                    name="qty[]"
                    class="qty"
                    min="1"
                    value="1"
                    required>
            </td>

            <td>
                <input type="number"
                    name="harga[]"
                    class="harga"
                    min="0"
                    value="0"
                    required>
            </td>

            <td>
                <input type="number"
                    name="jumlah[]"
                    class="jumlah"
                    value="0"
                    readonly>
            </td>

            <td>
                <button type="button" class="row-delete">✕</button>
            </td>
        `;

        receiptRows.appendChild(tr);

        aktifkanAutocomplete(tr);

        updateReceipt();
        updateTombolHapus();
    }

    // Hitung jumlah per baris dan total
    function updateReceipt() {
        let total = 0;
        let totalQty = 0;

        const rows = receiptRows.querySelectorAll('tr');

        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;

            const qty = Number(row.querySelector('.qty').value) || 0;
            const harga = Number(row.querySelector('.harga').value) || 0;

            const jumlah = qty * harga;

            row.querySelector('.jumlah').value = jumlah;

            total += jumlah;
            totalQty += qty;
        });

        document.getElementById('totalJenis').textContent =
            rows.length + ' Barang';

        document.getElementById('totalQty').textContent =
            totalQty.toLocaleString('id-ID');

        document.getElementById('grandTotal').textContent =
            rupiah(total);

        document.getElementById('totalInput').value = total;

        updateTombolHapus();
    }

    // Event tambah barang
    addRowBtn.addEventListener('click', addReceiptRow);

    // Event hitung otomatis
    receiptRows.addEventListener('input', function(e) {
        if (
            e.target.classList.contains('qty') ||
            e.target.classList.contains('harga')
        ) {
            updateReceipt();
        }
    });

    // Event hapus baris
    receiptRows.addEventListener('click', function(e) {
        if (e.target.classList.contains('row-delete')) {

            const rows = receiptRows.querySelectorAll('tr');

            // Jangan izinkan menghapus jika hanya tersisa satu baris
            if (rows.length <= 1) {
                updateTombolHapus();
                return;
            }

            e.target.closest('tr').remove();

            updateReceipt();
            updateTombolHapus();
        }
    });

    // Validasi sebelum disimpan
    document.getElementById('receiptForm').addEventListener('submit', function(e) {

        const rows = receiptRows.querySelectorAll('tr');

        if (rows.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal satu barang penerimaan!');
            addReceiptRow();
            return;
        }

        for (const row of rows) {
            const barangId = row.querySelector('.barang-id').value;

            if (!barangId) {
                e.preventDefault();

                alert('Pilih barang dari Master Barang terlebih dahulu!');

                row.querySelector('.barang-search').focus();

                return;
            }
        }
    });

    // Baris pertama otomatis
    addReceiptRow();

    // Aktifkan autocomplete Master Barang
    function aktifkanAutocomplete(row) {

        const input = row.querySelector('.barang-search');
        const suggestionBox = row.querySelector('.barang-suggestions');

        let timer = null;

        // Pindahkan daftar saran ke body agar tidak terpotong tabel
        document.body.appendChild(suggestionBox);

        // Atur posisi daftar saran tepat di bawah input
        function posisiSuggestion() {

            if (suggestionBox.style.display === 'none') return;

            const rect = input.getBoundingClientRect();

            suggestionBox.style.left = Math.max(
                10,
                Math.min(rect.left, window.innerWidth - suggestionBox.offsetWidth - 10)
            ) + 'px';

            suggestionBox.style.top = (rect.bottom + 4) + 'px';
            suggestionBox.style.width = Math.max(rect.width, 280) + 'px';
        }

        input.addEventListener('input', function() {

            const keyword = this.value.trim();

            // Hapus identitas barang sebelumnya
            row.querySelector('.barang-id').value = '';
            row.querySelector('.kode-barang').value = '';
            row.querySelector('.nama-barang').value = '';
            row.querySelector('.kategori-barang').value = '';
            row.querySelector('.satuan-barang').value = '';
            row.querySelector('.lokasi-barang').value = '';

            clearTimeout(timer);

            if (keyword.length < 1) {
                suggestionBox.style.display = 'none';
                return;
            }

            timer = setTimeout(async function() {

                try {
                    const response = await fetch(
                        barangSearchUrl + '?q=' + encodeURIComponent(keyword),
                        {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data barang.');
                    }

                    const barangs = await response.json();

                    // Pastikan hasil masih sesuai dengan teks terakhir
                    if (input.value.trim() !== keyword) return;

                    suggestionBox.innerHTML = '';

                    if (barangs.length === 0) {

                        const empty = document.createElement('div');
                        empty.className = 'barang-suggestion-empty';
                        empty.textContent = 'Barang tidak ditemukan di Master Barang';

                        suggestionBox.appendChild(empty);

                    } else {

                        barangs.forEach(function(barang) {

                            const item = document.createElement('div');
                            item.className = 'barang-suggestion-item';

                            const title = document.createElement('strong');
                            title.textContent =
                                barang.kode_barang + ' - ' + barang.nama_barang;

                            const detail = document.createElement('small');
                            detail.textContent =
                                'Kategori: ' + (barang.kategori || '-') +
                                ' | Satuan: ' + (barang.satuan || '-') +
                                ' | Lokasi: ' + (barang.lokasi || '-');

                            item.appendChild(title);
                            item.appendChild(detail);

                            item.addEventListener('click', function() {
                                pilihBarang(row, barang);
                                suggestionBox.style.display = 'none';
                            });

                            suggestionBox.appendChild(item);
                        });
                    }

                    suggestionBox.style.display = 'block';
                    posisiSuggestion();

                } catch (error) {
                    console.error(error);
                    suggestionBox.style.display = 'none';
                }

            }, 250);
        });

        // Posisi ulang saat halaman atau tabel digulir
        window.addEventListener('scroll', posisiSuggestion, true);
        window.addEventListener('resize', posisiSuggestion);
    }

    // Mengisi data setelah barang dipilih
    function pilihBarang(row, barang) {

        row.querySelector('.barang-search').value =
            barang.kode_barang + ' - ' + barang.nama_barang;

        row.querySelector('.barang-id').value = barang.id;

        row.querySelector('.kode-barang').value = barang.kode_barang;
        row.querySelector('.nama-barang').value = barang.nama_barang;
        row.querySelector('.kategori-barang').value = barang.kategori || '';
        row.querySelector('.satuan-barang').value = barang.satuan || '';
        row.querySelector('.lokasi-barang').value = barang.lokasi || '';
    }

    // Tutup saran jika pengguna mengklik di luar kolom
    document.addEventListener('click', function(event) {

        document.querySelectorAll('.barang-suggestions').forEach(function(box) {

            if (!box.contains(event.target)) {
                box.style.display = 'none';
            }
        });
    });
</script>

@push('scripts')
<script src="{{ asset('js/penerimaan.js') }}"></script>
@endpush
@endsection
