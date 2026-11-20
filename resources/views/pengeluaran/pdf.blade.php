<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pengeluaran Barang</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            position: relative;
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #222;
            padding: 3px 0;
            margin-bottom: 10px;
            min-height: 75px;
        }

        .header h2 {
            margin: 10px 0 5px;
            font-size: 18px;
            text-align: center;
        }

        .header p {
            margin: 3px 0;
            text-align: center;
        }

        .logo-pdf {
            position: absolute;
            left: 0;
            top: 5px;
            width: 85px;
            height: auto;
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
            padding: 7px 5px;
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

    {{-- HEADER --}}
    <div class="header">
        <h2>FORMULIR PENGELUARAN BARANG</h2>
        <p>INVENTARIS ALAT TULIS KANTOR (ATK)</p>
        <p>{{ $pengeluaran->branch->nama_cabang ?? '-' }}</p>

        <img src="{{ public_path('images/logo.png') }}"
             class="logo-pdf">
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h3>BUKTI PENGELUARAN BARANG</h3>
        <p>Nomor: {{ $pengeluaran->nomor_pengeluaran }}</p>
    </div>

    {{-- INFORMASI --}}
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
            <td>
                {{ $pengeluaran->user->user ?? $pengeluaran->user->name ?? '-' }}
            </td>
        </tr>
    </table>

    {{-- DETAIL BARANG --}}
    <table class="detail">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="13%">Kode Barang</th>
                <th width="21%">Nama Barang</th>
                <th width="10%">Kategori</th>
                <th width="8%">Satuan</th>
                <th width="7%">Lokasi</th>
                <th width="8%">Qty</th>
                <th width="14%">Harga Pokok</th>
                <th width="15%">Amount</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pengeluaran->details as $index => $detail)
                <tr>
                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $detail->kode_barang }}
                    </td>

                    <td>
                        {{ $detail->nama_barang }}
                    </td>

                    <td class="center">
                        {{ $detail->kategori ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $detail->satuan ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $detail->lokasi ?? '-' }}
                    </td>

                    <td class="right">
                        {{ number_format($detail->qty, 0, ',', '.') }}
                    </td>

                    <td class="right">
                        Rp {{ number_format($detail->harga_pokok ?? 0, 0, ',', '.') }}
                    </td>

                    <td class="right">
                        Rp {{ number_format($detail->jumlah_harga_pokok ?? (($detail->qty ?? 0) * ($detail->harga_pokok ?? 0)), 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">
                        Tidak ada detail barang.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- TOTAL --}}
        <tfoot>
            <tr>
                <th colspan="6" class="right">
                    Total Barang Keluar
                </th>

                <th class="right">
                    {{ number_format($pengeluaran->details->sum('qty'), 0, ',', '.') }}
                </th>

                <th class="right">
                    Total Harga
                </th>

                <th class="right">
                    Rp {{ number_format(
                        $pengeluaran->details->sum(function ($detail) {
                            return $detail->jumlah_harga_pokok
                                ?? (($detail->qty ?? 0) * ($detail->harga_pokok ?? 0));
                        }),
                        0, ',', '.'
                    ) }}
                </th>
            </tr>
        </tfoot>
    </table>

    {{-- KETERANGAN --}}
    <div class="keterangan">
        <strong>Keterangan:</strong><br>
        {{ $pengeluaran->keterangan ?? '-' }}
    </div>

    {{-- TANDA TANGAN --}}
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

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dicetak melalui Sistem Informasi Persediaan ATK.
        <br>
        Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}
    </div>

</body>
</html>
