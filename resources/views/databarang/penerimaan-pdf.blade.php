<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        {{ $penerimaan->nomor_penerimaan }}
    </title>

<style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            position: relative;
            width: 100%;
            height: 85px;
            text-align: center;
            border-bottom: 2px solid #222;
            margin-bottom: 20px;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: auto;
        }

        .header h2 {
            margin: 10px 0 5px;
            font-size: 18px;
        }

        .header p {
            margin: 3px 0;
        }

        .judul {
            text-align: center;
            margin-bottom: 20px;
        }

        .judul h3 {
            margin-bottom: 5px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .informasi td {
            padding: 5px;
            vertical-align: top;
        }

        .detail {
            margin-top: 15px;
        }

        .detail th,
        .detail td {
            border: 1px solid #333;
            padding: 7px;
        }

        .detail th {
            background: #eaf0f7;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bukti {
            margin-top: 20px;
        }

        .bukti img {
            max-width: 250px;
            max-height: 180px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
</style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">

        <img src="{{ public_path('images/logo.png') }}"
             class="logo">

        <h2>INVENTARIS ALAT TULIS KANTOR (ATK)</h2>

        <p>
            {{ $penerimaan->branch->nama_cabang ?? '-' }}
        </p>

    </div>

    {{-- JUDUL --}}
    <div class="judul">

        <h3>BUKTI PENERIMAAN BARANG</h3>

        <p>
            Nomor:
            <strong>{{ $penerimaan->nomor_penerimaan }}</strong>
        </p>

    </div>

    {{-- INFORMASI --}}
    <table class="informasi">

        <tr>
            <td width="18%">Supplier</td>
            <td width="32%">: {{ $penerimaan->supplier->nama_supplier ?? '-' }}</td>

            <td width="18%">Tanggal</td>
            <td width="32%">:
                {{ \Carbon\Carbon::parse($penerimaan->tanggal)->format('d-m-Y') }}
            </td>
        </tr>

        <tr>
            <td>Keterangan</td>
            <td colspan="3">:
                {{ $penerimaan->keterangan ?? '-' }}
            </td>
        </tr>

    </table>

    {{-- DETAIL BARANG --}}
    <table class="detail">

        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Lokasi</th>
                <th width="7%">Qty</th>
                <th width="12%">Harga</th>
                <th width="14%">Jumlah</th>
            </tr>
        </thead>

        <tbody>

            @php
                $totalQty = 0;
                $grandTotal = 0;
            @endphp

            @foreach($penerimaan->details as $index => $detail)

                @php
                    $totalQty += $detail->qty;
                    $grandTotal += $detail->jumlah;
                @endphp

                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->kode_barang }}</td>
                    <td>{{ $detail->nama_barang }}</td>
                    <td>{{ $detail->kategori }}</td>
                    <td class="center">{{ $detail->satuan }}</td>
                    <td>{{ $detail->lokasi }}</td>
                    <td class="center">{{ number_format($detail->qty) }}</td>
                    <td class="right">
                        {{ number_format($detail->harga, 0, ',', '.') }}
                    </td>
                    <td class="right">
                        {{ number_format($detail->jumlah, 0, ',', '.') }}
                    </td>
                </tr>

            @endforeach

            <tr>
                <th colspan="6" class="right">
                    TOTAL
                </th>

                <th class="center">
                    {{ number_format($totalQty) }}
                </th>

                <th></th>

                <th class="right">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </th>
            </tr>

        </tbody>

    </table>

    {{-- BUKTI PO --}}
    @if($penerimaan->bukti_po &&
        file_exists(public_path('storage/'.$penerimaan->bukti_po)))

        <div class="bukti">
            <strong>Bukti Purchase Order (PO):</strong>

            <br><br>

            <img src="{{ public_path('storage/'.$penerimaan->bukti_po) }}">
        </div>

    @endif

    <div class="footer">
        Dokumen ini dicetak melalui Aplikasi Inventaris ATK MDI.
        <br>
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>
</html>