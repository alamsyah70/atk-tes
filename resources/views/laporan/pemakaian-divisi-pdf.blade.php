<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemakaian Per Divisi</title>

    <style>
        @page { margin: 25px; }
        body { font-family: sans-serif; font-size: 9px; color: #222; }
        .header {
            position: relative;
            height: 85px;
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 15px;
        }
        .logo { position: absolute; left: 0; top: 0; width: 80px; }
        h2 { margin: 0; padding-top: 18px; font-size: 17px; }
        .subtitle { margin-top: 5px; }
        .periode { margin: 12px 0; }
        h3 {
            margin: 18px 0 7px;
            padding: 7px;
            background: #eaf0f7;
            border-left: 4px solid #2463ad;
            font-size: 12px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #999; padding: 5px; }
        th { background: #edf2f8; text-align: center; }
        .right { text-align: right; }
        .center { text-align: center; }
        .subtotal { background: #f3f6fa; font-weight: bold; }
        .grand-total {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #555;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #666; }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo.png') }}" class="logo">
    <h2>LAPORAN PEMAKAIAN PER DIVISI</h2>
    <div class="subtitle">Rekapitulasi pemakaian barang berdasarkan kategori</div>
</div>

<div class="periode">
    <strong>Periode:</strong>
    {{ \Carbon\Carbon::createFromDate($filter['tahun'], $filter['bulan'], 1)->translatedFormat('F Y') }}
</div>

@forelse($groups as $divisi => $divisiItems)
    @php
        $kategoriGroups = $divisiItems->groupBy(function ($item) {
            return $item->kategori ?: 'Tanpa Kategori';
        });
    @endphp

    <h3>DIVISI: {{ $divisi }}</h3>

    @foreach($kategoriGroups as $kategori => $items)
        <strong>Kategori: {{ $kategori }}</strong>

        <table>
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th>Tanggal</th>
                    <th>No. Pengeluaran</th>
                    <th>Cabang</th>
                    <th>Pengambil</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga Pokok</th>
                    <th>Jumlah Harga Pokok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $item->nomor_pengeluaran ?? '-' }}</td>
                        <td>{{ $item->nama_cabang ?? '-' }}</td>
                        <td>{{ $item->nama_pengambil ?? '-' }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="right">{{ number_format($item->qty, 2, ',', '.') }}</td>
                        <td class="center">{{ $item->satuan ?? '-' }}</td>
                        <td class="right">Rp {{ number_format($item->harga_pokok ?? 0, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($item->jumlah_harga_pokok ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr class="subtotal">
                    <td colspan="7" class="right">TOTAL KATEGORI {{ strtoupper($kategori) }}</td>
                    <td class="right">{{ number_format($items->sum('qty'), 2, ',', '.') }}</td>
                    <td colspan="2"></td>
                    <td class="right">Rp {{ number_format($items->sum('jumlah_harga_pokok'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <table>
        <tr class="subtotal">
            <td width="70%" class="right">TOTAL DIVISI: {{ strtoupper($divisi) }}</td>
            <td class="right">{{ number_format($divisiItems->sum('qty'), 2, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($divisiItems->sum('jumlah_harga_pokok'), 0, ',', '.') }}</td>
        </tr>
    </table>

@empty
    <p class="center">Tidak ada data pemakaian pada periode ini.</p>
@endforelse

<div class="grand-total">
    GRAND TOTAL PEMAKAIAN:
    {{ number_format($totalQty, 2, ',', '.') }} unit/item
    &nbsp; | &nbsp;
    Rp {{ number_format($totalNilai, 0, ',', '.') }}
</div>

<div class="footer">
    Dicetak pada: {{ now()->format('d-m-Y H:i') }}
</div>

</body>
</html>