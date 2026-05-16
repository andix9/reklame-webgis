<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'reklame_id',
        'latitude',
        'longitude',
        'alamat',
        'sumber_data',
        'waktu_kirim',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function reklame(): BelongsTo
    {
        return $this->belongsTo(Reklame::class, 'reklame_id');
    }
}
