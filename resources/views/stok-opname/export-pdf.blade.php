<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Stok Opname</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #243247;
        }

        h2 {
            text-align: center;
            margin: 0 0 5px;
            color: #183b65;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 5px;
            vertical-align: top;
        }

        .data th {
            background: #eaf0f7;
            font-weight: bold;
            text-align: center;
        }

        .data th,
        .data td {
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
        }

        .data td {
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .summary {
            margin: 15px 0;
        }

        .summary td {
            border: 1px solid #dbe3ec;
            padding: 8px;
            text-align: center;
        }

        .summary strong {
            display: block;
            font-size: 15px;
            margin-top: 4px;
        }

        .positive {
            color: #15803d;
            font-weight: bold;
        }

        .negative {
            color: #dc2626;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            color: #64748b;
        }
    </style>
</head>
<body>

    <h2>HASIL STOK OPNAME</h2>
    <div class="subtitle">Laporan Pemeriksaan Persediaan Barang</div>

    <table class="info">
        <tr>
            <td width="17%">Nomor Opname</td>
            <td width="33%">: {{ $stokOpname->nomor_opname }}</td>
            <td width="17%">Cabang</td>
            <td>: {{ $stokOpname->branch->nama_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Opname</td>
            <td>: {{ $stokOpname->tanggal->format('d-m-Y') }}</td>
            <td>Petugas</td>
            <td>: {{ $stokOpname->user->user ?? '-' }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td colspan="3">: {{ $stokOpname->keterangan ?: '-' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td colspan="3">
                :
                {{ $stokOpname->dibatalkan_at ? 'Dibatalkan' : ucfirst($stokOpname->status) }}
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                Total Barang
                <strong>{{ number_format($totalBarang) }}</strong>
            </td>
            <td>
                Sesuai
                <strong>{{ number_format($totalSesuai) }}</strong>
            </td>
            <td>
                Barang Lebih
                <strong>{{ number_format($totalLebih) }}</strong>
            </td>
            <td>
                Barang Kurang
                <strong>{{ number_format($totalKurang) }}</strong>
            </td>
            <td>
                Total Barang Selisih
                <strong>{{ number_format($totalSelisih) }}</strong>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="15%">Kode Barang</th>
                <th>Nama Barang</th>
                <th width="13%">Stok Sistem</th>
                <th width="13%">Stok Fisik</th>
                <th width="13%">Selisih</th>
                <th width="14%">Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @foreach($details as $index => $detail)
                @php
                    $selisih = (int) $detail->selisih;
                @endphp

                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->kode_barang }}</td>
                    <td>{{ $detail->nama_barang }}</td>
                    <td class="right">
                        {{ number_format($detail->stok_sistem) }}
                    </td>
                    <td class="right">
                        {{ $detail->stok_fisik === null
                            ? 'Belum dihitung'
                            : number_format($detail->stok_fisik) }}
                    </td>
                    <td class="right">
                        @if($detail->selisih === null)
                            -
                        @elseif($selisih > 0)
                            <span class="positive">+{{ number_format($selisih) }}</span>
                        @elseif($selisih < 0)
                            <span class="negative">{{ number_format($selisih) }}</span>
                        @else
                            0
                        @endif
                    </td>
                    <td class="center">
                        @if($detail->stok_fisik === null)
                            Belum dihitung
                        @elseif($selisih === 0)
                            Sesuai
                        @else
                            Selisih
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($stokOpname->dibatalkan_at)
        <p>
            <strong>Alasan Pembatalan:</strong>
            {{ $stokOpname->alasan_pembatalan }}
        </p>
    @endif

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>
</html>