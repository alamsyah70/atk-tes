<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Pemeriksaan Fisik</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #222;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
        }

        .info td {
            padding: 4px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        .data th,
        .data td {
            border: 1px solid #555;
            padding: 7px;
        }

        .data th {
            text-align: center;
            background: #eee;
        }

        .center {
            text-align: center;
        }

        .blank {
            height: 28px;
        }

        .signatures {
            width: 100%;
            margin-top: 35px;
            text-align: center;
        }

        .signatures td {
            width: 33.33%;
            height: 75px;
            vertical-align: top;
        }

        .no-print {
            margin-bottom: 15px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">Cetak Lembar Pemeriksaan</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <h2>LEMBAR PEMERIKSAAN FISIK BARANG</h2>
    <div class="subtitle">STOK OPNAME PERSEDIAAN ATK</div>

    <table class="info">
        <tr>
            <td width="15%">Nomor Opname</td>
            <td width="35%">: {{ $stokOpname->nomor_opname }}</td>
            <td width="15%">Cabang</td>
            <td>: {{ $stokOpname->branch->nama_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ $stokOpname->tanggal->format('d-m-Y') }}</td>
            <td>Petugas</td>
            <td>: {{ $stokOpname->user->user ?? '-' }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="3">: {{ $stokOpname->keterangan ?: '-' }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Kode Barang</th>
                <th>Nama Barang</th>
                <th width="12%">Satuan</th>
                <th width="15%">Lokasi</th>
                <th width="15%">Stok Fisik</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @foreach($details as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->kode_barang }}</td>
                    <td>{{ $detail->nama_barang }}</td>
                    <td class="center">
                        {{ $detail->barang->satuan ?? '-' }}
                    </td>
                    <td>{{ $detail->barang->lokasi ?? '-' }}</td>
                    <td class="blank"></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                Petugas Pemeriksa,
                <br><br><br><br>
                (................................)
            </td>
            <td>
                Mengetahui,
                <br><br><br><br>
                (................................)
            </td>
            <td>
                Penanggung Jawab Gudang,
                <br><br><br><br>
                (................................)
            </td>
        </tr>
    </table>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>

</body>
</html>