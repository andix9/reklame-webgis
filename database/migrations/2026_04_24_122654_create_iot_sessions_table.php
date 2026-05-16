<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('kode_reklame')->unique();
            $table->enum('status', [
                'open',
                'photo_uploaded',
                'location_uploaded',
                'completed',
                'cancelled'
            ])->default('open');

            $table->boolean('foto_uploaded')->default(false);
            $table->boolean('lokasi_uploaded')->default(false);

            $table->string('started_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iot_sessions');
    }
};
