<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reklame extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_reklame',
        'nama_reklame',
        'nama_pemilik',
        'jenis_reklame',
        'ukuran',
        'date_exp',
        'status_pajak_id',
        'user_id',
    ];

    protected $casts = [
    'date_exp' => 'date',
    ];

    public function statusPajak(): BelongsTo
    {
        return $this->belongsTo(StatusPajak::class, 'status_pajak_id');
    }

    public function lokasi(): HasMany
    {
        return $this->hasMany(Lokasi::class, 'reklame_id');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(Dokumentasi::class, 'reklame_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
