<?php

namespace App\Http\Controllers;

use App\Models\Dokumentasi;
use App\Models\IotSession;
use App\Models\Lokasi;
use App\Models\Reklame;
use App\Models\StatusPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IotController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return $this->unauthorizedResponse();
        }

        $data = $request->validate([
            'kode_reklame' => ['required', 'string', 'max:255'],
            'nama_reklame' => ['nullable', 'string', 'max:255'],
            'nama_pemilik' => ['nullable', 'string', 'max:255'],
            'jenis_reklame' => ['nullable', 'string', 'max:255'],
            'ukuran' => ['nullable', 'string', 'max:255'],
            'status_pajak' => ['nullable', Rule::in(['Lunas', 'Belum Lunas', 'Tidak Terdaftar'])],
            'foto' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sumber_data' => ['nullable', 'string', 'max:100'],
            'captured_at' => ['nullable', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $reklame = $this->resolveReklame($data);
            $fotoPath = $request->file('foto')->store('fotos', 'public');

            $dokumentasi = Dokumentasi::create([
                'reklame_id' => $reklame->id,
                'foto' => $fotoPath,
                'sumber_data' => $data['sumber_data'] ?? 'esp32-cam',
                'tanggal_upload' => $data['captured_at'] ?? now(),
            ]);

            $this->markSessionPhotoUploaded($reklame->kode_reklame);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Foto reklame berhasil diterima.',
                'data' => [
                    'reklame_id' => $reklame->id,
                    'kode_reklame' => $reklame->kode_reklame,
                    'dokumentasi_id' => $dokumentasi->id,
                    'foto' => $dokumentasi->foto,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('IOT photo upload failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload foto gagal diproses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadLocation(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return $this->unauthorizedResponse();
        }

        $data = $request->validate([
            'kode_reklame' => ['required', 'string', 'max:255'],
            'nama_reklame' => ['nullable', 'string', 'max:255'],
            'nama_pemilik' => ['nullable', 'string', 'max:255'],
            'jenis_reklame' => ['nullable', 'string', 'max:255'],
            'ukuran' => ['nullable', 'string', 'max:255'],
            'status_pajak' => ['nullable', Rule::in(['Lunas', 'Belum Lunas', 'Tidak Terdaftar'])],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'alamat' => ['nullable', 'string'],
            'sumber_data' => ['nullable', 'string', 'max:100'],
            'sent_at' => ['nullable', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $reklame = $this->resolveReklame($data);

            $lokasi = Lokasi::create([
                'reklame_id' => $reklame->id,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'alamat' => $data['alamat'] ?? null,
                'sumber_data' => $data['sumber_data'] ?? 'esp32-gps',
                'waktu_kirim' => $data['sent_at'] ?? now(),
            ]);

            $this->markSessionLocationUploaded($reklame->kode_reklame);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Koordinat reklame berhasil diterima.',
                'data' => [
                    'reklame_id' => $reklame->id,
                    'kode_reklame' => $reklame->kode_reklame,
                    'lokasi_id' => $lokasi->id,
                    'latitude' => $lokasi->latitude,
                    'longitude' => $lokasi->longitude,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('IOT location upload failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload koordinat gagal diproses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolveReklame(array $data): Reklame
    {
        $status = StatusPajak::firstOrCreate([
            'nama_status' => $data['status_pajak'] ?? 'Tidak Terdaftar',
        ]);

        $reklame = Reklame::firstOrCreate(
            ['kode_reklame' => $data['kode_reklame']],
            [
                'nama_reklame' => $data['nama_reklame'] ?? null,
                'nama_pemilik' => $data['nama_pemilik'] ?? null,
                'jenis_reklame' => $data['jenis_reklame'] ?? null,
                'ukuran' => $data['ukuran'] ?? null,
                'status_pajak_id' => $status->id,
                'user_id' => null,
            ]
        );

        $updateData = [
            'status_pajak_id' => $status->id,
        ];

        if (!empty($data['nama_reklame'])) {
            $updateData['nama_reklame'] = $data['nama_reklame'];
        }

        if (!empty($data['nama_pemilik'])) {
            $updateData['nama_pemilik'] = $data['nama_pemilik'];
        }

        if (!empty($data['jenis_reklame'])) {
            $updateData['jenis_reklame'] = $data['jenis_reklame'];
        }

        if (!empty($data['ukuran'])) {
            $updateData['ukuran'] = $data['ukuran'];
        }

        $reklame->update($updateData);

        return $reklame;
    }

    private function markSessionPhotoUploaded(string $kodeReklame): void
    {
        $session = IotSession::where('kode_reklame', $kodeReklame)
            ->latest('id')
            ->first();

        if (!$session) {
            return;
        }

        $session->foto_uploaded = true;

        if ($session->lokasi_uploaded) {
            $session->status = 'completed';
            $session->closed_at = now();
        } else {
            $session->status = 'photo_uploaded';
        }

        $session->save();
    }

    private function markSessionLocationUploaded(string $kodeReklame): void
    {
        $session = IotSession::where('kode_reklame', $kodeReklame)
            ->latest('id')
            ->first();

        if (!$session) {
            return;
        }

        $session->lokasi_uploaded = true;

        if ($session->foto_uploaded) {
            $session->status = 'completed';
            $session->closed_at = now();
        } else {
            $session->status = 'location_uploaded';
        }

        $session->save();
    }

    private function isAuthorized(Request $request): bool
    {
        $apiKey = $request->header('X-API-KEY') ?: $request->input('api_key');
        $expectedApiKey = env('ESP32_API_KEY', 'rahasia-skripsi-123');

        return !empty($apiKey) && hash_equals($expectedApiKey, $apiKey);
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized: API Key salah atau tidak dikirim.',
        ], 401);
    }
}
