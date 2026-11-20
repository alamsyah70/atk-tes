@extends('layouts.main')

@section('container')
<style>
    /* PAGINATION SERAGAM */
    .laporan-page .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .laporan-page .pagination-info {
        font-size: 13px;
        color: #6c757d;
    }

    .laporan-page .pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .laporan-page .page-btn {
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

    .laporan-page .page-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .laporan-page .page-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .laporan-page .page-btn.disabled {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* HALAMAN LAPORAN */
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
        min-width: 950px;
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

    .laporan-page .qty-tambah {
        color: #198754 !important;
        font-weight: 700;
    }

    .laporan-page .qty-kurang {
        color: #dc3545 !important;
        font-weight: 700;
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
            <h3 class="page-title">Laporan Penyesuaian Stok</h3>
            <p class="page-subtitle">
                Riwayat penambahan dan pengurangan stok barang berdasarkan tanggal.
            </p>
        </div>

        <a href="{{ route('laporan.penyesuaian.pdf', request()->query()) }}"
           target="_blank" class="btn-laporan btn-pdf">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

    {{-- FILTER --}}
    <div class="filter-card">
        <form action="{{ route('laporan.penyesuaian') }}" method="GET">
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

                @if($isGlobalUser)
                    <div class="filter-field">
                        <label>Cabang</label>
                        <select name="branch_id">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="filter-field filter-search">
                    <label>Pencarian</label>
                    <input type="text" name="q"
                           value="{{ request('q') }}"
                           placeholder="Nomor penyesuaian, kode barang, nama barang...">
                </div>

            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-laporan btn-primary">
                    <i class="fas fa-search"></i> Cari Data
                </button>

                <a href="{{ route('laporan.penyesuaian') }}"
                   class="btn-laporan btn-reset">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- DATA LAPORAN --}}
    <div class="report-card">

        <div class="report-card-header">
            <div>
                <h5>Data Penyesuaian Stok</h5>
                <span>{{ $data->total() }} transaksi ditemukan</span>
            </div>
        </div>

        <div class="report-card-body">

            <div class="table-responsive">
                <table class="table laporan-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No. Penyesuaian</th>
                            @if($isGlobalUser)
                                <th>Cabang</th>
                            @endif
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Qty Koreksi</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $index => $item)
                            <tr>
                                <td>{{ $data->firstItem() + $index }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>

                                <td>{{ $item->nomor_penyesuaian ?? '-' }}</td>

                                @if($isGlobalUser)
                                    <td>{{ $item->nama_cabang ?? '-' }}</td>
                                @endif

                                <td>{{ $item->kode_barang ?? '-' }}</td>

                                <td class="nama-barang">
                                    {{ $item->nama_barang ?? '-' }}
                                </td>

                                <td class="text-end">
                                    @if($item->qty_koreksi > 0)
                                        <span class="qty-tambah">
                                            +{{ number_format($item->qty_koreksi, 0, ',', '.') }}
                                        </span>
                                    @elseif($item->qty_koreksi < 0)
                                        <span class="qty-kurang">
                                            {{ number_format($item->qty_koreksi, 0, ',', '.') }}
                                        </span>
                                    @else
                                        0
                                    @endif
                                </td>

                                <td style="white-space: normal; min-width: 180px;">
                                    {{ $item->alasan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGlobalUser ? 8 : 7 }}"
                                    class="empty-data">
                                    Tidak ada data penyesuaian stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- PAGINATION --}}
            @if ($data->hasPages())
                @php
                    $current = $data->currentPage();
                    $last = $data->lastPage();
                    $start = max(1, $current - 1);
                    $end = min($last, $current + 1);
                @endphp

                <div class="pagination-container">

                    <div class="pagination-info">
                        Menampilkan {{ $data->firstItem() ?? 0 }}
                        sampai {{ $data->lastItem() ?? 0 }}
                        dari {{ $data->total() }} transaksi
                    </div>

                    <div class="pagination-links">

                        @if ($data->onFirstPage())
                            <span class="page-btn disabled">‹ Previous</span>
                        @else
                            <a href="{{ $data->previousPageUrl() }}" class="page-btn">
                                ‹ Previous
                            </a>
                        @endif

                        @if ($start > 1)
                            <a href="{{ $data->url(1) }}" class="page-btn">1</a>

                            @if ($start > 2)
                                <span class="page-btn disabled">...</span>
                            @endif
                        @endif

                        @foreach (range($start, $end) as $page)
                            @if ($page == $current)
                                <span class="page-btn active">{{ $page }}</span>
                            @else
                                <a href="{{ $data->url($page) }}" class="page-btn">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="page-btn disabled">...</span>
                            @endif

                            <a href="{{ $data->url($last) }}" class="page-btn">
                                {{ $last }}
                            </a>
                        @endif

                        @if ($data->hasMorePages())
                            <a href="{{ $data->nextPageUrl() }}" class="page-btn">
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
@endsection
