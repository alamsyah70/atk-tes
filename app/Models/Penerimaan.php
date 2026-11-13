<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $fillable = [
        'supplier_id',
        'branch_id',
        'tanggal',
        'keterangan',
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPenerimaan::class);
    }
}
