<?php

namespace App\Http\Controllers;

use App\Models\IotSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IotSessionController extends Controller
{
    public function start(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return $this->unauthorizedResponse();
        }

        $data = $request->validate([
            'started_by' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $session = DB::transaction(function () use ($data) {
                IotSession::whereIn('status', ['open', 'photo_uploaded', 'location_uploaded'])
                    ->update([
                        'status' => 'cancelled',
                        'closed_at' => now(),
                        'notes' => 'Ditutup otomatis saat sesi baru dibuat',
                    ]);

                $kodeBaru = $this->generateNextKode();

                return IotSession::create([
                    'kode_reklame' => $kodeBaru,
                    'status' => 'open',
                    'foto_uploaded' => false,
                    'lokasi_uploaded' => false,
                    'started_by' => $data['started_by'] ?? 'esp32-cam',
                    'started_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Sesi baru berhasil dibuat.',
                'data' => [
                    'kode_reklame' => $session->kode_reklame,
                    'status' => $session->status,
                    'started_at' => optional($session->started_at)->toDateTimeString(),
                ],
            ], 201);
        } catch (\Throwable $e) {
            Log::error('IOT session start failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat sesi baru.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function current(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return $this->unauthorizedResponse();
        }

        try {
            $session = IotSession::whereIn('status', ['open', 'photo_uploaded', 'location_uploaded'])
                ->latest('id')
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada sesi aktif.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sesi aktif ditemukan.',
                'data' => [
                    'kode_reklame' => $session->kode_reklame,
                    'status' => $session->status,
                    'foto_uploaded' => $session->foto_uploaded,
                    'lokasi_uploaded' => $session->lokasi_uploaded,
                    'started_at' => optional($session->started_at)->toDateTimeString(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('IOT session current failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca sesi aktif.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function close(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return $this->unauthorizedResponse();
        }

        $data = $request->validate([
            'kode_reklame' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:completed,cancelled'],
        ]);

        try {
            $query = IotSession::whereIn('status', ['open', 'photo_uploaded', 'location_uploaded']);

            if (!empty($data['kode_reklame'])) {
                $query->where('kode_reklame', $data['kode_reklame']);
            }

            $session = $query->latest('id')->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi aktif tidak ditemukan.',
                ], 404);
            }

            $finalStatus = $data['status'] ?? (($session->foto_uploaded && $session->lokasi_uploaded) ? 'completed' : 'cancelled');

            $session->update([
                'status' => $finalStatus,
                'closed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sesi berhasil ditutup.',
                'data' => [
                    'kode_reklame' => $session->kode_reklame,
                    'status' => $session->status,
                    'closed_at' => optional($session->closed_at)->toDateTimeString(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('IOT session close failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup sesi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateNextKode(): string
    {
        $lastKode = IotSession::orderByDesc('id')->value('kode_reklame');

        if (!$lastKode) {
            return 'RKL-0001';
        }

        $angka = (int) preg_replace('/[^0-9]/', '', $lastKode);
        $angkaBaru = $angka + 1;

        return 'RKL-' . str_pad((string) $angkaBaru, 4, '0', STR_PAD_LEFT);
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