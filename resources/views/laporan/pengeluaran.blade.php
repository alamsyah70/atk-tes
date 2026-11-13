@extends('layouts.main')

@section('container')
<style>
    /* ==============================
    HALAMAN LAPORAN
    ============================== */

    .laporan-page {
        padding: 24px;
        color: #243247;
    }

    .laporan-page .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }

    .laporan-page .page-title {
        margin: 0;
        color: #172b4d;
        font-size: 23px;
        font-weight: 700;
    }

    .laporan-page .page-subtitle {
        margin: 6px 0 0;
        color: #718096;
        font-size: 13px;
    }

    .laporan-page .filter-card,
    .laporan-page .report-card {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(31, 45, 61, .04);
        margin-bottom: 20px;
    }

    .laporan-page .filter-card {
        padding: 20px;
    }

    .laporan-page .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(150px, 1fr));
        gap: 16px;
    }

    .laporan-page .filter-field {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .laporan-page .filter-field label {
        color: #34445b;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .laporan-page .filter-field input,
    .laporan-page .filter-field select {
        width: 100%;
        height: 40px;
        border: 1px solid #dce4ee;
        border-radius: 7px;
        padding: 0 11px;
        color: #34445b;
        background: #fff;
        font-size: 13px;
        outline: none;
    }

    .laporan-page .filter-field input:focus,
    .laporan-page .filter-field select:focus {
        border-color: #3478d4;
        box-shadow: 0 0 0 3px rgba(52, 120, 212, .10);
    }

    .laporan-page .filter-search {
        grid-column: span 2;
    }

    .laporan-page .filter-actions {
        display: flex;
        gap: 9px;
        margin-top: 18px;
    }

    .laporan-page .btn-laporan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 8px 15px;
        border: 0;
        border-radius: 7px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
    }

    .laporan-page .btn-primary {
        background: #2463ad;
        color: #fff;
    }

    .laporan-page .btn-primary:hover {
        background: #194d8b;
        color: #fff;
    }

    .laporan-page .btn-reset {
        background: #edf1f6;
        color: #45566c;
    }

    .laporan-page .btn-reset:hover {
        background: #dfe6ef;
        color: #243247;
    }

    .laporan-page .btn-pdf {
        background: #d94343;
        color: #fff;
        white-space: nowrap;
    }

    .laporan-page .btn-pdf:hover {
        background: #bd3030;
        color: #fff;
    }

    .laporan-page .report-card {
        overflow: hidden;
    }

    .laporan-page .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 17px 20px;
        border-bottom: 1px solid #e9eef5;
    }

    .laporan-page .report-card-header h5 {
        margin: 0;
        color: #203653;
        font-size: 15px;
        font-weight: 700;
    }

    .laporan-page .report-card-header span {
        display: block;
        margin-top: 5px;
        color: #8492a6;
        font-size: 12px;
    }

    .laporan-page .report-card-body {
        padding: 18px 20px;
    }

    .laporan-page .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .laporan-page .laporan-table {
        width: 100%;
        min-width: 1050px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
    }

    .laporan-page .laporan-table thead th {
        padding: 12px 10px;
        background: #f3f6fa;
        color: #40536d;
        border-bottom: 1px solid #dfe6ef;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .laporan-page .laporan-table tbody td {
        padding: 11px 10px;
        color: #526176;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
        white-space: nowrap;
    }

    .laporan-page .laporan-table tbody tr:hover {
        background: #f8faff;
    }

    .laporan-page .laporan-table .nama-barang {
        min-width: 170px;
        white-space: normal;
        color: #263b57;
        font-weight: 600;
    }

    .laporan-page .laporan-table .text-end {
        text-align: right;
    }

    .laporan-page .empty-data {
        padding: 30px !important;
        text-align: center;
        color: #8996a8 !important;
    }

    .laporan-page .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 18px;
    }

    .laporan-page .pagination {
        margin-bottom: 0;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        .laporan-page .filter-grid {
            grid-template-columns: repeat(2, minmax(140px, 1fr));
        }

        .laporan-page .filter-search {
            grid-column: span 2;
        }
    }

    @media (max-width: 576px) {
        .laporan-page {
            padding: 14px;
        }

        .laporan-page .page-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .laporan-page .page-title {
            font-size: 19px;
        }

        .laporan-page .filter-grid {
            grid-template-columns: 1fr;
        }

        .laporan-page .filter-search {
            grid-column: span 1;
        }

        .laporan-page .filter-actions {
            flex-wrap: wrap;
        }

        .laporan-page .btn-laporan {
            flex: 1;
        }

        .laporan-page .report-card-body {
            padding: 12px;
        }
    }
</style>
<div class="container-fluid laporan-page">

    <div class="page-heading">
        <div>
            <h3 class="page-title">Laporan Pengeluaran Barang</h3>
            <p class="page-subtitle">Riwayat barang keluar berdasarkan pengambil, divisi, dan tanggal.</p>
        </div>
        <a href="{{ route('laporan.pengeluaran.pdf', request()->query()) }}"
           target="_blank" class="btn-laporan btn-pdf">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    <div class="filter-card">
        <form action="{{ route('laporan.pengeluaran') }}" method="GET">
            <div class="filter-grid">

                <div class="filter-field">
                    <label>Tanggal Dari</label>
                    <input type="date" name="tanggal_dari"
                           value="{{ request('tanggal_dari') }}">
                </div>

                <div class="filter-field">
                    <label>Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai"
                           value="{{ request('tanggal_sampai') }}">
                </div>

                <div class="filter-field">
                    <label>Divisi</label>
                    <input type="text" name="divisi"
                           value="{{ request('divisi') }}"
                           placeholder="Masukkan divisi">
                </div>

                <div class="filter-field">
                    <label>Nama Pengambil</label>
                    <input type="text" name="nama_pengambil"
                           value="{{ request('nama_pengambil') }}"
                           placeholder="Masukkan nama pengambil">
                </div>

                <div class="filter-field filter-search">
                    <label>Pencarian</label>
                    <input type="text" name="q"
                           value="{{ request('q') }}"
                           placeholder="Kode barang, nama barang, nomor transaksi...">
                </div>

            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-laporan btn-primary">
                    <i class="fas fa-search"></i> Cari Data
                </button>

                <a href="{{ route('laporan.pengeluaran') }}"
                   class="btn-laporan btn-reset">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="report-card">
        <div class="report-card-header">
            <div>
                <h5>Data Pengeluaran Barang</h5>
                <span>{{ $data->total() }} detail barang ditemukan</span>
            </div>
        </div>

        <div class="report-card-body">
            <div class="table-responsive">
                <table class="table laporan-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No. Pengeluaran</th>
                            <th>Cabang</th>
                            <th>Nama Pengambil</th>
                            <th>Divisi</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td>{{ $data->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                                <td>{{ $item->nomor_pengeluaran ?? '-' }}</td>
                                <td>{{ $item->nama_cabang ?? '-' }}</td>
                                <td>{{ $item->nama_pengambil ?? '-' }}</td>
                                <td>{{ $item->divisi ?? '-' }}</td>
                                <td>{{ $item->kode_barang }}</td>
                                <td class="nama-barang">{{ $item->nama_barang }}</td>
                                <td>{{ $item->kategori ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->qty, 0, ',', '.') }}</td>
                                <td>{{ $item->satuan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="empty-data">
                                    Tidak ada data pengeluaran barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>
@endsection