<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // Semua role menggunakan guard web
    protected $guard_name = 'web';

    protected $fillable = [
        'user',
        'branch_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi user dengan cabang.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
