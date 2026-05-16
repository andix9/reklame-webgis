<?php

namespace App\Http\Controllers;

use App\Models\Reklame;
use App\Models\StatusPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReklameController extends Controller
{
    public function index()
    {
        $reklames = Reklame::with(['statusPajak', 'lokasi', 'dokumentasi'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => $this->transformReklame($item));

        return view('reklames.index', compact('reklames'));
    }

    public function list()
    {
        $reklames = Reklame::with(['statusPajak', 'lokasi', 'dokumentasi'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => $this->transformReklame($item));

        return view('reklames.list', compact('reklames'));
    }

    public function create()
    {
        return view('reklames.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'status_pajak' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Tidak Terdaftar'])],
            'alamat' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'kode_reklame' => ['nullable', 'string', 'max:255'],
            'nama_reklame' => ['nullable', 'string', 'max:255'],
            'jenis_reklame' => ['nullable', 'string', 'max:255'],
            'ukuran' => ['nullable', 'string', 'max:255'],
            'date_exp' => ['nullable', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $status = $this->getOrCreateStatus($data['status_pajak']);

            $reklame = Reklame::create([
                'kode_reklame' => $data['kode_reklame'] ?? $this->generateKodeReklame(),
                'nama_reklame' => $data['nama_reklame'] ?? null,
                'nama_pemilik' => $data['nama_pemilik'],
                'jenis_reklame' => $data['jenis_reklame'] ?? null,
                'ukuran' => $data['ukuran'] ?? null,
                'date_exp' => $data['date_exp'] ?? null,
                'status_pajak_id' => $status->id,
                'user_id' => auth()->id(),
            ]);

            $reklame->lokasi()->create([
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'alamat' => $data['alamat'],
                'sumber_data' => 'manual',
                'waktu_kirim' => now(),
            ]);

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('fotos', 'public');

                $reklame->dokumentasi()->create([
                    'foto' => $fotoPath,
                    'sumber_data' => 'manual',
                    'tanggal_upload' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('reklames.list')
                ->with('success', 'Data reklame berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menambah data reklame', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Data gagal disimpan. Periksa format foto, koordinat, dan koneksi database.');
        }
    }

    public function show(string $id)
    {
        $reklame = Reklame::with(['statusPajak', 'lokasi', 'dokumentasi'])->findOrFail($id);

        return response()->json($this->transformReklame($reklame));
    }

    public function edit(string $id)
    {
        $reklame = Reklame::with(['statusPajak', 'lokasi', 'dokumentasi'])->findOrFail($id);
        $reklame = $this->transformReklame($reklame);

        return view('reklames.edit', compact('reklame'));
    }

    public function update(Request $request, string $id)
    {
        $reklame = Reklame::with(['statusPajak', 'lokasi', 'dokumentasi'])->findOrFail($id);

        $data = $request->validate([
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'status_pajak' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Tidak Terdaftar'])],
            'alamat' => ['required', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'kode_reklame' => ['nullable', 'string', 'max:255'],
            'nama_reklame' => ['nullable', 'string', 'max:255'],
            'jenis_reklame' => ['nullable', 'string', 'max:255'],
            'ukuran' => ['nullable', 'string', 'max:255'],
            'date_exp' => ['nullable', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $status = $this->getOrCreateStatus($data['status_pajak']);

            $reklame->update([
                'kode_reklame' => $data['kode_reklame'] ?? $reklame->kode_reklame,
                'nama_reklame' => $data['nama_reklame'] ?? $reklame->nama_reklame,
                'nama_pemilik' => $data['nama_pemilik'],
                'jenis_reklame' => $data['jenis_reklame'] ?? $reklame->jenis_reklame,
                'ukuran' => $data['ukuran'] ?? $reklame->ukuran,
                'date_exp' => $data['date_exp'] ?? null,
                'status_pajak_id' => $status->id,
                'user_id' => auth()->id(),
            ]);

            $lokasiTerakhir = $reklame->lokasi()->latest('id')->first();

            if ($lokasiTerakhir) {
                // Koordinat dikunci pada halaman edit.
                // Update manual hanya memperbarui alamat, bukan latitude dan longitude.
                // Latitude dan longitude tetap mengikuti data GPS/IoT terakhir yang tersimpan.
                $lokasiTerakhir->update([
                    'alamat' => $data['alamat'],
                    'sumber_data' => $lokasiTerakhir->sumber_data ?? 'gps',
                    'waktu_kirim' => $lokasiTerakhir->waktu_kirim ?? now(),
                ]);
            } else {
                // Kondisi cadangan jika data lokasi belum ada sama sekali.
                $reklame->lokasi()->create([
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'alamat' => $data['alamat'],
                    'sumber_data' => 'manual',
                    'waktu_kirim' => now(),
                ]);
            }

            if ($request->hasFile('foto')) {
                $dokumentasiTerakhir = $reklame->dokumentasi()->latest('id')->first();

                if ($dokumentasiTerakhir && $dokumentasiTerakhir->foto && Storage::disk('public')->exists($dokumentasiTerakhir->foto)) {
                    Storage::disk('public')->delete($dokumentasiTerakhir->foto);
                }

                $fotoPath = $request->file('foto')->store('fotos', 'public');

                if ($dokumentasiTerakhir) {
                    $dokumentasiTerakhir->update([
                        'foto' => $fotoPath,
                        'sumber_data' => 'manual',
                        'tanggal_upload' => now(),
                    ]);
                } else {
                    $reklame->dokumentasi()->create([
                        'foto' => $fotoPath,
                        'sumber_data' => 'manual',
                        'tanggal_upload' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('reklames.list')
                ->with('success', 'Data reklame berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal memperbarui data reklame', [
                'message' => $e->getMessage(),
                'id' => $id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Data gagal diperbarui. Periksa format foto, koordinat, dan koneksi database.');
        }
    }

    public function destroy(string $id)
    {
        $reklame = Reklame::with(['dokumentasi'])->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($reklame->dokumentasi as $dok) {
                if ($dok->foto && Storage::disk('public')->exists($dok->foto)) {
                    Storage::disk('public')->delete($dok->foto);
                }
            }

            $reklame->delete();

            DB::commit();

            return redirect()->route('reklames.list')
                ->with('success', 'Data reklame berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menghapus data reklame', [
                'message' => $e->getMessage(),
                'id' => $id,
            ]);

            return back()->with('error', 'Data gagal dihapus.');
        }
    }

    public function apiStore(Request $request)
    {
        $apiKey = $request->header('X-API-KEY') ?: $request->input('api_key');
        $expectedApiKey = env('ESP32_API_KEY', 'rahasia-skripsi-123');

        if ($apiKey !== $expectedApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API Key salah.',
            ], 401);
        }

        $data = $request->validate([
            'kode_reklame' => ['required', 'string', 'max:255'],
            'nama_pemilik' => ['nullable', 'string', 'max:255'],
            'nama_reklame' => ['nullable', 'string', 'max:255'],
            'jenis_reklame' => ['nullable', 'string', 'max:255'],
            'ukuran' => ['nullable', 'string', 'max:255'],
            'date_exp' => ['nullable', 'date'],
            'status_pajak' => ['nullable', Rule::in(['Lunas', 'Belum Lunas', 'Tidak Terdaftar'])],
            'alamat' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sumber_data' => ['nullable', 'string', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            $status = $this->getOrCreateStatus($data['status_pajak'] ?? 'Belum Lunas');
            $sumberData = $data['sumber_data'] ?? 'iot';

            $reklame = Reklame::firstOrCreate(
                ['kode_reklame' => $data['kode_reklame']],
                [
                    'nama_reklame' => $data['nama_reklame'] ?? null,
                    'nama_pemilik' => $data['nama_pemilik'] ?? 'ESP32 Upload',
                    'jenis_reklame' => $data['jenis_reklame'] ?? null,
                    'ukuran' => $data['ukuran'] ?? null,
                    'date_exp' => $data['date_exp'] ?? null,
                    'status_pajak_id' => $status->id,
                    'user_id' => null,
                ]
            );

            $reklame->update([
                'nama_reklame' => $data['nama_reklame'] ?? $reklame->nama_reklame,
                'nama_pemilik' => $data['nama_pemilik'] ?? $reklame->nama_pemilik,
                'jenis_reklame' => $data['jenis_reklame'] ?? $reklame->jenis_reklame,
                'ukuran' => $data['ukuran'] ?? $reklame->ukuran,
                'date_exp' => $data['date_exp'] ?? $reklame->date_exp,
                'status_pajak_id' => $status->id,
            ]);

            if (!is_null($data['latitude'] ?? null) && !is_null($data['longitude'] ?? null)) {
                $reklame->lokasi()->create([
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'alamat' => $data['alamat'] ?? 'Lokasi dari perangkat IoT',
                    'sumber_data' => $sumberData,
                    'waktu_kirim' => now(),
                ]);
            }

            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('fotos', 'public');

                $reklame->dokumentasi()->create([
                    'foto' => $fotoPath,
                    'sumber_data' => $sumberData,
                    'tanggal_upload' => now(),
                ]);
            }

            DB::commit();

            $reklame->load(['statusPajak', 'lokasi', 'dokumentasi']);

            return response()->json([
                'success' => true,
                'message' => 'Data reklame berhasil diterima.',
                'data' => $this->transformReklame($reklame),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menerima data dari perangkat IoT', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data gagal diproses di server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function transformReklame(Reklame $reklame): object
    {
        $lokasi = $reklame->lokasi->sortByDesc('id')->first();
        $dokumentasi = $reklame->dokumentasi->sortByDesc('id')->first();

        return (object) [
            'id' => $reklame->id,
            'kode_reklame' => $reklame->kode_reklame,
            'nama_reklame' => $reklame->nama_reklame,
            'nama_pemilik' => $reklame->nama_pemilik,
            'jenis_reklame' => $reklame->jenis_reklame,
            'ukuran' => $reklame->ukuran,
            'date_exp' => $reklame->date_exp
                ? $reklame->date_exp->format('Y-m-d')
                : null,
            'status_pajak' => optional($reklame->statusPajak)->nama_status,
            'alamat' => optional($lokasi)->alamat,
            'latitude' => optional($lokasi)->latitude,
            'longitude' => optional($lokasi)->longitude,
            'foto' => optional($dokumentasi)->foto,
            'created_at' => $reklame->created_at,
            'updated_at' => $reklame->updated_at,
        ];
    }

    private function getOrCreateStatus(string $namaStatus): StatusPajak
    {
        return StatusPajak::firstOrCreate([
            'nama_status' => $namaStatus,
        ]);
    }

    private function generateKodeReklame(): string
    {
        do {
            $kode = 'RKL-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        } while (Reklame::where('kode_reklame', $kode)->exists());

        return $kode;
    }
}
