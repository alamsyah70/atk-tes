@extends('layouts.main')

@section('container')
<div class="card p-3 mb-3">
    <h5 class="mb-3">Laporan Rekapitulasi Bulanan</h5>

    <form method="GET" id="formLaporanBulanan">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach(range(1, 12) as $bulan)
                        <option value="{{ $bulan }}"
                            {{ request('bulan', now()->month) == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($tahun = now()->year; $tahun >= now()->year - 10; $tahun--)
                        <option value="{{ $tahun }}"
                            {{ request('tahun', now()->year) == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endfor
                </select>
            </div>

            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                <div class="col-md-3">
                    <label class="form-label">Cabang</label>
                    <select name="branch_id" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach(\App\Models\Branch::orderBy('nama_cabang')->get() as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="button"
                    class="btn btn-primary"
                    onclick="exportLaporanBulanan('{{ route('laporan.penerimaan.supplier.pdf') }}')">
                <i class="fas fa-file-pdf"></i>
                Penerimaan Per Supplier
            </button>

            <button type="button"
                    class="btn btn-danger"
                    onclick="exportLaporanBulanan('{{ route('laporan.pemakaian.divisi.pdf') }}')">
                <i class="fas fa-file-pdf"></i>
                Pemakaian Per Divisi
            </button>
        </div>
    </form>
</div>

<div class="card p-3 mb-3">
    <h5 class="mb-3">Laporan Tahunan</h5>

    <form method="GET"
          action="{{ route('laporan.pemakaian.tahunan.pdf') }}"
          target="_blank">

        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($tahun = now()->year; $tahun >= now()->year - 10; $tahun--)
                        <option value="{{ $tahun }}"
                            {{ request('tahun', now()->year) == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endfor
                </select>
            </div>

            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                <div class="col-md-3">
                    <label class="form-label">Cabang</label>
                    <select name="branch_id" class="form-select">
                        <option value="">Semua Cabang</option>
                        @foreach(\App\Models\Branch::orderBy('nama_cabang')->get() as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i>
                    Export Laporan Tahunan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function exportLaporanBulanan(url) {
        const form = document.getElementById('formLaporanBulanan');
        const params = new URLSearchParams(new FormData(form));

        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
@endsection