@extends('layouts.main')

@section('container')
<style>
    
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

</style>
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
<form action="{{ route('databarang.penerimaan.store') }}"
      method="POST"
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
                      keterangan penerimaan barang..."></textarea>
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
                            <th>KODE BARANG</th>
                            <th>NAMA BARANG</th>
                            <th>KATEGORI</th>
                            <th>SATUAN</th>
                            <th>LOKASI</th>
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
</div>
<!-- @if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: 'Berhasil Disimpan!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#1769d2',
            allowOutsideClick: false
        });
    });
</script>
@endif -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    const receiptRows = document.getElementById('receiptRows');
    const addRowBtn = document.getElementById('addRow');

    let rowNumber = 0;

    // Format Rupiah
    function rupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    // Tambah baris barang
    function addReceiptRow() {
        rowNumber++;

        const tr = document.createElement('tr');

        tr.innerHTML = `
        <td class="row-number">${receiptRows.children.length + 1}</td>

        <td>
            <input type="text" name="kode_barang[]" required>
        </td>

        <td>
            <input type="text" name="nama_barang[]" required>
        </td>

        <td>
            <input type="text" name="kategori[]" required>
        </td>

        <td>
            <input type="text" name="satuan[]" required>
        </td>

        <td>
            <input type="text" name="lokasi[]" required>
        </td>

        <td>
            <input type="number" name="qty[]" class="qty" min="1" value="1" required>
        </td>

        <td>
            <input type="number" name="harga[]" class="harga" min="0" value="0" required>
        </td>

        <td>
            <input type="number" name="jumlah[]" class="jumlah" value="0" readonly>
        </td>

        <td>
            <button type="button" class="row-delete">✕</button>
        </td>
    `;

        receiptRows.appendChild(tr);

        updateReceipt();
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
    }

    // Event tambah barang
    addRowBtn.addEventListener('click', addReceiptRow);

    // Event hitung otomatis dan hapus baris
    receiptRows.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty') ||
            e.target.classList.contains('harga')) {
            updateReceipt();
        }
    });

    receiptRows.addEventListener('click', function(e) {
        if (e.target.classList.contains('row-delete')) {
            e.target.closest('tr').remove();
            updateReceipt();
        }
    });

    // Validasi sebelum disimpan
    document.getElementById('receiptForm').addEventListener('submit', function(e) {
        if (receiptRows.children.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal satu barang penerimaan!');
        }
    });

    // Baris pertama otomatis
    addReceiptRow();

</script>
@push('scripts')
<script src="{{ asset('js/penerimaan.js') }}"></script>
@endpush
@endsection
