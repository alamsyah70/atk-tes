<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengeluaran extends Model
{
    protected $table = 'detail_pengeluarans';

    protected $fillable = [
        'pengeluaran_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'lokasi',
        'qty',
    ];

    public function pengeluaran()
    {
        return $this->belongsTo(
            Pengeluaran::class,
            'pengeluaran_id'
        );
    }
}
