<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IotSession extends Model
{
    use HasFactory;

    protected $table = 'iot_sessions';

    protected $fillable = [
        'kode_reklame',
        'status',
        'foto_uploaded',
        'lokasi_uploaded',
        'started_by',
        'started_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'foto_uploaded' => 'boolean',
        'lokasi_uploaded' => 'boolean',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];
}
