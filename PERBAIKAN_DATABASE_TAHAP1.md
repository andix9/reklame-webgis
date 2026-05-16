# Perbaikan Database Tahap 1

Fokus perbaikan:
- normalisasi tabel sesuai skripsi
- pemisahan tabel `status_pajak`, `lokasi`, dan `dokumentasi`
- relasi `reklames` ke tabel pendukung
- seeder status pajak dan admin

Catatan:
- Setelah perubahan ini, jalankan `php artisan migrate:fresh --seed`
- Controller dan view lama masih perlu disesuaikan karena sebelumnya membaca kolom flat langsung dari tabel `reklames`
