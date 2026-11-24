<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemakaian Tahunan</title>

    <style>
        @page {
            margin: 25px 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #222;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #234b75;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 75px;
            height: auto;
        }

        .judul {
            text-align: center;
        }

        .judul h2 {
            margin: 0;
            font-size: 17px;
            color: #234b75;
        }

        .judul h3 {
            margin: 5px 0 0;
            font-size: 12px;
        }

        .informasi {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px 4px;
        }

        th {
            background: #dce6f1;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .total {
            background: #e8edf3;
            font-weight: bold;
        }

        .section-title {
            margin-top: 18px;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: bold;
            color: #234b75;
        }

        .naik {
            color: #c0392b;
            font-weight: bold;
        }

        .turun {
            color: #16834a;
            font-weight: bold;
        }

        .tetap {
            color: #555;
            font-weight: bold;
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
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="90">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" class="logo">
                    @endif
                </td>

                <td class="judul">
                    <h2>APLIKASI ATK MDI</h2>
                    <h3>LAPORAN PEMAKAIAN PER DIVISI TAHUNAN</h3>
                </td>

                <td width="90"></td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI --}}
    <div class="informasi">
        <strong>Tahun:</strong> {{ $tahun }}<br>
        <strong>Cabang:</strong> {{ $namaCabang }}<br>
        <strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}
    </div>

    {{-- TABEL PEMAKAIAN PER DIVISI --}}
    <div class="section-title">
        A. Rekap Pemakaian Per Divisi Setiap Bulan
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="25">No</th>
                <th rowspan="2" width="120">Divisi</th>
                <th colspan="12">Total Pemakaian Per Bulan (Rp)</th>
                <th rowspan="2" width="100">Grand Total</th>
            </tr>

            <tr>
                @foreach($namaBulan as $nama)
                    <th>{{ substr($nama, 0, 3) }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($divisiList as $index => $divisi)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>

                    <td>{{ $divisi }}</td>

                    @php
                        $totalDivisi = 0;
                    @endphp

                    @foreach($namaBulan as $bulan => $nama)
                        @php
                            $nilai = $dataTahunan[$divisi][$bulan]['nilai'] ?? 0;
                            $totalDivisi += $nilai;
                        @endphp

                        <td class="text-right">
                            {{ number_format($nilai, 0, ',', '.') }}
                        </td>
                    @endforeach

                    <td class="text-right total">
                        {{ number_format($totalDivisi, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center">
                        Tidak ada data pemakaian pada tahun {{ $tahun }}
                    </td>
                </tr>
            @endforelse

            @if($divisiList->count() > 0)
                <tr class="total">
                    <td colspan="2" class="text-center">
                        TOTAL
                    </td>

                    @foreach($namaBulan as $bulan => $nama)
                        <td class="text-right">
                            {{ number_format($totalBulanan[$bulan]['nilai'] ?? 0, 0, ',', '.') }}
                        </td>
                    @endforeach

                    <td class="text-right">
                        {{ number_format($grandTotalNilai, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- RINGKASAN BULANAN --}}
    <div class="section-title">
        B. Ringkasan Pemakaian dan Perubahan Bulanan
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Bulan</th>
                <th>Total Qty</th>
                <th>Total Pemakaian (Rp)</th>
                <th>Selisih (Rp)</th>
                <th>Perubahan (%)</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($namaBulan as $bulan => $nama)
                @php
                    $perubahan = $perubahanBulanan[$bulan] ?? [
                        'selisih' => 0,
                        'persentase' => 0,
                        'status' => 'Awal Tahun',
                    ];

                    $statusClass = '';

                    if ($perubahan['status'] === 'Naik') {
                        $statusClass = 'naik';
                    } elseif ($perubahan['status'] === 'Turun') {
                        $statusClass = 'turun';
                    } else {
                        $statusClass = 'tetap';
                    }
                @endphp

                <tr>
                    <td class="text-center">{{ $bulan }}</td>

                    <td>{{ $nama }}</td>

                    <td class="text-right">
                        {{ number_format($totalBulanan[$bulan]['qty'] ?? 0, 2, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($totalBulanan[$bulan]['nilai'] ?? 0, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($perubahan['selisih'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($perubahan['persentase'], 2, ',', '.') }}%
                    </td>

                    <td class="text-center {{ $statusClass }}">
                        {{ $perubahan['status'] }}
                    </td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="2" class="text-center">
                    TOTAL TAHUN {{ $tahun }}
                </td>

                <td class="text-right">
                    {{ number_format($grandTotalQty, 2, ',', '.') }}
                </td>

                <td class="text-right">
                    {{ number_format($grandTotalNilai, 0, ',', '.') }}
                </td>

                <td colspan="3" class="text-center">
                    -
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak melalui Aplikasi ATK MDI - Inventory Management
    </div>

</body>
</html>
