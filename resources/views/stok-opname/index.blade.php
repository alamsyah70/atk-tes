@extends('Layouts.main')

@section('container')

<style>
    .opname-page {
        color: #243247;
    }

    .opname-page .page-title {
        font-size: 23px;
        font-weight: 700;
        color: #183b65;
    }

    .opname-page .page-subtitle {
        color: #7b8798;
        font-size: 13px;
    }

    .opname-page .panel {
        background: #fff;
        border: 1px solid #e6ebf2;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(20, 45, 80, .04);
        margin-bottom: 20px;
    }

    .opname-page .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #526174;
    }

    .opname-page .form-control,
    .opname-page .form-select {
        min-height: 39px;
        border-color: #dce3ec;
        font-size: 13px;
    }

    .opname-page .btn {
        min-height: 38px;
        font-size: 13px;
        font-weight: 600;
    }

    .opname-page table th {
        background: #f3f6fa;
        color: #526174;
        font-size: 12px;
        white-space: nowrap;
        padding: 12px;
    }

    .opname-page table td {
        font-size: 13px;
        padding: 12px;
        vertical-align: middle;
    }

    .opname-page .badge-status {
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .opname-page .pagination {
        margin-bottom: 0;
    }

    .opname-page .table-responsive {
        overflow-x: auto;
    }

    .opname-page .filter-search {
        max-width: 300px;
    }

    .opname-page .summary-number {
        font-size: 21px;
        font-weight: 700;
        color: #183b65;
    }

    .opname-page .summary-label {
        color: #7b8798;
        font-size: 12px;
    }

    .opname-page .summary-card {
        border: 1px solid #e6ebf2;
        border-radius: 10px;
        padding: 14px;
        height: 100%;
    }

    @media (max-width: 768px) {

        .opname-page .panel {
            padding: 14px;
        }

        .opname-page .filter-search {
            max-width: 100%;
            width: 100%;
        }

        .opname-page .page-title {
            font-size: 20px;
        }

    }
</style>

<div class="container-fluid opname-page">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

        <div>
            <div class="page-title">
                Stok Opname
            </div>

            <div class="page-subtitle">
                Pemeriksaan dan pencocokan stok fisik dengan stok sistem.
            </div>
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

    {{-- RINGKASAN --}}
    <div class="row g-3 mb-3">

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="summary-label">
                    Total Sesi Opname
                </div>

                <div class="summary-number">
                    {{ number_format($stokOpnames->total()) }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="summary-label">
                    Data Ditampilkan
                </div>

                <div class="summary-number">
                    {{ $stokOpnames->count() }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card bg-white">
                <div class="summary-label">
                    Halaman
                </div>

                <div class="summary-number">
                    {{ $stokOpnames->currentPage() }}
                    <span class="fs-6 text-muted">
                        / {{ $stokOpnames->lastPage() }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- FORM BUAT OPNAME --}}
    <div class="panel">

        <h6 class="fw-bold mb-3">
            <i class="bi bi-plus-circle me-1"></i>
            Buat Sesi Stok Opname
        </h6>

        <form action="{{ route('stok-opname.store') }}"
              method="POST">

            @csrf

            <div class="row g-3 align-items-end">

                @if($isGlobal)

                    <div class="col-md-4">

                        <label class="form-label">
                            Cabang
                            <span class="text-danger">*</span>
                        </label>

                        <select name="branch_id"
                                class="form-select"
                                required>

                            <option value="">
                                -- Pilih Cabang --
                            </option>

                            @foreach($branches as $branch)

                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>

                                    {{ $branch->nama_cabang }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                @endif

                <div class="col-md-3">

                    <label class="form-label">
                        Tanggal Opname
                        <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required>

                </div>

                <div class="col-md-{{ $isGlobal ? '3' : '6' }}">

                    <label class="form-label">
                        Keterangan
                    </label>

                    <input type="text"
                           name="keterangan"
                           class="form-control"
                           maxlength="255"
                           value="{{ old('keterangan') }}"
                           placeholder="Contoh: Stok opname akhir bulan">

                </div>

                <div class="col-md-2">

                    <button type="submit"
                            class="btn btn-primary w-100">

                        <i class="bi bi-plus-lg me-1"></i>
                        Buat Opname

                    </button>

                </div>

            </div>

            <small class="text-muted d-block mt-3">

                <i class="bi bi-info-circle me-1"></i>

                Sistem akan mengambil seluruh master barang yang terupload.

            </small>

        </form>

    </div>

    {{-- RIWAYAT OPNAME --}}
    <div class="panel">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">

            <div>
                <h6 class="fw-bold mb-1">
                    Riwayat Stok Opname
                </h6>

                <small class="text-muted">
                    Daftar seluruh sesi pemeriksaan stok.
                </small>
            </div>

            <input type="text"
                   id="searchOpname"
                   class="form-control filter-search"
                   placeholder="Cari nomor opname atau cabang...">

        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0"
                   id="tabelOpname">

                <thead>

                    <tr>
                        <th width="50">No</th>

                        <th>Nomor Opname</th>

                        @if($isGlobal)
                            <th>Cabang</th>
                        @endif

                        <th>Tanggal</th>

                        <th>Jumlah Barang</th>

                        <th>Pembuat</th>

                        <th>Status</th>

                        <th width="100" class="text-center">
                            Aksi
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($stokOpnames as $index => $opname)

                        <tr class="opname-row"
                            data-search="{{ strtolower(
                                $opname->nomor_opname . ' ' .
                                ($opname->branch->nama_cabang ?? '') . ' ' .
                                ($opname->user->user ?? '')
                            ) }}">

                            <td>
                                {{ $stokOpnames->firstItem() + $index }}
                            </td>

                            <td class="fw-semibold">
                                {{ $opname->nomor_opname }}
                            </td>

                            @if($isGlobal)

                                <td>
                                    {{ $opname->branch->nama_cabang ?? '-' }}
                                </td>

                            @endif

                            <td>
                                {{ $opname->tanggal?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ number_format($opname->details_count ?? 0) }}
                                barang
                            </td>

                            <td>
                                {{ $opname->user->user ?? '-' }}
                            </td>

                            <td>

                                @if($opname->status === 'dibatalkan' || $opname->dibatalkan_at)

                                    <span class="badge bg-danger badge-status">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Dibatalkan
                                    </span>

                                @elseif($opname->status === 'selesai')

                                    <span class="badge bg-success badge-status">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Selesai
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark badge-status">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Draft
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                <a href="{{ route('stok-opname.show', $opname->id) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye me-1"></i>
                                    Detail

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="{{ $isGlobal ? 8 : 7 }}"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                Belum ada sesi stok opname.

                            </td>

                        </tr>

                    @endforelse

                    <tr id="emptySearch"
                        style="display:none;">

                        <td colspan="{{ $isGlobal ? 8 : 7 }}"
                            class="text-center text-muted py-4">

                            Tidak ditemukan data yang sesuai pencarian.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

            <small class="text-muted">

                Menampilkan
                {{ $stokOpnames->firstItem() ?? 0 }}

                sampai
                {{ $stokOpnames->lastItem() ?? 0 }}

                dari
                {{ $stokOpnames->total() }}
                data

            </small>

            {{ $stokOpnames->links() }}

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // PENCARIAN RIWAYAT OPNAME
    const searchInput = document.getElementById('searchOpname');

    const rows = document.querySelectorAll('.opname-row');

    const emptySearch = document.getElementById('emptySearch');

    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            let visibleCount = 0;

            rows.forEach(function (row) {

                const text = row.dataset.search || '';

                const match = text.includes(keyword);

                row.style.display = match ? '' : 'none';

                if (match) {
                    visibleCount++;
                }

            });

            if (emptySearch) {
                emptySearch.style.display =
                    visibleCount === 0 && rows.length > 0 ? '' : 'none';
            }

        });

    }

});
</script>

@endsection
