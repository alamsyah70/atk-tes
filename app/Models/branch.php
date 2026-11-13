<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'alamat',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
