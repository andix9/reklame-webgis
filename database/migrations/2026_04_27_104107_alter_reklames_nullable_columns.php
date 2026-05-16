<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE reklames
            MODIFY nama_reklame VARCHAR(255) NULL,
            MODIFY nama_pemilik VARCHAR(255) NULL,
            MODIFY jenis_reklame VARCHAR(255) NULL,
            MODIFY ukuran VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reklames
            MODIFY nama_reklame VARCHAR(255) NOT NULL,
            MODIFY nama_pemilik VARCHAR(255) NOT NULL,
            MODIFY jenis_reklame VARCHAR(255) NOT NULL,
            MODIFY ukuran VARCHAR(255) NOT NULL");
    }
};
