
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pengeluaran Barang</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 5px;
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
            padding: 5px 3px;
            vertical-align: top;
        }

        .detail {
            margin-top: 20px;
        }

        .detail th,
        .detail td {
            border: 1px solid #333;
            padding: 8px;
        }

        .detail th {
            background: #eee;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .keterangan {
            margin-top: 15px;
        }

        .ttd {
            margin-top: 45px;
            width: 100%;
        }

        .ttd td {
            text-align: center;
            width: 50%;
            padding: 5px;
        }

        .nama-ttd {
            padding-top: 65px !important;
            text-decoration: underline;
            font-weight: bold;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>FORMULIR PENGELUARAN BARANG</h2>
        <p>INVENTARIS ALAT TULIS KANTOR (ATK)</p>
        <p>{{ $pengeluaran->branch->nama_cabang ?? '-' }}</p>
    </div>

    <div class="judul">
        <h3>BUKTI PENGELUARAN BARANG</h3>
        <p>Nomor: {{ $pengeluaran->nomor_pengeluaran }}</p>
    </div>

    <table class="informasi">
        <tr>
            <td width="20%">Tanggal</td>
            <td width="3%">:</td>
            <td width="77%">
                {{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-m-Y') }}
            </td>
        </tr>

        <tr>
            <td>Divisi / Bagian</td>
            <td>:</td>
            <td>{{ $pengeluaran->divisi }}</td>
        </tr>

        <tr>
            <td>Nama Pengambil</td>
            <td>:</td>
            <td>{{ $pengeluaran->nama_pengambil }}</td>
        </tr>

        <tr>
            <td>Petugas Input</td>
            <td>:</td>
            <td>{{ $pengeluaran->user->user ?? $pengeluaran->user->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="17%">Kode Barang</th>
                <th width="30%">Nama Barang</th>
                <th width="16%">Kategori</th>
                <th width="10%">Satuan</th>
                <th width="10%">Lokasi</th>
                <th width="12%">Jumlah</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pengeluaran->details as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->kode_barang }}</td>
                    <td>{{ $detail->nama_barang }}</td>
                    <td>{{ $detail->kategori ?? '-' }}</td>
                    <td class="center">{{ $detail->satuan ?? '-' }}</td>
                    <td class="center">{{ $detail->lokasi ?? '-' }}</td>
                    <td class="right">{{ number_format($detail->qty, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">
                        Tidak ada detail barang.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <th colspan="6" class="right">Total Barang Keluar</th>
                <th class="right">
                    {{ number_format($pengeluaran->details->sum('qty'), 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
    </table>

    <div class="keterangan">
        <strong>Keterangan:</strong><br>
        {{ $pengeluaran->keterangan ?? '-' }}
    </div>

    <table class="ttd">
        <tr>
            <td>Accounting,</td>
            <td>Penerima Barang,</td>
        </tr>

        <tr>
            <td class="nama-ttd">
                (................................)
            </td>
            <td class="nama-ttd">
                ({{ $pengeluaran->nama_pengambil }})
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dicetak melalui Sistem Informasi Persediaan ATK.
        <br>
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div>

</body>
</html>
