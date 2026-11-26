@extends('Layouts.main')

@section('container')

@php
    $maksGrafik = max(
        1,
        $grafikBulanan->max('masuk'),
        $grafikBulanan->max('keluar')
    );

    $formatAngka = function ($angka) {
        return number_format((float) $angka, 0, ',', '.');
    };
@endphp

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h4>Dashboard Inventory</h4>
            <p>Ringkasan dan aktivitas persediaan ATK MDI</p>
        </div>

        <div class="dashboard-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="dashboard-stats">

        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <span>Jenis Barang Tersedia</span>
                <h3>{{ $formatAngka($totalItems) }}</h3>
                <small>{{ $formatAngka($totalCategories) }} kategori barang</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon cyan">
                <i class="bi bi-boxes"></i>
            </div>
            <div>
                <span>Total Stok</span>
                <h3>{{ $formatAngka($totalStock) }}</h3>
                <small>Akumulasi seluruh persediaan</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <span>Nilai Persediaan</span>
                <h3 class="stat-currency">
                    Rp {{ $formatAngka($totalInventoryValue) }}
                </h3>
                <small>Estimasi nilai stok saat ini</small>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <span>Stok Menipis</span>
                <h3>{{ $formatAngka($lowStock) }}</h3>
                <small>Barang dengan stok 1–10</small>
            </div>
        </div>

    </div>

    {{-- AKTIVITAS BULAN INI --}}
    <div class="activity-summary">

        <div class="activity-card">
            <div class="activity-icon masuk">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <div>
                <span>Barang Masuk Bulan Ini</span>
                <h3>{{ $formatAngka($totalMasukBulanIni) }}</h3>
                <small>Total kuantitas penerimaan</small>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-icon keluar">
                <i class="bi bi-box-arrow-up"></i>
            </div>
            <div>
                <span>Barang Keluar Bulan Ini</span>
                <h3>{{ $formatAngka($totalKeluarBulanIni) }}</h3>
                <small>Total kuantitas pengeluaran</small>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-icon habis">
                <i class="bi bi-x-circle"></i>
            </div>
            <div>
                <span>Barang Habis / Minus</span>
                <h3>{{ $formatAngka($outOfStock) }}</h3>
                <small>Perlu dilakukan pengecekan</small>
            </div>
        </div>

    </div>

    {{-- GRAFIK DAN TOP BARANG --}}
    <div class="dashboard-grid">

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h5>Aktivitas Persediaan</h5>
                    <p>Perbandingan barang masuk dan keluar selama 6 bulan</p>
                </div>
                <span class="panel-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </span>
            </div>

            <div class="chart-legend">
                <span><i class="legend-dot masuk"></i> Barang Masuk</span>
                <span><i class="legend-dot keluar"></i> Barang Keluar</span>
            </div>

            <div class="bar-chart">
                @foreach ($grafikBulanan as $data)
                    @php
                        $tinggiMasuk = ($data['masuk'] / $maksGrafik) * 100;
                        $tinggiKeluar = ($data['keluar'] / $maksGrafik) * 100;
                    @endphp

                    <div class="chart-column">
                        <div class="chart-bars">
                            <div class="bar-wrapper">
                                <div
                                    class="chart-bar masuk"
                                    style="height: {{ max($data['masuk'] > 0 ? 4 : 0, $tinggiMasuk) }}%;"
                                    title="Masuk: {{ $formatAngka($data['masuk']) }}">
                                </div>
                            </div>

                            <div class="bar-wrapper">
                                <div
                                    class="chart-bar keluar"
                                    style="height: {{ max($data['keluar'] > 0 ? 4 : 0, $tinggiKeluar) }}%;"
                                    title="Keluar: {{ $formatAngka($data['keluar']) }}">
                                </div>
                            </div>
                        </div>

                        <span class="chart-label">{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h5>Barang Paling Banyak Keluar</h5>
                    <p>5 barang dengan pengeluaran tertinggi</p>
                </div>
                <span class="panel-icon">
                    <i class="bi bi-trophy"></i>
                </span>
            </div>

            @forelse ($topBarangKeluar as $index => $item)
                <div class="top-item">
                    <div class="top-number">{{ $index + 1 }}</div>
                    <div class="top-info">
                        <strong>{{ $item->nama_barang }}</strong>
                        <small>{{ $item->kode_barang }}</small>
                    </div>
                    <div class="top-qty">
                        {{ $formatAngka($item->total_keluar) }}
                        <small>Qty</small>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada data pengeluaran.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- STOK PERLU PERHATIAN --}}
    <div class="dashboard-panel stock-panel">
        <div class="panel-header">
            <div>
                <h5>Perhatian Stok Barang</h5>
                <p>Barang dengan stok 10 atau kurang, termasuk stok habis dan minus</p>
            </div>
            <span class="warning-count">
                {{ $peringatanStok->count() }} barang ditampilkan
            </span>
        </div>

        <div class="table-responsive">
            <table class="table dashboard-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        @if ($isGlobal)
                            <th>Cabang</th>
                        @endif
                        <th>Lokasi</th>
                        <th class="text-end">Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peringatanStok as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->kode_barang }}</strong></td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->kategori }}</td>

                            @if ($isGlobal)
                                <td>{{ $item->nama_cabang ?? '-' }}</td>
                            @endif

                            <td>{{ $item->lokasi ?? '-' }}</td>

                            <td class="text-end">
                                <strong class="{{ $item->stok <= 0 ? 'text-danger' : 'text-warning' }}">
                                    {{ $formatAngka($item->stok) }}
                                </strong>
                                <small>{{ $item->satuan }}</small>
                            </td>

                            <td>
                                @if ($item->stok <= 0)
                                    <span class="status-badge habis">Habis / Minus</span>
                                @else
                                    <span class="status-badge menipis">Menipis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobal ? 8 : 7 }}" class="text-center empty-table">
                                <i class="bi bi-check-circle"></i>
                                Semua stok barang dalam kondisi aman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- AKTIVITAS TERBARU --}}
    <div class="dashboard-grid recent-grid">

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h5>Penerimaan Terbaru</h5>
                    <p>5 transaksi barang masuk terakhir</p>
                </div>
                <span class="panel-icon">
                    <i class="bi bi-box-arrow-in-down"></i>
                </span>
            </div>

            @forelse ($recentPenerimaan as $item)
                <div class="recent-item">
                    <div class="recent-icon masuk">
                        <i class="bi bi-download"></i>
                    </div>
                    <div class="recent-info">
                        <strong>{{ $item->nomor_penerimaan }}</strong>
                        <small>{{ $item->nama_supplier ?? 'Supplier tidak tersedia' }}</small>
                        @if ($isGlobal)
                            <small>{{ $item->nama_cabang ?? '-' }}</small>
                        @endif
                    </div>
                    <div class="recent-date">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada penerimaan.</p>
                </div>
            @endforelse
        </div>

        <div class="dashboard-panel">
            <div class="panel-header">
                <div>
                    <h5>Pengeluaran Terbaru</h5>
                    <p>5 transaksi barang keluar terakhir</p>
                </div>
                <span class="panel-icon">
                    <i class="bi bi-box-arrow-up"></i>
                </span>
            </div>

            @forelse ($recentPengeluaran as $item)
                <div class="recent-item">
                    <div class="recent-icon keluar">
                        <i class="bi bi-upload"></i>
                    </div>
                    <div class="recent-info">
                        <strong>{{ $item->nomor_pengeluaran }}</strong>
                        <small>{{ $item->divisi }} — {{ $item->nama_pengambil }}</small>
                        @if ($isGlobal)
                            <small>{{ $item->nama_cabang ?? '-' }}</small>
                        @endif
                    </div>
                    <div class="recent-date">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada pengeluaran.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- DAFTAR BARANG --}}
    <div class="dashboard-panel inventory-panel">

        <div class="panel-header inventory-header">
            <div>
                <h5>Daftar Persediaan Barang</h5>
                <p>Pencarian dan pemantauan stok barang</p>
            </div>
            @can('superadmin')
            <a href="{{ url('/databarang/barang') }}" class="btn-dashboard">
                <i class="bi bi-box-seam"></i>
                Kelola Barang
            </a>
            @endcan
        </div>

        <form method="GET" action="{{ url('/dashboard') }}" class="filter-form">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari kode, nama, kategori, cabang, atau lokasi...">
            </div>

            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ $status === 'tersedia' ? 'selected' : '' }}>
                    Tersedia
                </option>
                <option value="menipis" {{ $status === 'menipis' ? 'selected' : '' }}>
                    Menipis
                </option>
                <option value="habis" {{ $status === 'habis' ? 'selected' : '' }}>
                    Habis / Minus
                </option>
            </select>

            <button type="submit" class="btn-dashboard">
                Cari
            </button>

            @if ($search !== '' || $status !== '')
                <a href="{{ url('/dashboard') }}" class="btn-reset">Reset</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table dashboard-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        @if ($isGlobal)
                            <th>Cabang</th>
                        @endif
                        <th>Lokasi</th>
                        <th class="text-end">Stok</th>
                        <th class="text-end">Harga Rata-rata</th>
                        <th class="text-end">Nilai Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($barang as $index => $item)
                        <tr>
                            <td>{{ $barang->firstItem() + $index }}</td>
                            <td><strong>{{ $item->kode_barang }}</strong></td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->kategori }}</td>

                            @if ($isGlobal)
                                <td>{{ $item->nama_cabang ?? '-' }}</td>
                            @endif

                            <td>{{ $item->lokasi ?? '-' }}</td>

                            <td class="text-end">
                                {{ $formatAngka($item->stok) }}
                                <small>{{ $item->satuan }}</small>
                            </td>

                            <td class="text-end">
                                Rp {{ $formatAngka($item->harga_rata_rata) }}
                            </td>

                            <td class="text-end">
                                Rp {{ $formatAngka($item->nilai_saldo) }}
                            </td>

                            <td>
                                @if ($item->stok <= 0)
                                    <span class="status-badge habis">Habis / Minus</span>
                                @elseif ($item->stok <= 10)
                                    <span class="status-badge menipis">Menipis</span>
                                @else
                                    <span class="status-badge tersedia">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGlobal ? 10 : 9 }}" class="text-center empty-table">
                                Tidak ada data barang yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan
                {{ $barang->firstItem() ?? 0 }}
                sampai
                {{ $barang->lastItem() ?? 0 }}
                dari {{ $barang->total() }} barang
            </div>

            {{ $barang->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>

<style>
.dashboard-page {
    color: #26364a;
    padding-bottom: 25px;
    font-size: 13px;
}

.dashboard-page * {
    box-sizing: border-box;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 22px;
}

.dashboard-header h4 {
    font-size: 21px;
    font-weight: 700;
    color: #243b5a;
    margin: 0 0 5px;
}

.dashboard-header p,
.panel-header p {
    color: #8492a6;
    margin: 0;
    font-size: 12px;
}

.dashboard-date {
    color: #64748b;
    background: #fff;
    border: 1px solid #e5eaf1;
    padding: 9px 13px;
    border-radius: 9px;
    white-space: nowrap;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.stat-card,
.activity-card {
    display: flex;
    align-items: center;
    gap: 13px;
    background: #fff;
    border: 1px solid #e8edf4;
    border-radius: 12px;
    padding: 17px;
    min-width: 0;
    box-shadow: 0 3px 12px rgba(35, 55, 80, .035);
}

.stat-icon,
.activity-icon {
    width: 43px;
    height: 43px;
    min-width: 43px;
    border-radius: 11px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 19px;
}

.stat-icon.blue { background: #eaf1ff; color: #4778cf; }
.stat-icon.cyan { background: #e7f7fb; color: #2699b5; }
.stat-icon.green { background: #e9f7ef; color: #32966a; }
.stat-icon.orange { background: #fff3e5; color: #d88b35; }

.stat-card span,
.activity-card span {
    display: block;
    color: #78879a;
    font-size: 12px;
    margin-bottom: 5px;
}

.stat-card h3,
.activity-card h3 {
    color: #263b57;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 3px;
}

.stat-card small,
.activity-card small {
    color: #a0aaba;
    font-size: 10px;
}

.stat-card .stat-currency {
    font-size: 17px;
    overflow-wrap: anywhere;
}

.activity-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 18px;
}

.activity-icon.masuk { background: #e8f6ee; color: #31976a; }
.activity-icon.keluar { background: #fff0e9; color: #d77a4a; }
.activity-icon.habis { background: #fff0f0; color: #d65c65; }

.dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(300px, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.dashboard-panel {
    background: #fff;
    border: 1px solid #e8edf4;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 15px;
    box-shadow: 0 3px 12px rgba(35, 55, 80, .035);
    min-width: 0;
}

.dashboard-grid .dashboard-panel {
    margin-bottom: 0;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 17px;
}

.panel-header h5 {
    font-size: 14px;
    color: #2c405b;
    font-weight: 700;
    margin: 0 0 5px;
}

.panel-icon {
    width: 35px;
    height: 35px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef3fb;
    color: #5479b6;
    font-size: 16px;
}

.chart-legend {
    display: flex;
    gap: 18px;
    margin-bottom: 12px;
    color: #78879a;
    font-size: 11px;
}

.legend-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 5px;
}

.legend-dot.masuk { background: #579bce; }
.legend-dot.keluar { background: #e6a36e; }

.bar-chart {
    height: 190px;
    display: flex;
    align-items: stretch;
    justify-content: space-around;
    gap: 8px;
    border-bottom: 1px solid #e8edf4;
    padding: 10px 5px 0;
}

.chart-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.chart-bars {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 5px;
    width: 100%;
    height: 145px;
}

.bar-wrapper {
    height: 100%;
    width: 23px;
    display: flex;
    align-items: flex-end;
}

.chart-bar {
    width: 100%;
    min-height: 0;
    border-radius: 5px 5px 0 0;
    transition: height .2s ease;
}

.chart-bar.masuk { background: #579bce; }
.chart-bar.keluar { background: #e6a36e; }

.chart-label {
    font-size: 10px;
    color: #8290a2;
    margin-top: 9px;
    white-space: nowrap;
}

.top-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f2f6;
}

.top-item:last-child { border-bottom: 0; }

.top-number {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #eef3fb;
    color: #5479b6;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 700;
    font-size: 12px;
}

.top-info,
.recent-info {
    min-width: 0;
    flex: 1;
}

.top-info strong,
.recent-info strong {
    display: block;
    color: #344760;
    font-size: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.top-info small,
.recent-info small {
    display: block;
    color: #9aa5b4;
    font-size: 10px;
    margin-top: 3px;
}

.top-qty {
    color: #355d8b;
    font-weight: 700;
    text-align: right;
    white-space: nowrap;
}

.top-qty small {
    display: block;
    color: #9aa5b4;
    font-size: 10px;
    font-weight: 400;
}

.warning-count {
    background: #fff5e8;
    color: #c58a42;
    padding: 6px 10px;
    border-radius: 7px;
    font-size: 10px;
    white-space: nowrap;
}

.dashboard-table {
    margin: 0;
    font-size: 12px;
    vertical-align: middle;
    white-space: nowrap;
}

.dashboard-table thead th {
    background: #f5f8fc;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    padding: 12px 10px;
    border-bottom: 1px solid #e7edf5;
}

.dashboard-table tbody td {
    padding: 11px 10px;
    color: #526176;
    border-color: #edf1f5;
}

.dashboard-table tbody tr:hover {
    background: #f9fbfe;
}

.dashboard-table td strong {
    color: #344a66;
}

.status-badge {
    display: inline-block;
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
}

.status-badge.tersedia {
    background: #eaf6ef;
    color: #34865d;
}

.status-badge.menipis {
    background: #fff5e6;
    color: #c28a36;
}

.status-badge.habis {
    background: #fff0f0;
    color: #cc5e65;
}

.empty-state,
.empty-table {
    text-align: center;
    color: #9aa6b5;
    padding: 25px 10px !important;
}

.empty-state i,
.empty-table i {
    display: block;
    font-size: 23px;
    color: #a9b7c8;
    margin-bottom: 7px;
}

.empty-state p {
    margin: 0;
    font-size: 11px;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f2f6;
}

.recent-item:last-child { border-bottom: 0; }

.recent-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.recent-icon.masuk {
    color: #31976a;
    background: #e8f6ee;
}

.recent-icon.keluar {
    color: #d77a4a;
    background: #fff0e9;
}

.recent-date {
    font-size: 10px;
    color: #8d9aac;
    white-space: nowrap;
}

.inventory-header {
    align-items: center;
}

.btn-dashboard {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 13px;
    border: 0;
    border-radius: 8px;
    background: #4778b9;
    color: #fff;
    font-size: 11px;
    text-decoration: none;
    cursor: pointer;
    transition: .2s;
}

.btn-dashboard:hover {
    background: #35649f;
    color: #fff;
}

.filter-form {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 15px;
}

.search-box {
    flex: 1;
    min-width: 180px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9aa7b7;
}

.search-box input,
.filter-select {
    width: 100%;
    height: 36px;
    border: 1px solid #e1e7ef;
    border-radius: 8px;
    background: #fff;
    color: #526176;
    font-size: 11px;
    outline: none;
}

.search-box input {
    padding: 8px 12px 8px 34px;
}

.filter-select {
    width: 155px;
    padding: 0 10px;
}

.search-box input:focus,
.filter-select:focus {
    border-color: #8eadd4;
    box-shadow: 0 0 0 3px rgba(71, 120, 185, .08);
}

.btn-reset {
    color: #77869a;
    font-size: 11px;
    text-decoration: none;
    padding: 8px;
}

.btn-reset:hover { color: #355d8b; }

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.pagination-info {
    color: #8b98a9;
    font-size: 11px;
}

.pagination-wrapper .pagination {
    margin: 0;
}

.pagination-wrapper .page-link {
    color: #5479b6;
    font-size: 11px;
    border-color: #e5eaf1;
}

.pagination-wrapper .page-item.active .page-link {
    background: #4778b9;
    border-color: #4778b9;
    color: #fff;
}

@media (max-width: 1100px) {
    .dashboard-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .dashboard-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .activity-summary {
        grid-template-columns: 1fr;
    }

    .filter-form {
        align-items: stretch;
        flex-wrap: wrap;
    }

    .search-box {
        flex-basis: 100%;
    }

    .filter-select {
        flex: 1;
        width: auto;
    }

    .inventory-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .pagination-wrapper {
        align-items: flex-start;
        flex-direction: column;
    }

    .dashboard-panel {
        padding: 14px;
    }
}

@media (max-width: 450px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .stat-card,
    .activity-card {
        padding: 14px;
    }

    .warning-count {
        white-space: normal;
        text-align: right;
    }
}
</style>

@endsection
