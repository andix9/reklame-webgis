<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak dipakai lagi.
        // Field alamat dan foto dipindahkan ke tabel lokasi dan dokumentasi
        // agar sesuai dengan rancangan database pada skripsi.
    }

    public function down(): void
    {
        // No-op.
    }
};
