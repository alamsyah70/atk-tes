<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengeluaran Barang</title>

<style>
        @page {
            margin: 25px 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
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
            width: 85px;
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
            font-size: 10px;
        }

        .periode {
            margin-top: 12px;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px 4px;
        }

        th {
            background: #eaf0f7;
            text-align: center;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .center {
            text-align: center;
        }

        .total-box {
            width: 310px;
            margin-top: 15px;
            margin-left: auto;
            border: 1px solid #777;
            padding: 10px;
        }

        .total-label {
            font-size: 10px;
            font-weight: bold;
        }

        .total-value {
            font-size: 15px;
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            margin-top: 20px;
            font-size: 8px;
            text-align: right;
            color: #666;
        }
</style>
</head>

<body>

    <div class="header-pdf">

        <img src="{{ public_path('images/logo.png') }}"
             class="logo-pdf">

        <h2 class="judul-pdf">
            LAPORAN PENGELUARAN BARANG
        </h2>

        <p class="subjudul-pdf">
            Riwayat transaksi barang keluar
        </p>

    </div>

    <div class="periode">
        <strong>Periode:</strong>

        {{ $filter->tanggal_dari
            ? \Carbon\Carbon::parse($filter->tanggal_dari)->format('d-m-Y')
            : 'Semua tanggal' }}

        s/d

        {{ $filter->tanggal_sampai
            ? \Carbon\Carbon::parse($filter->tanggal_sampai)->format('d-m-Y')
            : 'Semua tanggal' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Pengeluaran</th>
                <th>Cabang</th>
                <th>Nama Pengambil</th>
                <th>Divisi</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Pokok</th>
                <th>Jumlah Harga Pokok</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>

                    <td class="center">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                    </td>

                    <td>{{ $item->nomor_pengeluaran ?? '-' }}</td>
                    <td>{{ $item->nama_cabang ?? '-' }}</td>
                    <td>{{ $item->nama_pengambil ?? '-' }}</td>
                    <td>{{ $item->divisi ?? '-' }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori ?? '-' }}</td>

                    <td class="right">
                        {{ number_format($item->qty, 0, ',', '.') }}
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
            @empty
                <tr>
                    <td colspan="13" class="center">
                        Tidak ada data pengeluaran.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-label">
            TOTAL HARGA POKOK PENGELUARAN
        </div>

        <div class="total-value">
            Rp {{ number_format($totalHargaPokok ?? 0, 0, ',', '.') }}
        </div>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>
</html>