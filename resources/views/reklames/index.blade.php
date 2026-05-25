<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Web GIS Reklame</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        #sidebar {
            height: calc(100vh - 56px);
            background-color: #1e293b;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar-section-header {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            font-weight: 700;
        }

        #sidebar .content {
            padding: 15px;
            overflow-y: auto;
            flex-grow: 1;
        }

        #detail img {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.15);
        }

        #map {
            height: calc(100vh - 56px - 205px);
            min-height: 420px;
        }

        .leaflet-tooltip {
            background-color: #111827;
            color: white;
            border-radius: 5px;
            font-size: 13px;
            padding: 6px;
        }

        .detail-label {
            color: #cbd5e1;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }

        .detail-value {
            margin-bottom: 10px;
            font-weight: 500;
            word-break: break-word;
        }

        .stat-box {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .map-summary {
            background: #f4f1e8;
            border-bottom: 2px solid #333333;
        }

        .vertical-chart-card {
            background: #ffffff;
            border: 1px solid #555555;
            border-radius: 0;
            padding: 12px 16px;
            height: 100%;
            box-shadow: none;
        }

        .vertical-chart-title {
            color: #111111;
            font-family: "Times New Roman", Times, serif;
            font-size: 1rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }

        .vertical-chart {
            height: 145px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 18px;
            padding: 10px 10px 26px 10px;
            border-left: 2px solid #333333;
            border-bottom: 2px solid #333333;
            background:
                repeating-linear-gradient(
                    to top,
                    #ffffff 0,
                    #ffffff 28px,
                    #e5e5e5 29px
                );
        }

        .vertical-chart-item {
            min-width: 68px;
            text-align: center;
        }

        .chart-track {
            width: 42px;
            height: 105px;
            margin: 0 auto 7px auto;
            background: transparent;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: visible;
        }

        .chart-bar {
            width: 100%;
            height: 0%;
            min-height: 2px;
            border-radius: 0;
            border: 1px solid #333333;
            transition: height 0.3s ease;
        }

        .chart-bar-total {
            background: #4f81bd;
        }

        .chart-bar-visible {
            background: #c0c0c0;
        }

        .chart-bar-lunas {
            background: #70ad47;
        }

        .chart-bar-belum-terdaftar {
            background: #f4b183;
        }

        .chart-value {
            color: #111111;
            font-family: "Times New Roman", Times, serif;
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .chart-label {
            color: #111111;
            font-family: "Times New Roman", Times, serif;
            font-size: 0.74rem;
            line-height: 1.1;
        }

        .map-status {
            font-size: 0.85rem;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            #sidebar {
                height: auto;
            }

            #map {
                height: 60vh;
                min-height: 360px;
            }

            .vertical-chart {
                height: 105px;
                gap: 18px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Web GIS Reklame</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex ms-auto" role="search" onsubmit="return false;">
                <input class="form-control me-2" type="search" id="searchBox" placeholder="Cari nama pemilik..." aria-label="Search">
            </form>

            <ul class="navbar-nav">
                @auth
                    <li class="nav-item d-flex align-items-center text-white me-2 small">
                        {{ auth()->user()->name }} ({{ auth()->user()->role }})
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light btn-sm ms-2" type="submit">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login Admin</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3 p-0" id="sidebar">
            <div class="p-3 border-bottom border-secondary d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->check() ? auth()->user()->name : 'Guest User') }}&background=0D8ABC&color=fff" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                <div>
                    <h6 class="mb-0">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</h6>
                    <small class="text-white-50" style="font-size: 0.8rem;">
                        {{ auth()->check() ? ucfirst(auth()->user()->role) : 'Pengunjung' }}
                    </small>
                </div>
            </div>

            <div class="p-3 border-bottom border-secondary">
                <div class="d-grid gap-2">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('reklames.create') }}" class="btn btn-success btn-sm">Tambah Data</a>
                            <a href="{{ route('reklames.list') }}" class="btn btn-info btn-sm text-white">Daftar dan Hapus Data</a>
                        @endif
                    @endauth
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <label class="text-white-50 small mb-1">Filter Status Pajak</label>
                        <select id="filterStatus" class="form-select form-select-sm bg-dark text-white border-secondary">
                            <option value="">Semua Status</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Tidak Terdaftar">Tidak Terdaftar</option>
                        </select>
                    </div>
                </div>

                <div id="mapStatus" class="alert alert-warning d-none map-status mb-0">
                    Tile OpenStreetMap gagal dimuat. Cek koneksi internet.
                </div>
            </div>

            <h6 class="sidebar-section-header m-0 bg-secondary bg-opacity-10">Detail Reklame</h6>
            <div id="detail" class="content">
                <div class="alert alert-secondary bg-dark border-secondary text-white-50">
                    Klik marker pada peta untuk melihat detail reklame.
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-9 p-0">
            <div class="map-summary p-3">
                <div class="vertical-chart-card">
                    <div class="vertical-chart-title">Diagram Statistik Data Reklame</div>
                    <div class="vertical-chart">
                        <div class="vertical-chart-item">
                            <div class="chart-track">
                                <div class="chart-bar chart-bar-total" id="chartTotalBar"></div>
                            </div>
                            <div class="chart-value" id="chartTotalValue">0</div>
                            <div class="chart-label">Total</div>
                        </div>

                        <div class="vertical-chart-item">
                            <div class="chart-track">
                                <div class="chart-bar chart-bar-visible" id="chartVisibleBar"></div>
                            </div>
                            <div class="chart-value" id="chartVisibleValue">0</div>
                            <div class="chart-label">Tampil</div>
                        </div>

                        <div class="vertical-chart-item">
                            <div class="chart-track">
                                <div class="chart-bar chart-bar-lunas" id="chartLunasBar"></div>
                            </div>
                            <div class="chart-value" id="chartLunasValue">0</div>
                            <div class="chart-label">Lunas</div>
                        </div>

                        <div class="vertical-chart-item">
                            <div class="chart-track">
                                <div class="chart-bar chart-bar-belum-terdaftar" id="chartBelumTerdaftarBar"></div>
                            </div>
                            <div class="chart-value" id="chartBelumTerdaftarValue">0</div>
                            <div class="chart-label">Belum<br>Terdaftar</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="map"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const mapStatus = document.getElementById('mapStatus');

    var map = L.map('map').setView([2.9658, 99.0637], 12);

    var tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    });

    tileLayer.on('tileerror', function () {
        mapStatus.classList.remove('d-none');
    });

    tileLayer.on('load', function () {
        mapStatus.classList.add('d-none');
    });

    tileLayer.addTo(map);

    var rawReklames = @json($reklames);
    var markersLayer = L.markerClusterGroup();
    var isAdmin = @json(auth()->check() && auth()->user()->role === 'admin');

    function safeText(value, fallback = '-') {
        return value !== null && value !== undefined && String(value).trim() !== '' ? value : fallback;
    }

    function formatTanggalIndonesia(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(value + 'T00:00:00');

        if (isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }

    function getDateExpStatus(value) {
        if (!value) {
            return '-';
        }

        const today = new Date();
        const expDate = new Date(value + 'T00:00:00');

        today.setHours(0, 0, 0, 0);
        expDate.setHours(0, 0, 0, 0);

        if (expDate < today) {
            return 'Kedaluwarsa';
        }

        return 'Masih Berlaku';
    }

    function getDateExpBadge(value) {
        const status = getDateExpStatus(value);

        if (status === 'Kedaluwarsa') {
            return '<span class="badge bg-danger">Kedaluwarsa</span>';
        }

        if (status === 'Masih Berlaku') {
            return '<span class="badge bg-success">Masih Berlaku</span>';
        }

        return '<span class="badge bg-secondary">-</span>';
    }

    function getLatestLokasi(item) {
        if (item.lokasi && Array.isArray(item.lokasi) && item.lokasi.length > 0) {
            return item.lokasi[item.lokasi.length - 1];
        }
        return null;
    }

    function getLatestDokumentasi(item) {
        if (item.dokumentasi && Array.isArray(item.dokumentasi) && item.dokumentasi.length > 0) {
            return item.dokumentasi[item.dokumentasi.length - 1];
        }
        return null;
    }

    function getStatusName(item) {
        if (item.status_pajak && typeof item.status_pajak === 'string') {
            return item.status_pajak;
        }

        if (item.statusPajak && item.statusPajak.nama_status) {
            return item.statusPajak.nama_status;
        }

        if (item.status_pajak_relation && item.status_pajak_relation.nama_status) {
            return item.status_pajak_relation.nama_status;
        }

        return 'Belum Lunas';
    }

    function normalizeReklame(item) {
        const lokasi = getLatestLokasi(item);
        const dokumentasi = getLatestDokumentasi(item);

        return {
            id: item.id,
            kode_reklame: item.kode_reklame ?? null,
            nama_reklame: item.nama_reklame ?? null,
            nama_pemilik: item.nama_pemilik ?? null,
            jenis_reklame: item.jenis_reklame ?? null,
            ukuran: item.ukuran ?? null,
            date_exp: item.date_exp ?? null,
            status_pajak: getStatusName(item),
            alamat: item.alamat ?? (lokasi ? lokasi.alamat : null),
            latitude: item.latitude ?? (lokasi ? lokasi.latitude : null),
            longitude: item.longitude ?? (lokasi ? lokasi.longitude : null),
            foto: item.foto ?? (dokumentasi ? dokumentasi.foto : null)
        };
    }

    var allReklames = rawReklames.map(normalizeReklame);

    function setBarHeight(elementId, value, maxValue) {
        const element = document.getElementById(elementId);
        const percentage = maxValue > 0 ? (value / maxValue) * 100 : 0;
        element.style.height = Math.min(percentage, 100) + '%';
    }

    function isBelumTerdaftarStatus(status) {
        const normalizedStatus = (status || '').toString().toLowerCase().trim();

        return normalizedStatus === 'tidak terdaftar' ||
               normalizedStatus === 'belum terdaftar';
    }

    function updateStatistik(visibleData) {
        const totalCount = allReklames.length;
        const visibleCount = visibleData.length;
        const lunasCount = visibleData.filter(r => r.status_pajak === 'Lunas').length;
        const belumTerdaftarCount = visibleData.filter(r => isBelumTerdaftarStatus(r.status_pajak)).length;
        const maxValue = Math.max(totalCount, visibleCount, lunasCount, belumTerdaftarCount, 1);

        document.getElementById('chartTotalValue').innerText = totalCount;
        document.getElementById('chartVisibleValue').innerText = visibleCount;
        document.getElementById('chartLunasValue').innerText = lunasCount;
        document.getElementById('chartBelumTerdaftarValue').innerText = belumTerdaftarCount;

        setBarHeight('chartTotalBar', totalCount, maxValue);
        setBarHeight('chartVisibleBar', visibleCount, maxValue);
        setBarHeight('chartLunasBar', lunasCount, maxValue);
        setBarHeight('chartBelumTerdaftarBar', belumTerdaftarCount, maxValue);
    }

    var greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var orangeIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    function getStatusBadge(status) {
        if (status === 'Lunas') {
            return '<span class="badge bg-success">Lunas</span>';
        }
        if (status === 'Tidak Terdaftar') {
            return '<span class="badge bg-warning text-dark">Tidak Terdaftar</span>';
        }
        return '<span class="badge bg-danger">Belum Lunas</span>';
    }

    function getIconByStatus(status) {
        if (status === 'Lunas') return greenIcon;
        if (status === 'Tidak Terdaftar') return orangeIcon;
        return redIcon;
    }

    function renderDetail(r) {
        var fotoHtml = r.foto
            ? `<img src="/storage/${r.foto}" class="img-fluid" alt="Foto Reklame">`
            : `<div class="alert alert-secondary bg-dark border-secondary text-white-50">Tidak ada foto reklame.</div>`;

        var adminControls = '';

        if (isAdmin) {
            adminControls = `
                <hr class="border-secondary">
                <a href="/reklames/${r.id}/edit" class="btn btn-warning btn-sm w-100 mb-2">Edit Data</a>
                <form action="/reklames/${r.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm w-100">Hapus Data</button>
                </form>
            `;
        }

        document.getElementById('detail').innerHTML = `
            ${fotoHtml}

            <div class="detail-label">Kode Reklame</div>
            <div class="detail-value">${safeText(r.kode_reklame)}</div>

            <div class="detail-label">Nama Reklame</div>
            <div class="detail-value">${safeText(r.nama_reklame)}</div>

            <div class="detail-label">Nama Pemilik / Usaha</div>
            <div class="detail-value">${safeText(r.nama_pemilik)}</div>

            <div class="detail-label">Jenis Reklame</div>
            <div class="detail-value">${safeText(r.jenis_reklame)}</div>

            <div class="detail-label">Ukuran</div>
            <div class="detail-value">${safeText(r.ukuran)}</div>

            <div class="detail-label">Status Pajak</div>
            <div class="detail-value">${getStatusBadge(r.status_pajak)}</div>

            <div class="detail-label">Date Exp / Masa Berlaku</div>
            <div class="detail-value">
                ${formatTanggalIndonesia(r.date_exp)}
                <div class="mt-1">${getDateExpBadge(r.date_exp)}</div>
            </div>

            <div class="detail-label">Alamat</div>
            <div class="detail-value">${safeText(r.alamat)}</div>

            <div class="detail-label">Koordinat</div>
            <div class="detail-value">${safeText(r.latitude)}, ${safeText(r.longitude)}</div>

            ${adminControls}
        `;
    }

    function renderMarkers(data) {
        markersLayer.clearLayers();

        var validData = data.filter(function (r) {
            return r.latitude !== null &&
                   r.longitude !== null &&
                   r.latitude !== '' &&
                   r.longitude !== '' &&
                   !isNaN(parseFloat(r.latitude)) &&
                   !isNaN(parseFloat(r.longitude));
        });

        validData.forEach(function (r) {
            var lat = parseFloat(r.latitude);
            var lng = parseFloat(r.longitude);
            var icon = getIconByStatus(r.status_pajak);
            var marker = L.marker([lat, lng], { icon: icon });

            marker.bindTooltip(
                "<b>" + safeText(r.nama_reklame, r.nama_pemilik) + "</b><br>" +
                "Status Pajak: " + safeText(r.status_pajak) + "<br>" +
                "Date Exp: " + formatTanggalIndonesia(r.date_exp) + "<br>" +
                "Status Exp: " + getDateExpStatus(r.date_exp),
                { direction: "top", permanent: false }
            );

            marker.on('click', function () {
                renderDetail(r);
            });

            markersLayer.addLayer(marker);
        });

        map.addLayer(markersLayer);
        updateStatistik(validData);

        if (validData.length > 0) {
            var bounds = validData.map(function (r) {
                return [parseFloat(r.latitude), parseFloat(r.longitude)];
            });

            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
        }
    }

    function applyFilters() {
        var keyword = document.getElementById('searchBox').value.toLowerCase().trim();
        var status = document.getElementById('filterStatus').value;

        var filtered = allReklames.filter(function (r) {
            var namaPemilik = (r.nama_pemilik || '').toLowerCase();
            var namaReklame = (r.nama_reklame || '').toLowerCase();
            var kodeReklame = (r.kode_reklame || '').toLowerCase();

            var matchKeyword =
                namaPemilik.includes(keyword) ||
                namaReklame.includes(keyword) ||
                kodeReklame.includes(keyword);

            var matchStatus = status === '' || r.status_pajak === status;

            return matchKeyword && matchStatus;
        });

        renderMarkers(filtered);
    }

    renderMarkers(allReklames);

    document.getElementById('searchBox').addEventListener('input', applyFilters);
    document.getElementById('filterStatus').addEventListener('change', applyFilters);
</script>
</body>
</html>
