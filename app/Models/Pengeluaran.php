<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\User;
use App\Models\DetailPengeluaran;

class Pengeluaran extends Model
{
    protected $table = 'pengeluarans';

    protected $fillable = [
        'branch_id',
        'user_id',
        'nomor_pengeluaran',
        'divisi',
        'nama_pengambil',
        'keterangan',
        'tanggal',
    ];

    // Relasi ke tabel cabang
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke detail pengeluaran
    public function details()
    {
        return $this->hasMany(
            DetailPengeluaran::class,
            'pengeluaran_id'
        );
    }
}
