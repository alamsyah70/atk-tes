<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Barang</title>
<style>
    body {
        font-family: sans-serif;
        font-size: 10px;
        color: #222;
    }

    .header-pdf {
        position: relative;
        width: 100%;
        height: 90px;
        text-align: center;
        border-bottom: 2px solid #333;
        margin-bottom: 15px;
    }

    .logo-pdf {
        position: absolute;
        left: 0;
        top: 0;
        width: 90px;
        height: auto;
    }

    .judul-pdf {
        text-align: center;
        margin: 0;
        padding-top: 20px;
        font-size: 18px;
    }

    .subjudul-pdf {
        text-align: center;
        margin-top: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 18px;
    }

    th, td {
        border: 1px solid #999;
        padding: 6px;
    }

    th {
        background: #eaf0f7;
    }

    .right {
        text-align: right;
    }
</style>
</head>
<body>
<div class="header-pdf">

    <img src="{{ public_path('images/logo.png') }}"
         class="logo-pdf">

    <h2 class="judul-pdf">
        LAPORAN PENERIMAAN BARANG
    </h2>

    <p class="subjudul-pdf">
        Riwayat transaksi barang masuk
    </p>

</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Cabang</th>
                <th>Supplier</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->nama_cabang ?? '-' }}</td>
                    <td>{{ $item->nama_supplier ?? '-' }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori ?? '-' }}</td>
                    <td class="right">{{ number_format($item->qty, 0, ',', '.') }}</td>
                    <td>{{ $item->satuan ?? '-' }}</td>
                    <td class="right">{{ number_format($item->harga ?? 0, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->jumlah ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" align="center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>