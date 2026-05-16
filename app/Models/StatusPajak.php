<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusPajak extends Model
{
    use HasFactory;

    protected $table = 'status_pajak';

    protected $fillable = [
        'nama_status',
    ];

    public function reklames(): HasMany
    {
        return $this->hasMany(Reklame::class, 'status_pajak_id');
    }
}
