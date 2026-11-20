<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class DetailPengeluaran extends Model
{
    protected $table = 'detail_pengeluarans';

    protected $fillable = [
        'pengeluaran_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'lokasi',
        'qty',
        'harga_pokok',
        'jumlah_harga_pokok',
    ];

    public function pengeluaran()
    {
        return $this->belongsTo(
            Pengeluaran::class,
            'pengeluaran_id'
        );
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
