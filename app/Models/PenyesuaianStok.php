<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyesuaianStok extends Model
{
    protected $fillable = [
        'branch_id',
        'user_id',
        'nomor_penyesuaian',
        'kode_barang',
        'nama_barang',
        'qty_koreksi',
        'alasan',
        'tanggal',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}