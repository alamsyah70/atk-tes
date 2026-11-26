@extends('layouts.main')

@section('container')

<style>
    /* =========================================
       HALAMAN LAPORAN BULANAN DAN TAHUNAN
    ========================================= */

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

    /* CARD */

    .laporan-page .report-card {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(31, 45, 61, .04);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .laporan-page .report-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 17px 20px;
        border-bottom: 1px solid #e9eef5;
    }

    .laporan-page .report-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 9px;
        background: #edf4fd;
        color: #2463ad;
        font-size: 17px;
    }

    .laporan-page .report-icon.annual {
        background: #f0f2fa;
        color: #586a9b;
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
        padding: 20px;
    }

    /* FILTER */

    .laporan-page .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(150px, 1fr));
        gap: 16px;
        align-items: end;
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
        transition: .2s ease;
    }

    .laporan-page .filter-field input:focus,
    .laporan-page .filter-field select:focus {
        border-color: #3478d4;
        box-shadow: 0 0 0 3px rgba(52, 120, 212, .10);
    }

    .laporan-page .filter-hint {
        margin-top: 6px;
        color: #8492a6;
        font-size: 11px;
    }

    /* TOMBOL */

    .laporan-page .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 20px;
        padding-top: 17px;
        border-top: 1px solid #edf1f5;
    }

    .laporan-page .btn-laporan {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 39px;
        padding: 8px 16px;
        border: 0;
        border-radius: 7px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s ease;
    }

    .laporan-page .btn-laporan:hover {
        transform: translateY(-1px);
    }

    .laporan-page .btn-primary-laporan {
        background: #2463ad;
        color: #fff;
    }

    .laporan-page .btn-primary-laporan:hover {
        background: #194d8b;
        color: #fff;
    }

    .laporan-page .btn-pdf {
        background: #d94343;
        color: #fff;
    }

    .laporan-page .btn-pdf:hover {
        background: #bd3030;
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

    /* PEMISAH ANTAR LAPORAN */

    .laporan-page .section-caption {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 25px 0 12px;
        color: #718096;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .laporan-page .section-caption::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e7edf5;
    }

    /* KETERANGAN */

    .laporan-page .info-panel {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        margin-top: 18px;
        background: #f5f8fc;
        border: 1px solid #e7edf5;
        border-radius: 8px;
        color: #65758b;
        font-size: 12px;
        line-height: 1.6;
    }

    .laporan-page .info-panel i {
        margin-top: 2px;
        color: #3478d4;
    }

    /* RESPONSIVE */

    @media (max-width: 992px) {
        .laporan-page .filter-grid {
            grid-template-columns: repeat(2, minmax(140px, 1fr));
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

        .laporan-page .report-card-header {
            padding: 14px;
        }

        .laporan-page .report-card-body {
            padding: 14px;
        }

        .laporan-page .filter-grid {
            grid-template-columns: 1fr;
        }

        .laporan-page .filter-actions {
            flex-direction: column;
        }

        .laporan-page .btn-laporan {
            width: 100%;
        }
    }
</style>


<div class="container-fluid laporan-page">

    {{-- HEADER HALAMAN --}}
    <div class="page-heading">
        <div>
            <h3 class="page-title">Laporan Rekapitulasi</h3>
            <p class="page-subtitle">
                Rekapitulasi penerimaan dan pemakaian barang ATK berdasarkan periode bulanan dan tahunan.
            </p>
        </div>
    </div>


    {{-- =========================================
         LAPORAN BULANAN
    ========================================== --}}
    <div class="report-card">

        <div class="report-card-body">

            <form method="GET" id="formLaporanBulanan">

                <div class="filter-grid">

                    {{-- BULAN --}}
                    <div class="filter-field">
                        <label for="bulan">
                            <i class="far fa-calendar me-1"></i>
                            Bulan
                        </label>

                        <select name="bulan" id="bulan">
                            @foreach(range(1, 12) as $bulan)
                                <option value="{{ $bulan }}"
                                    {{ request('bulan', now()->month) == $bulan ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TAHUN --}}
                    <div class="filter-field">
                        <label for="tahun_bulanan">
                            <i class="far fa-calendar-check me-1"></i>
                            Tahun
                        </label>

                        <select name="tahun" id="tahun_bulanan">
                            @for($tahun = now()->year; $tahun >= now()->year - 10; $tahun--)
                                <option value="{{ $tahun }}"
                                    {{ request('tahun', now()->year) == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- CABANG --}}
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                        <div class="filter-field">
                            <label for="branch_bulanan">
                                <i class="fas fa-code-branch me-1"></i>
                                Cabang
                            </label>

                            <select name="branch_id" id="branch_bulanan">
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


                {{-- TOMBOL EXPORT BULANAN --}}
                <div class="section-caption">
                    <i class="fas fa-file-pdf"></i>
                    Jenis Laporan Bulanan
                </div>

                <div class="filter-actions">

                    <button type="button"
                            class="btn-laporan btn-primary-laporan"
                            onclick="exportLaporanBulanan('{{ route('laporan.penerimaan.supplier.pdf') }}')">

                        <i class="fas fa-file-pdf"></i>
                        Penerimaan Per Supplier
                    </button>

                    <button type="button"
                            class="btn-laporan btn-pdf"
                            onclick="exportLaporanBulanan('{{ route('laporan.pemakaian.divisi.pdf') }}')">

                        <i class="fas fa-file-pdf"></i>
                        Pemakaian Per Divisi
                    </button>

                </div>

                <div class="info-panel">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        Pilih bulan, tahun, dan cabang terlebih dahulu.
                        Kemudian klik jenis laporan yang ingin dicetak.
                        File PDF akan terbuka pada tab baru.
                    </div>
                </div>

            </form>
        </div>
    </div>


    {{-- =========================================
         LAPORAN TAHUNAN
    ========================================== --}}
    <div class="report-card">

        <div class="report-card-body">

            <form method="GET"
                  action="{{ route('laporan.pemakaian.tahunan.pdf') }}"
                  target="_blank">

                <div class="filter-grid">

                    {{-- TAHUN --}}
                    <div class="filter-field">
                        <label for="tahun_tahunan">
                            <i class="far fa-calendar-check me-1"></i>
                            Tahun
                        </label>

                        <select name="tahun" id="tahun_tahunan" required>
                            @for($tahun = now()->year; $tahun >= now()->year - 10; $tahun--)
                                <option value="{{ $tahun }}"
                                    {{ request('tahun', now()->year) == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- CABANG --}}
                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin Pusat']))
                        <div class="filter-field">
                            <label for="branch_tahunan">
                                <i class="fas fa-code-branch me-1"></i>
                                Cabang
                            </label>

                            <select name="branch_id" id="branch_tahunan">
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


                {{-- TOMBOL EXPORT TAHUNAN --}}
                <div class="section-caption">
                    <i class="fas fa-file-pdf"></i>
                    Jenis Laporan Tahunan
                </div>

                <div class="filter-actions">

                    <button type="submit" class="btn-laporan btn-pdf">
                        <i class="fas fa-file-pdf"></i>
                        Export Laporan Tahunan
                    </button>

                </div>

                <div class="info-panel">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        Laporan tahunan menampilkan rekapitulasi pemakaian
                        per divisi selama 12 bulan, termasuk total pemakaian
                        dan perubahan pemakaian setiap bulan.
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>


<script>
    // Export laporan bulanan dengan parameter filter
    function exportLaporanBulanan(url) {

        const form = document.getElementById('formLaporanBulanan');

        const params = new URLSearchParams(new FormData(form));

        const queryString = params.toString();

        const exportUrl = queryString
            ? url + '?' + queryString
            : url;

        window.open(exportUrl, '_blank');
    }
</script>

@endsection
