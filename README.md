# Reklame WebGIS Laravel

Proyek ini adalah aplikasi Web GIS untuk monitoring data reklame. Sistem ini mendukung:
- login admin
- manajemen data reklame
- upload foto reklame
- penyimpanan koordinat latitude dan longitude
- tampilan peta berbasis OpenStreetMap dan Leaflet
- endpoint API untuk upload data dari ESP32-CAM

## Akun admin default
- Email: `admin@reklame.com`
- Password: `admin123`

## Endpoint API ESP32
- `GET /api/ping`
- `POST /api/esp32/upload`

Header atau parameter API key:
- Header: `X-API-KEY: rahasia-skripsi-123`
- atau field: `api_key=rahasia-skripsi-123`

Field upload minimum:
- `latitude`
- `longitude`
- `foto`

Field opsional:
- `nama_pemilik`
- `status_pajak`
- `alamat`

## Cara menjalankan di XAMPP
1. Ekstrak proyek ke folder kerja Anda.
2. Pastikan Apache dan MySQL di XAMPP aktif.
3. Buat database baru di phpMyAdmin dengan nama `reklame_webgis`.
4. Copy file `.env.example` menjadi `.env`.
5. Jika perlu, sesuaikan bagian database di file `.env`.
6. Jalankan perintah berikut di terminal pada folder proyek:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

7. Buka browser ke `http://127.0.0.1:8000`.

## Catatan penting
- Session dan cache sudah diatur ke mode file agar aman dipakai pada XAMPP.
- Jika Anda memakai database lama, sebaiknya hapus dulu database lama lalu buat ulang.
- Jika foto tidak tampil, ulangi perintah `php artisan storage:link`.
