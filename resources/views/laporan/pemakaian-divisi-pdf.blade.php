<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemakaian Per Divisi</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #222;
        }

        /* ==============================
           HEADER LAPORAN
        ============================== */

        .header {
            position: relative;
            height: 85px;
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 15px;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
        }

        h2 {
            margin: 0;
            padding-top: 18px;
            font-size: 17px;
        }

        .subtitle {
            margin-top: 5px;
            font-size: 9px;
        }

        .periode {
            margin: 12px 0;
            page-break-inside: avoid;
        }

        /* ==============================
           JUDUL DIVISI
        ============================== */

        h3 {
            margin: 18px 0 7px;
            padding: 7px;
            background: #eaf0f7;
            border-left: 4px solid #2463ad;
            font-size: 12px;

            /* Hindari judul tertinggal di bawah halaman */
            page-break-after: avoid;
        }

        /* ==============================
           JUDUL KATEGORI
        ============================== */

        .kategori-title {
            display: block;
            margin: 8px 0 5px;
            font-weight: bold;
            font-size: 9px;

            page-break-after: avoid;
        }

        .kategori-block {
            page-break-inside: auto;
        }

        /* ==============================
           TABEL
        ============================== */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 5px;
        }

        th {
            background: #edf2f8;
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        td {
            vertical-align: middle;
            font-size: 8px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* ==============================
           SUBTOTAL
        ============================== */

        .subtotal {
            background: #f3f6fa;
            font-weight: bold;
            page-break-inside: avoid;
        }

        .subtotal td {
            font-size: 8px;
        }

        .divisi-total {
            margin-top: 8px;
            page-break-inside: avoid;
        }

        /* ==============================
           GRAND TOTAL
        ============================== */

        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #555;
            text-align: right;
            font-size: 12px;
            font-weight: bold;

            page-break-inside: avoid;
        }

        /* ==============================
           FOOTER
        ============================== */

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #666;
            page-break-inside: avoid;
        }

        /* ==============================
           DATA KOSONG
        ============================== */

        .empty-data {
            text-align: center;
            padding: 20px;
            font-size: 10px;
        }

    </style>
</head>

<body>

    {{-- ==========================================
         HEADER LAPORAN
    =========================================== --}}

    <div class="header">

        <img
            src="{{ public_path('images/logo.png') }}"
            class="logo"
        >

        <h2>LAPORAN PEMAKAIAN PER DIVISI</h2>

        <div class="subtitle">
            Rekapitulasi pemakaian barang berdasarkan kategori
        </div>

    </div>


    {{-- ==========================================
         PERIODE LAPORAN
    =========================================== --}}

    <div class="periode">

        <strong>Periode:</strong>

        {{ \Carbon\Carbon::createFromDate(
            $filter['tahun'],
            $filter['bulan'],
            1
        )->translatedFormat('F Y') }}

    </div>


    {{-- ==========================================
         DATA PER DIVISI
    =========================================== --}}

    @forelse($groups as $divisi => $divisiItems)

        @php
            $kategoriGroups = $divisiItems->groupBy(function ($item) {
                return $item->kategori ?: 'Tanpa Kategori';
            });
        @endphp


        {{-- JUDUL DIVISI --}}

        <h3>
            DIVISI: {{ $divisi }}
        </h3>


        {{-- ======================================
             DATA PER KATEGORI
        ======================================= --}}

        @foreach($kategoriGroups as $kategori => $items)

            <div class="kategori-block">

                <div class="kategori-title">
                    Kategori: {{ $kategori }}
                </div>


                <table>

                    <thead>
                        <tr>
                            <th width="4%">No</th>
                            <th width="8%">Tanggal</th>
                            <th width="11%">No. Pengeluaran</th>
                            <th width="9%">Cabang</th>
                            <th width="9%">Pengambil</th>
                            <th width="8%">Kode Barang</th>
                            <th width="15%">Nama Barang</th>
                            <th width="5%">Qty</th>
                            <th width="6%">Satuan</th>
                            <th width="11%">Harga Pokok</th>
                            <th width="14%">Jumlah Harga Pokok</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($items as $i => $item)

                            <tr>

                                <td class="center">
                                    {{ $i + 1 }}
                                </td>

                                <td class="center">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $item->nomor_pengeluaran ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nama_cabang ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nama_pengambil ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->kode_barang }}
                                </td>

                                <td>
                                    {{ $item->nama_barang }}
                                </td>

                                <td class="right">
                                    {{ number_format($item->qty, 2, ',', '.') }}
                                </td>

                                <td class="center">
                                    {{ $item->satuan ?? '-' }}
                                </td>

                                <td class="right">
                                    Rp {{ number_format($item->harga_pokok ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="right">
                                    Rp {{ number_format($item->jumlah_harga_pokok ?? 0, 0, ',', '.') }}
                                </td>

                            </tr>

                        @endforeach


                        {{-- TOTAL KATEGORI --}}

                        <tr class="subtotal">

                            <td colspan="7" class="right">
                                TOTAL KATEGORI {{ strtoupper($kategori) }}
                            </td>

                            <td class="right">
                                {{ number_format($items->sum('qty'), 2, ',', '.') }}
                            </td>

                            <td colspan="2"></td>

                            <td class="right">
                                Rp {{ number_format($items->sum('jumlah_harga_pokok'), 0, ',', '.') }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        @endforeach


        {{-- ======================================
             TOTAL DIVISI
        ======================================= --}}

        <table class="divisi-total">

            <tbody>

                <tr class="subtotal">

                    <td width="70%" class="right">
                        TOTAL DIVISI: {{ strtoupper($divisi) }}
                    </td>

                    <td class="right">
                        {{ number_format($divisiItems->sum('qty'), 2, ',', '.') }}
                    </td>

                    <td class="right">
                        Rp {{ number_format($divisiItems->sum('jumlah_harga_pokok'), 0, ',', '.') }}
                    </td>

                </tr>

            </tbody>

        </table>


    @empty

        <div class="empty-data">
            Tidak ada data pemakaian pada periode ini.
        </div>

    @endforelse


    {{-- ==========================================
         GRAND TOTAL
    =========================================== --}}

    <div class="grand-total">

        GRAND TOTAL PEMAKAIAN:

        {{ number_format($totalQty, 2, ',', '.') }} unit/item

        &nbsp; | &nbsp;

        Rp {{ number_format($totalNilai, 0, ',', '.') }}

    </div>


    {{-- ==========================================
         FOOTER
    =========================================== --}}

    <div class="footer">

        Dicetak pada:
        {{ now()->format('d-m-Y H:i') }}

    </div>

</body>
</html>
