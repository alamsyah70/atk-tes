<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #222; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #999; padding: 6px; }
        th { background: #eaf0f7; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>LAPORAN PENERIMAAN BARANG</h2>
    <p>Riwayat transaksi barang masuk</p>

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