<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailStokOpname extends Model
{
    protected $table = 'detail_stok_opnames';

    protected $fillable = [
        'stok_opname_id',
        'barang_id',
        'kode_barang',
        'nama_barang',
        'stok_sistem',
        'stok_fisik',
        'selisih',
    ];

    public function stokOpname(): BelongsTo
    {
        return $this->belongsTo(
            StokOpname::class,
            'stok_opname_id'
        );
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
