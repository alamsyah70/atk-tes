<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'branch_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'satuan',
        'lokasi',
        'average_perolehan',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function detailPenerimaans(): HasMany
    {
        return $this->hasMany(
            DetailPenerimaan::class,
            'barang_id'
        );
    }

    public function detailPengeluarans(): HasMany
    {
        return $this->hasMany(
            DetailPengeluaran::class,
            'barang_id'
        );
    }

    public function scopeCabang($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
