<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Reklame</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />
    <style>
        body {
            background-color: #f1f5f9;
        }

        #map {
            height: 420px;
            width: 100%;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
        }

        .card {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .card-header {
            background: linear-gradient(45deg, #0f172a, #334155);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.5rem;
        }

        .img-preview {
            max-height: 220px;
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 1rem;
        }

        .map-note {
            font-size: 0.9rem;
        }

        .leaflet-container {
            font-family: inherit;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('reklames.index') }}">Web GIS Reklame</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Data Reklame</h4>
                    <a href="{{ route('reklames.list') }}" class="btn btn-outline-light btn-sm">Kembali ke Daftar</a>
                </div>

                <div class="card-body p-4">
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Data belum bisa disimpan.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reklames.update', $reklame->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="section-title">Informasi Utama Reklame</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Reklame</label>
                                <input
                                    type="text"
                                    name="kode_reklame"
                                    class="form-control"
                                    value="{{ old('kode_reklame', $reklame->kode_reklame ?? '') }}"
                                    placeholder="Contoh: RKL-0001"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Reklame</label>
                                <input
                                    type="text"
                                    name="nama_reklame"
                                    class="form-control"
                                    value="{{ old('nama_reklame', $reklame->nama_reklame ?? '') }}"
                                    placeholder="Contoh: Billboard Simpang Raya"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Pemilik / Usaha</label>
                                <input
                                    type="text"
                                    name="nama_pemilik"
                                    class="form-control"
                                    value="{{ old('nama_pemilik', $reklame->nama_pemilik ?? '') }}"
                                    required
                                >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jenis Reklame</label>
                                <input
                                    type="text"
                                    name="jenis_reklame"
                                    class="form-control"
                                    value="{{ old('jenis_reklame', $reklame->jenis_reklame ?? '') }}"
                                    placeholder="Contoh: Billboard"
                                >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Ukuran</label>
                                <input
                                    type="text"
                                    name="ukuran"
                                    class="form-control"
                                    value="{{ old('ukuran', $reklame->ukuran ?? '') }}"
                                    placeholder="Contoh: 4x6 m"
                                >
                            </div>
                        </div>

                        <div class="section-title mt-4">Status dan Lokasi</div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Pajak</label>
                                    <select name="status_pajak" class="form-select" required>
                                        <option value="Lunas" {{ old('status_pajak', $reklame->status_pajak ?? '') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="Belum Lunas" {{ old('status_pajak', $reklame->status_pajak ?? '') == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                        <option value="Tidak Terdaftar" {{ old('status_pajak', $reklame->status_pajak ?? '') == 'Tidak Terdaftar' ? 'selected' : '' }}>Tidak Terdaftar</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Date Exp / Masa Berlaku</label>
                                    <input
                                        type="date"
                                        name="date_exp"
                                        class="form-control"
                                        value="{{ old('date_exp', $reklame->date_exp ?? '') }}"
                                    >
                                    <div class="form-text">Isi tanggal masa berlaku reklame jika tersedia.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat akan terisi setelah GPS mengirim data, atau isi manual">{{ old('alamat', $reklame->alamat ?? '') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label small text-muted">Latitude</label>
                                        <input
                                            type="text"
                                            id="latitude"
                                            name="latitude"
                                            class="form-control bg-light"
                                            value="{{ old('latitude', $reklame->latitude ?? '2.9658000') }}"
                                            readonly
                                            required
                                        >
                                    </div>

                                    <div class="col-6 mb-3">
                                        <label class="form-label small text-muted">Longitude</label>
                                        <input
                                            type="text"
                                            id="longitude"
                                            name="longitude"
                                            class="form-control bg-light"
                                            value="{{ old('longitude', $reklame->longitude ?? '99.0637000') }}"
                                            readonly
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="alert alert-info map-note">
                                    Koordinat dikunci dan tidak dapat diubah melalui halaman edit. Perubahan koordinat hanya diperbarui melalui data GPS/IoT.
                                </div>

                                <div id="mapStatus" class="alert alert-warning d-none map-note">
                                    Tile OpenStreetMap tidak berhasil dimuat. Cek koneksi internet laptop Anda.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Reklame (Terkunci)</label>
                                    <div id="map"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Foto Reklame</label>
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            @if(!empty($reklame->foto))
                                                <img src="{{ asset('storage/' . $reklame->foto) }}" class="img-preview" alt="Foto Lama">
                                            @else
                                                <div class="bg-light text-center py-4 border rounded text-muted small">Tidak ada foto</div>
                                            @endif
                                        </div>

                                        <div class="col-8">
                                            <input type="file" name="foto" class="form-control" accept="image/*">
                                            <div class="form-text">Upload foto baru jika ingin mengganti foto lama.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('reklames.list') }}" class="btn btn-secondary px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const mapStatus = document.getElementById('mapStatus');

        let startLat = parseFloat(latInput.value);
        let startLng = parseFloat(lngInput.value);

        if (isNaN(startLat)) startLat = 2.9658;
        if (isNaN(startLng)) startLng = 99.0637;

        const map = L.map('map', {
            zoomControl: true
        }).setView([startLat, startLng], 15);

        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        tileLayer.on('tileerror', function () {
            mapStatus.classList.remove('d-none');
        });

        tileLayer.on('load', function () {
            mapStatus.classList.add('d-none');
        });

        tileLayer.addTo(map);

        const marker = L.marker([startLat, startLng], {
            draggable: false
        }).addTo(map);

        // Koordinat dikunci pada halaman edit.
        // Marker tidak dapat digeser dan input latitude/longitude dibuat readonly.
        // Perubahan koordinat hanya dilakukan melalui pengiriman data GPS/IoT.

        setTimeout(function () {
            map.invalidateSize();
        }, 300);
    });
</script>
</body>
</html>
