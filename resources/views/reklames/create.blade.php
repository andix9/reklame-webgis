<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Reklame</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; width: 100%; border-radius: 8px; border: 2px solid #dee2e6; }
        .card { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border: none; }
        .form-label { font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('reklames.index') }}">Reklame WebGIS</a>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 text-center fw-bold text-primary">Tambah Data Reklame</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
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

                        <form action="{{ route('reklames.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="nama_pemilik" class="form-label">Nama Pemilik</label>
                                <input type="text" class="form-control" id="nama_pemilik" name="nama_pemilik" value="{{ old('nama_pemilik') }}" required placeholder="Contoh: PT. Maju Mundur">
                            </div>

                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required placeholder="Masukkan alamat lokasi reklame">{{ old('alamat') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status_pajak" class="form-label">Status Pajak</label>
                                    <select class="form-select" id="status_pajak" name="status_pajak" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Lunas" {{ old('status_pajak') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="Belum Lunas" {{ old('status_pajak') === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                        <option value="Tidak Terdaftar" {{ old('status_pajak') === 'Tidak Terdaftar' ? 'selected' : '' }}>Tidak Terdaftar</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="date_exp" class="form-label">Date Exp / Masa Berlaku</label>
                                    <input type="date" class="form-control" id="date_exp" name="date_exp" value="{{ old('date_exp') }}">
                                    <div class="form-text">Isi tanggal masa berlaku reklame jika tersedia.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Reklame</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <div class="form-text">Format: JPG, JPEG, PNG, atau WEBP. Ukuran maksimal 4 MB.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-block">Lokasi Titik Reklame</label>
                                <div class="alert alert-info py-2 small">
                                    Geser marker merah di peta atau klik lokasi yang diinginkan. Jika peta tidak muncul, isi latitude dan longitude secara manual.
                                </div>
                                <div id="map" class="mb-3"></div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Lat</span>
                                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', '2.9658000') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Lng</span>
                                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', '99.0637000') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('reklames.list') }}" class="btn btn-secondary me-md-2 px-4">Batal</a>
                                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Ambil nilai dari input yang mungkin berisi nilai old() dari Laravel
        // Jika kosong, baru gunakan nilai default (2.9658, 99.0637)
        let startLat = parseFloat(document.getElementById('latitude').value) || 2.9658;
        let startLng = parseFloat(document.getElementById('longitude').value) || 99.0637;

        var map = L.map('map').setView([startLat, startLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        function updateInput(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
        }

        // Hapus pemanggilan awal karena value sudah diisi melalui attribute di Blade

        // Sinkronisasi jika marker digeser
        marker.on('dragend', function () {
            const position = marker.getLatLng();
            updateInput(position.lat, position.lng);
        });

        // Sinkronisasi jika peta diklik
        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            updateInput(lat, lng);
        });

        // Sinkronisasi jika latitude diisi secara manual
        document.getElementById('latitude').addEventListener('input', function(e) {
            let lat = parseFloat(e.target.value);
            let lng = parseFloat(document.getElementById('longitude').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        });

        // Sinkronisasi jika longitude diisi secara manual
        document.getElementById('longitude').addEventListener('input', function(e) {
            let lat = parseFloat(document.getElementById('latitude').value);
            let lng = parseFloat(e.target.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng]);
            }
        });
    </script>
</body>
</html>
