<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaan extends Model
{
    protected $fillable = [
        'penerimaan_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'lokasi',
        'qty',
        'harga',
        'jumlah',
    ];

    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class);
    }
}