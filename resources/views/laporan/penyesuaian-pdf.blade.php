<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penyesuaian Stok</title>

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
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px 5px;
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

        .tambah {
            color: #198754;
            font-weight: bold;
        }

        .kurang {
            color: #dc3545;
            font-weight: bold;
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

        .total-row {
            margin-top: 7px;
            font-size: 10px;
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

    {{-- HEADER --}}
    <div class="header-pdf">

        <img src="{{ public_path('images/logo.png') }}"
             class="logo-pdf">

        <h2 class="judul-pdf">
            LAPORAN PENYESUAIAN STOK
        </h2>

        <p class="subjudul-pdf">
            Riwayat penambahan dan pengurangan stok barang
        </p>

    </div>

    {{-- PERIODE --}}
    <div class="periode">
        <strong>Periode:</strong>

        {{ $filter->tanggal_dari
            ? \Carbon\Carbon::parse($filter->tanggal_dari)->format('d-m-Y')
            : 'Semua tanggal' }}

        s/d

        {{ $filter->tanggal_sampai
            ? \Carbon\Carbon::parse($filter->tanggal_sampai)->format('d-m-Y')
            : 'Semua tanggal' }}

        @if($isGlobalUser && $filter->filled('branch_id'))
            <br>
            <strong>Cabang:</strong>
            {{ optional(\App\Models\Branch::find($filter->branch_id))->nama_cabang ?? '-' }}
        @endif

        @if($filter->filled('q'))
            <br>
            <strong>Pencarian:</strong> {{ $filter->q }}
        @endif
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Penyesuaian</th>

                @if($isGlobalUser)
                    <th>Cabang</th>
                @endif

                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Qty Koreksi</th>
                <th>Alasan Penyesuaian</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>

                    <td class="center">
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                    </td>

                    <td>
                        {{ $item->nomor_penyesuaian ?? '-' }}
                    </td>

                    @if($isGlobalUser)
                        <td>
                            {{ $item->nama_cabang ?? '-' }}
                        </td>
                    @endif

                    <td>
                        {{ $item->kode_barang ?? '-' }}
                    </td>

                    <td>
                        {{ $item->nama_barang ?? '-' }}
                    </td>

                    <td class="right">
                        @if($item->qty_koreksi > 0)
                            <span class="tambah">
                                +{{ number_format($item->qty_koreksi, 0, ',', '.') }}
                            </span>
                        @elseif($item->qty_koreksi < 0)
                            <span class="kurang">
                                {{ number_format($item->qty_koreksi, 0, ',', '.') }}
                            </span>
                        @else
                            0
                        @endif
                    </td>

                    <td>
                        {{ $item->alasan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isGlobalUser ? 8 : 7 }}"
                        class="center">
                        Tidak ada data penyesuaian stok.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- RINGKASAN --}}
    <div class="total-box">

        <div class="total-label">
            RINGKASAN PENYESUAIAN STOK
        </div>

        <div class="total-row tambah">
            Total Penambahan:
            +{{ number_format($ringkasan->total_penambahan ?? 0, 0, ',', '.') }}
        </div>

        <div class="total-row kurang">
            Total Pengurangan:
            -{{ number_format($ringkasan->total_pengurangan ?? 0, 0, ',', '.') }}
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>
</html>
