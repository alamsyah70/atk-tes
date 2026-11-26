<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokOpname extends Model
{
    protected $table = 'stok_opnames';

    protected $fillable = [
        'nomor_opname',
        'branch_id',
        'tanggal',
        'keterangan',
        'status',
        'snapshot_at',
        'user_id',
        'finalized_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'snapshot_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            DetailStokOpname::class,
            'stok_opname_id'
        );
    }
}
