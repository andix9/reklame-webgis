<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reklames', 'date_exp')) {
            Schema::table('reklames', function (Blueprint $table) {
                $table->date('date_exp')->nullable()->after('ukuran');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reklames', 'date_exp')) {
            Schema::table('reklames', function (Blueprint $table) {
                $table->dropColumn('date_exp');
            });
        }
    }
};
