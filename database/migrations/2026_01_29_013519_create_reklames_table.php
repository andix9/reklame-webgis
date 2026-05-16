<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_pajak', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 50)->unique();
            $table->timestamps();
        });

        Schema::create('reklames', function (Blueprint $table) {
            $table->id();
            $table->string('kode_reklame')->unique();
            $table->string('nama_reklame');
            $table->string('nama_pemilik')->nullable();
            $table->string('jenis_reklame')->nullable();
            $table->string('ukuran')->nullable();
            $table->foreignId('status_pajak_id')->constrained('status_pajak')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reklame_id')->constrained('reklames')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('alamat')->nullable();
            $table->string('sumber_data')->default('manual');
            $table->timestamp('waktu_kirim')->nullable();
            $table->timestamps();
        });

        Schema::create('dokumentasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reklame_id')->constrained('reklames')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('foto');
            $table->string('sumber_data')->default('manual');
            $table->timestamp('tanggal_upload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi');
        Schema::dropIfExists('lokasi');
        Schema::dropIfExists('reklames');
        Schema::dropIfExists('status_pajak');
    }
};
