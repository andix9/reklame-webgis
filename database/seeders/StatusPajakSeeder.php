<?php

namespace Database\Seeders;

use App\Models\StatusPajak;
use Illuminate\Database\Seeder;

class StatusPajakSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Lunas', 'Belum Lunas', 'Tidak Terdaftar'] as $status) {
            StatusPajak::updateOrCreate(
                ['nama_status' => $status],
                ['nama_status' => $status]
            );
        }
    }
}
