<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $fillable = [
        'nomor_penerimaan',
        'supplier_id',
        'branch_id',
        'tanggal',
        'keterangan',
        'bukti_po',
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function details()
    {
        return $this->hasMany(DetailPenerimaan::class, 'penerimaan_id');
    }
}
