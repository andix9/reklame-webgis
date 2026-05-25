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

### Endpoint API ESP32
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

#### Cara menjalankan di XAMPP
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

# Perbaikan Database Tahap 1

Fokus perbaikan:
- normalisasi tabel sesuai skripsi
- pemisahan tabel `status_pajak`, `lokasi`, dan `dokumentasi`
- relasi `reklames` ke tabel pendukung
- seeder status pajak dan admin

Catatan:
- Setelah perubahan ini, jalankan `php artisan migrate:fresh --seed`
- Controller dan view lama masih perlu disesuaikan karena sebelumnya membaca kolom flat langsung dari tabel `reklames`


## Perbaikan fokus fitur tambah data dan edit data.

File yang diubah:
1. app/Http/Controllers/ReklameController.php
   - tambah try/catch saat simpan dan update
   - validasi foto sekarang menerima jpg, jpeg, png, webp
   - jika gagal, muncul pesan yang lebih jelas

2. resources/views/reklames/create.blade.php
   - tampilkan pesan validasi
   - latitude/longitude bisa diisi manual
   - keterangan format foto diperjelas

3. resources/views/reklames/edit.blade.php
   - tampilkan pesan validasi
   - latitude/longitude bisa diisi manual
   - keterangan format foto diperjelas

Langkah setelah update:
- php artisan optimize:clear
- php artisan storage:link
- pastikan login sebagai admin

Daftar file yang diubah / ditambahkan:

1. DIUBAH
- bootstrap/app.php
- routes/web.php
- app/Http/Controllers/LoginController.php
- app/Http/Controllers/ReklameController.php
- app/Models/User.php
- database/migrations/0001_01_01_000000_create_users_table.php
- database/seeders/DatabaseSeeder.php
- resources/views/reklames/create.blade.php
- resources/views/reklames/edit.blade.php
- resources/views/reklames/index.blade.php
- resources/views/reklames/list.blade.php
- .env.example
- README.md

2. DITAMBAHKAN
- routes/api.php
- app/Http/Middleware/AdminMiddleware.php
- PERUBAHAN_CHATGPT.txt

3. DIHAPUS
- database/migrations/2026_01_30_044003_create_users_table.php
- .env

Perubahan utama:
- menambahkan sistem role admin
- membuat akun admin default
- membatasi halaman manajemen data hanya untuk admin
- menambahkan endpoint API untuk ESP32
- menyeragamkan status pajak menjadi Lunas / Belum Lunas
- mengubah default konfigurasi XAMPP pada .env.example
- mengarahkan default peta ke area Simalungun



### Sistem Monitoring Pajak Reklame Berbasis IoT dan Web GIS

Sistem ini merupakan aplikasi monitoring pajak reklame berbasis Internet of Things (IoT) dan Web GIS. Sistem dikembangkan untuk membantu proses pendataan, dokumentasi visual, pengambilan koordinat lokasi, pengelolaan status pajak, visualisasi titik reklame pada peta digital, serta penyusunan laporan data reklame.

Aplikasi ini menggunakan Laravel sebagai backend, MySQL sebagai basis data, Leaflet/OpenStreetMap sebagai peta digital, serta perangkat ESP32-CAM dan ESP32-GPS sebagai perangkat akuisisi data lapangan.

# Fitur Utama

### 1. Monitoring Reklame Berbasis Web GIS

Sistem menampilkan data reklame dalam bentuk marker pada peta digital berbasis OpenStreetMap. Setiap marker memuat informasi reklame seperti nama pemilik, alamat, status pajak, tanggal masa berlaku, koordinat lokasi, dan dokumentasi foto.

### 2. Akuisisi Foto Reklame Menggunakan ESP32-CAM

ESP32-CAM digunakan untuk mengambil foto objek reklame di lapangan. Foto dikirim ke server Laravel melalui REST API dan disimpan sebagai dokumentasi visual reklame.

### 3. Akuisisi Koordinat Menggunakan ESP32-GPS

