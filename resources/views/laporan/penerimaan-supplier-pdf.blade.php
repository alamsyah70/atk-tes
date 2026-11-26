<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Per Supplier</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #222;
        }

        /* =====================================
           HEADER LAPORAN
        ===================================== */

        .header {
            position: relative;
            height: 85px;
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 15px;
            page-break-inside: avoid;
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

        /* =====================================
           PERIODE
        ===================================== */

        .periode {
            margin: 12px 0;
            page-break-inside: avoid;
        }

        /* =====================================
           JUDUL SUPPLIER
        ===================================== */

        h3 {
            margin: 18px 0 7px;
            padding: 7px;
            background: #eaf0f7;
            border-left: 4px solid #2463ad;
            font-size: 12px;

            /* Hindari judul tertinggal di akhir halaman */
            page-break-after: avoid;
        }

        /* =====================================
           JUDUL KATEGORI
        ===================================== */

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

        /* =====================================
           TABEL
        ===================================== */

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
            vertical-align: middle;
        }

        th {
            background: #edf2f8;
            text-align: center;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        /* =====================================
           PERATAAN
        ===================================== */

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        /* =====================================
           SUBTOTAL
        ===================================== */

        .subtotal {
            background: #f3f6fa;
            font-weight: bold;
            page-break-inside: avoid;
        }

        .subtotal td {
            font-size: 8px;
        }

        .supplier-total {
            margin-top: 8px;
            page-break-inside: avoid;
        }

        /* =====================================
           GRAND TOTAL
        ===================================== */

        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #555;
            text-align: right;
            font-size: 12px;
            font-weight: bold;

            page-break-inside: avoid;
        }

        /* =====================================
           FOOTER
        ===================================== */

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8px;
            color: #666;
            page-break-inside: avoid;
        }

        /* =====================================
           DATA KOSONG
        ===================================== */

        .empty-data {
            padding: 20px;
            text-align: center;
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

        <h2>LAPORAN PENERIMAAN PER SUPPLIER</h2>

        <div class="subtitle">
            Rekapitulasi penerimaan barang berdasarkan kategori
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
         DATA PER SUPPLIER
    =========================================== --}}

    @forelse($groups as $supplierItems)

        @php
            $namaSupplier = $supplierItems->first()->nama_supplier
                ?? 'Tanpa Supplier';

            $kategoriGroups = $supplierItems->groupBy(function ($item) {
                return $item->kategori ?: 'Tanpa Kategori';
            });
        @endphp


        {{-- JUDUL SUPPLIER --}}

        <h3>
            SUPPLIER: {{ $namaSupplier }}
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

                            <th width="9%">Tanggal</th>

                            <th width="13%">No. Penerimaan</th>

                            <th width="10%">Cabang</th>

                            <th width="9%">Kode Barang</th>

                            <th width="17%">Nama Barang</th>

                            <th width="6%">Qty</th>

                            <th width="7%">Satuan</th>

                            <th width="12%">Harga</th>

                            <th width="13%">Jumlah</th>

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
                                    {{ $item->nomor_penerimaan ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nama_cabang ?? '-' }}
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
                                    Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="right">
                                    Rp {{ number_format($item->jumlah ?? 0, 0, ',', '.') }}
                                </td>

                            </tr>

                        @endforeach


                        {{-- TOTAL KATEGORI --}}

                        <tr class="subtotal">

                            <td colspan="6" class="right">
                                TOTAL KATEGORI {{ strtoupper($kategori) }}
                            </td>

                            <td class="right">
                                {{ number_format($items->sum('qty'), 2, ',', '.') }}
                            </td>

                            <td colspan="2"></td>

                            <td class="right">
                                Rp {{ number_format($items->sum('jumlah'), 0, ',', '.') }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        @endforeach


        {{-- ======================================
             TOTAL SUPPLIER
        ======================================= --}}

        <table class="supplier-total">

            <tbody>

                <tr class="subtotal">

                    <td width="70%" class="right">
                        TOTAL SUPPLIER: {{ strtoupper($namaSupplier) }}
                    </td>

                    <td class="right">
                        {{ number_format($supplierItems->sum('qty'), 2, ',', '.') }}
                    </td>

                    <td class="right">
                        Rp {{ number_format($supplierItems->sum('jumlah'), 0, ',', '.') }}
                    </td>

                </tr>

            </tbody>

        </table>


    @empty

        <div class="empty-data">
            Tidak ada data penerimaan pada periode ini.
        </div>

    @endforelse


    {{-- ==========================================
         GRAND TOTAL
    =========================================== --}}

    <div class="grand-total">

        GRAND TOTAL PENERIMAAN:

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
