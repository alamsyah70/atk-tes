@extends('Layouts.main')

@section('container')

<style>
    .opname-detail {
        color: #243247;
    }

    .opname-detail .page-title {
        font-size: 22px;
        font-weight: 700;
        color: #183b65;
    }

    .opname-detail .page-subtitle {
        color: #7b8798;
        font-size: 13px;
    }

    .opname-detail .panel {
        background: #fff;
        border: 1px solid #e6ebf2;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, .04);
        margin-bottom: 18px;
    }

    .opname-detail .stat-card {
        border: 1px solid #e6ebf2;
        border-radius: 10px;
        padding: 15px;
        height: 100%;
        background: #fff;
    }

    .opname-detail .stat-label {
        color: #7b8798;
        font-size: 12px;
    }

    .opname-detail .stat-value {
        font-size: 23px;
        font-weight: 700;
        color: #183b65;
    }

    .opname-detail .form-control {
        min-height: 36px;
        font-size: 13px;
    }

    .opname-detail .input-fisik {
        text-align: center;
    }

    .opname-detail table th {
        background: #f3f6fa;
        color: #526174;
        font-size: 12px;
        white-space: nowrap;
        padding: 12px;
    }

    .opname-detail table td {
        font-size: 13px;
        padding: 9px 12px;
        vertical-align: middle;
    }

    .opname-detail .btn {
        font-size: 13px;
        font-weight: 600;
        min-height: 37px;
    }

    .opname-detail .search-input {
        max-width: 300px;
    }

    .opname-detail .difference {
        font-weight: 700;
    }

    .opname-detail .table-responsive {
        max-height: 650px;
        overflow: auto;
    }

    .opname-detail thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .opname-detail .status-badge {
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .opname-detail .info-label {
        color: #7b8798;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .opname-detail .info-value {
        color: #243247;
        font-size: 14px;
        font-weight: 600;
    }

    .opname-detail .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    @media (max-width: 768px) {
        .opname-detail .page-title {
            font-size: 19px;
        }

        .opname-detail .panel {
            padding: 13px;
        }

        .opname-detail .search-input {
            max-width: 100%;
            width: 100%;
        }

        .opname-detail .action-group {
            width: 100%;
        }

        .opname-detail .action-group .btn {
            flex: 1 1 auto;
        }
    }
</style>

<div class="container-fluid opname-detail">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <div class="page-title">Detail Stok Opname</div>

            <div class="page-subtitle">
                {{ $stokOpname->nomor_opname }}
                |
                {{ $stokOpname->branch->nama_cabang ?? '-' }}
            </div>
        </div>

        <div class="action-group">

            @if($stokOpname->status === 'selesai' || $stokOpname->status === 'dibatalkan')

                <a href="{{ route('stok-opname.export-pdf', $stokOpname->id) }}"
                   class="btn btn-danger">
                    <i class="bi bi-file-pdf me-1"></i>
                    Export PDF
                </a>

                <a href="{{ route('stok-opname.export-excel', $stokOpname->id) }}"
                   class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Export Excel
                </a>

            @endif

            <a href="{{ route('stok-opname.print-sheet', $stokOpname->id) }}"
               target="_blank"
               class="btn btn-outline-primary">
                <i class="bi bi-printer me-1"></i>
                Lembar Pemeriksaan
            </a>

            <a href="{{ route('stok-opname.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INFORMASI OPNAME --}}
    <div class="panel">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

            <h6 class="fw-bold mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Informasi Stok Opname
            </h6>

            @if($stokOpname->status === 'dibatalkan' || $stokOpname->dibatalkan_at)
                <span class="badge bg-danger status-badge">
                    <i class="bi bi-x-circle me-1"></i>
                    Dibatalkan
                </span>
            @elseif($stokOpname->status === 'selesai')
                <span class="badge bg-success status-badge">
                    <i class="bi bi-check-circle me-1"></i>
                    Selesai
                </span>
            @else
                <span class="badge bg-warning text-dark status-badge">
                    <i class="bi bi-pencil-square me-1"></i>
                    Draft
                </span>
            @endif

        </div>

        <div class="row g-3">

            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-label">Tanggal Opname</div>
                    <div class="fw-bold mt-1">
                        {{ $stokOpname->tanggal?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value">
                        {{ number_format($totalBarang) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-label">Sudah Dihitung</div>
                    <div class="stat-value text-success">
                        {{ number_format($sudahDihitung) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-label">Barang Selisih</div>
                    <div class="stat-value text-danger">
                        {{ number_format($totalSelisih) }}
                    </div>
                </div>
            </div>

        </div>

        <hr>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="info-label">Nomor Opname</div>
                <div class="info-value">
                    {{ $stokOpname->nomor_opname }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Cabang</div>
                <div class="info-value">
                    {{ $stokOpname->branch->nama_cabang ?? '-' }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-label">Petugas Pembuat</div>
                <div class="info-value">
                    {{ $stokOpname->user->user ?? '-' }}
                </div>
            </div>

            <div class="col-md-12">
                <div class="info-label">Keterangan</div>
                <div>
                    {{ $stokOpname->keterangan ?: '-' }}
                </div>
            </div>

        </div>

        {{-- STATUS SELESAI --}}
        @if($stokOpname->status === 'selesai')

            <div class="alert alert-success mt-3 mb-0">
                <i class="bi bi-check-circle me-1"></i>

                <strong>Stok opname telah difinalisasi.</strong>

                <div class="small mt-1">
                    Waktu finalisasi:
                    {{ $stokOpname->finalized_at?->format('d-m-Y H:i') ?? '-' }}
                </div>

                <div class="small">
                    Hasil pemeriksaan sudah masuk ke transaksi penyesuaian stok.
                </div>
            </div>

        @endif

        {{-- STATUS DIBATALKAN --}}
        @if($stokOpname->status === 'dibatalkan' || $stokOpname->dibatalkan_at)

            <div class="alert alert-danger mt-3 mb-0">

                <strong>
                    <i class="bi bi-x-circle me-1"></i>
                    Stok opname telah dibatalkan.
                </strong>

                <div class="small mt-2">
                    Waktu pembatalan:
                    {{ $stokOpname->dibatalkan_at
                        ? \Carbon\Carbon::parse($stokOpname->dibatalkan_at)->format('d-m-Y H:i')
                        : '-' }}
                </div>

                <div class="small">
                    Alasan pembatalan:
                    {{ $stokOpname->alasan_pembatalan ?: '-' }}
                </div>

                <div class="small mt-2">
                    Transaksi pembalikan telah dicatat untuk menjaga riwayat stok.
                </div>

            </div>

        @endif

    </div>

    {{-- TABEL BARANG --}}
    <div class="panel">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

            <div>
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-check me-1"></i>
                    Daftar Pemeriksaan Barang
                </h6>

                <small class="text-muted">
                    @if($stokOpname->status === 'draft')
                        Isi stok fisik sesuai hasil penghitungan di lapangan.
                    @else
                        Berikut hasil pemeriksaan stok opname.
                    @endif
                </small>
            </div>

            <input type="text"
                   id="searchBarang"
                   class="form-control search-input"
                   placeholder="Cari kode atau nama barang...">

        </div>

        @if($stokOpname->status === 'draft')

            <form action="{{ route('stok-opname.update', $stokOpname->id) }}"
                  method="POST"
                  id="formOpname">

                @csrf
                @method('PUT')

        @endif

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0"
                   id="tabelOpname">

                <thead>
                    <tr>
                        <th width="45">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th width="130" class="text-center">Stok Sistem</th>
                        <th width="150" class="text-center">Stok Fisik</th>
                        <th width="130" class="text-center">Selisih</th>
                        <th width="120" class="text-center">Keterangan</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($stokOpname->details as $index => $detail)

                        <tr class="barang-row"
                            data-search="{{ strtolower($detail->kode_barang . ' ' . $detail->nama_barang) }}">

                            <td>{{ $index + 1 }}</td>

                            <td class="fw-semibold">
                                {{ $detail->kode_barang }}
                            </td>

                            <td>
                                {{ $detail->nama_barang }}
                            </td>

                            <td class="text-center fw-semibold">
                                {{ number_format($detail->stok_sistem) }}
                            </td>

                            <td>

                                @if($stokOpname->status === 'draft')

                                    <input type="number"
                                           name="stok_fisik[{{ $detail->id }}]"
                                           class="form-control input-fisik"
                                           min="0"
                                           step="1"
                                           required
                                           value="{{ old('stok_fisik.' . $detail->id, $detail->stok_fisik) }}"
                                           placeholder="Stok fisik">

                                @else

                                    <div class="text-center fw-semibold">
                                        {{ $detail->stok_fisik !== null
                                            ? number_format($detail->stok_fisik)
                                            : '-' }}
                                    </div>

                                @endif

                            </td>

                            <td class="text-center difference">

                                @if($detail->selisih === null)

                                    <span class="text-muted">
                                        Belum dihitung
                                    </span>

                                @elseif($detail->selisih > 0)

                                    <span class="text-success">
                                        +{{ number_format($detail->selisih) }}
                                    </span>

                                @elseif($detail->selisih < 0)

                                    <span class="text-danger">
                                        {{ number_format($detail->selisih) }}
                                    </span>

                                @else

                                    <span class="text-success">0</span>

                                @endif

                            </td>

                            <td class="text-center">

                                @if($detail->selisih === null)

                                    <span class="badge bg-secondary">
                                        Belum dihitung
                                    </span>

                                @elseif($detail->selisih == 0)

                                    <span class="badge bg-success">
                                        Sesuai
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        Selisih
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada detail barang.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($stokOpname->status === 'draft')

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

                <small class="text-muted">
                    Pastikan seluruh barang sudah dihitung sebelum finalisasi.
                </small>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Simpan Hasil Hitung
                </button>

            </div>

            </form>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <div class="fw-bold">
                        Finalisasi Stok Opname
                    </div>

                    <small class="text-muted">
                        Setelah finalisasi, data tidak dapat diedit dan selisih
                        akan masuk ke Penyesuaian Stok.
                    </small>
                </div>

                <form action="{{ route('stok-opname.finalize', $stokOpname->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin memfinalisasi stok opname ini? Pastikan seluruh stok fisik sudah benar.')">

                    @csrf

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Finalisasi Opname
                    </button>

                </form>

            </div>

        @elseif($stokOpname->status === 'selesai')

            <div class="alert alert-success mt-3 mb-0">
                <i class="bi bi-check-circle me-1"></i>
                Stok opname telah selesai. Hasil pemeriksaan tidak dapat diubah.
            </div>

            {{-- PEMBATALAN OPNAME --}}
            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <div class="fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Pembatalan Stok Opname
                    </div>

                    <small class="text-muted">
                        Sistem akan membuat transaksi pembalik. Riwayat opname
                        dan transaksi sebelumnya tetap disimpan.
                    </small>
                </div>

                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#modalPembatalan">

                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Batalkan Opname

                </button>

            </div>

        @else

            <div class="alert alert-danger mt-3 mb-0">
                <i class="bi bi-lock me-1"></i>
                Opname sudah dibatalkan. Data hanya dapat dilihat dan tidak dapat diubah.
            </div>

        @endif

    </div>

</div>

{{-- MODAL PEMBATALAN --}}
@if($stokOpname->status === 'selesai')

<div class="modal fade"
     id="modalPembatalan"
     tabindex="-1"
     aria-labelledby="modalPembatalanLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <form action="{{ route('stok-opname.cancel', $stokOpname->id) }}"
                  method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan opname ini? Sistem akan membuat transaksi pembalik.')">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"
                        id="modalPembatalanLabel">

                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Pembatalan Stok Opname

                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-warning small">
                        <strong>Perhatian!</strong>

                        <br>

                        Pembatalan tidak menghapus transaksi sebelumnya.
                        Sistem akan membuat transaksi penyesuaian dengan nilai
                        kebalikan dari penyesuaian hasil opname.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nomor Opname
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ $stokOpname->nomor_opname }}"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Alasan Pembatalan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea name="alasan_pembatalan"
                                  class="form-control"
                                  rows="4"
                                  maxlength="1000"
                                  required
                                  placeholder="Tuliskan alasan pembatalan opname...">{{ old('alasan_pembatalan') }}</textarea>

                        <small class="text-muted">
                            Maksimal 1.000 karakter.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Tutup
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Konfirmasi Pembatalan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif

<script>
document.addEventListener('DOMContentLoaded', function () {

    // PENCARIAN BARANG
    const searchInput = document.getElementById('searchBarang');
    const rows = document.querySelectorAll('.barang-row');

    if (searchInput) {
        searchInput.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            rows.forEach(function (row) {

                const text = row.dataset.search || '';

                row.style.display = text.includes(keyword) ? '' : 'none';

            });

        });
    }

    // VALIDASI INPUT STOK FISIK
    const form = document.getElementById('formOpname');

    if (form) {

        form.addEventListener('submit', function (event) {

            const inputs = form.querySelectorAll('.input-fisik');

            for (const input of inputs) {

                if (input.value.trim() === '' || Number(input.value) < 0) {

                    event.preventDefault();

                    alert('Semua stok fisik wajib diisi dengan angka nol atau lebih.');

                    input.focus();

                    return;
                }

            }

        });

    }

    // BUKA KEMBALI MODAL JIKA VALIDASI PEMBATALAN GAGAL
    @if($errors->has('alasan_pembatalan'))

        const modalElement = document.getElementById('modalPembatalan');

        if (modalElement && window.bootstrap) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

    @endif

});
</script>

@endsection