ESP32 yang terhubung dengan modul GPS digunakan untuk membaca koordinat latitude dan longitude. Data koordinat dikirim ke server melalui REST API dan digunakan sebagai dasar penempatan marker pada Web GIS.

### 4. Integrasi Data Foto dan Koordinat

Data foto dari ESP32-CAM dan data koordinat dari ESP32-GPS dihubungkan menggunakan kode reklame/session yang sama. Dengan mekanisme ini, foto dan titik lokasi dapat tersimpan sebagai satu kesatuan data reklame.

### 5. Manajemen Data Reklame

Admin dapat menambah, melihat, mengedit, dan menghapus data reklame melalui dashboard. Data yang dikelola meliputi nama pemilik, alamat, status pajak, tanggal masa berlaku, foto, dan informasi lokasi.

### 6. Date Exp / Masa Berlaku Reklame

Sistem menyediakan field Date Exp atau masa berlaku reklame. Informasi ini ditampilkan pada detail data dan marker Web GIS untuk membantu pemantauan masa berlaku reklame.

### 7. Lock Koordinat

Koordinat latitude dan longitude pada halaman edit dikunci agar tidak dapat diubah secara manual oleh admin. Koordinat tetap mengikuti data yang diperoleh dari perangkat GPS agar akurasi data lokasi tetap terjaga.

### 8. Export Data Reklame ke Excel

Sistem menyediakan fitur Export Excel untuk mengunduh data reklame dalam bentuk file laporan. Fitur ini dibuat untuk memudahkan admin dalam menyusun laporan data reklame yang dapat diserahkan kepada atasan, pejabat terkait, atau pihak yang membutuhkan.

Data yang diekspor meliputi:

- Kode reklame
- Nama reklame
- Nama pemilik
- Jenis reklame
- Ukuran
- Date Exp / masa berlaku
- Status pajak
- Alamat
- Latitude
- Longitude
- Foto
- Sumber lokasi
- Waktu kirim lokasi
- Tanggal upload foto
- Tanggal data dibuat

Fitur export dibuat dalam format `.xls`, sehingga dapat langsung dibuka menggunakan Microsoft Excel tanpa instalasi package tambahan.

### 9. Diagram Statistik Reklame

Sistem dilengkapi diagram batang statistik pada halaman Web GIS. Diagram ini digunakan untuk menampilkan ringkasan data reklame secara visual.

Kategori yang ditampilkan pada diagram statistik meliputi:

- Total data
- Data tampil
- Lunas
- Belum terdaftar

Diagram dibuat dalam bentuk diagram batang vertikal dengan tampilan klasik, sehingga mudah dibaca sebagai ringkasan laporan. Diagram juga menyesuaikan data yang tampil berdasarkan pencarian dan filter status pajak.

### 10. Filter dan Pencarian Data

Sistem menyediakan fitur filter status pajak dan pencarian data reklame. Filter ini membantu admin menampilkan data tertentu berdasarkan status pajak atau kata kunci pencarian.

## Teknologi yang Digunakan

- Laravel
- PHP
- MySQL
- Bootstrap
- Leaflet.js
- OpenStreetMap
- ESP32-CAM
- ESP32 + GPS Module
- REST API
- Arduino IDE

## Struktur Fitur Terbaru

Perubahan terbaru yang ditambahkan pada sistem meliputi:

1. Penambahan fitur Export Excel pada halaman daftar data reklame.
2. Penambahan diagram statistik reklame pada halaman Web GIS.
3. Penambahan kategori Belum Terdaftar pada diagram statistik.
4. Penghapusan kotak statistik terpisah Total, Tampil, dan Lunas agar tampilan lebih sederhana.
5. Pengubahan tampilan diagram menjadi diagram batang vertikal bergaya klasik.
6. Penghapusan tombol Export Excel pada navbar atas agar tampilan lebih rapi.
7. Tombol Export Excel tetap tersedia pada halaman Daftar Data Reklame.

## Instalasi Project

Clone repository:

```bash
git clone https://github.com/username/reklame-webgis.git
