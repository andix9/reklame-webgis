<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Reklame</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000000;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #d9eaf7;
            font-weight: bold;
            text-align: center;
        }
        .title {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .subtitle {
            font-family: Arial, sans-serif;
            font-size: 12px;
            text-align: center;
        }
        .text {
            mso-number-format: "\@";
        }
    </style>
</head>
<body>
    <div class="title">LAPORAN DATA REKLAME</div>
    <div class="subtitle">Sistem Monitoring Pajak Reklame Berbasis IoT dan Web GIS</div>
    <div class="subtitle">Tanggal Cetak: {{ now()->format('d-m-Y H:i:s') }}</div>
    <br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Reklame</th>
                <th>Nama Reklame</th>
                <th>Nama Pemilik</th>
                <th>Jenis Reklame</th>
                <th>Ukuran</th>
                <th>Date Exp / Masa Berlaku</th>
                <th>Status Pajak</th>
                <th>Alamat</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Foto</th>
                <th>Sumber Lokasi</th>
                <th>Waktu Kirim Lokasi</th>
                <th>Tanggal Upload Foto</th>
                <th>Tanggal Data Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reklames as $item)
                @php
                    $lokasi = $item->lokasi->sortByDesc('id')->first();
                    $dokumentasi = $item->dokumentasi->sortByDesc('id')->first();
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                    <td class="text">{{ $item->kode_reklame ?? '-' }}</td>
                    <td>{{ $item->nama_reklame ?? '-' }}</td>
                    <td>{{ $item->nama_pemilik ?? '-' }}</td>
                    <td>{{ $item->jenis_reklame ?? '-' }}</td>
                    <td class="text">{{ $item->ukuran ?? '-' }}</td>
                    <td>{{ $item->date_exp ? $item->date_exp->format('d-m-Y') : '-' }}</td>
                    <td>{{ optional($item->statusPajak)->nama_status ?? '-' }}</td>
                    <td>{{ optional($lokasi)->alamat ?? '-' }}</td>
                    <td class="text">{{ optional($lokasi)->latitude ?? '-' }}</td>
                    <td class="text">{{ optional($lokasi)->longitude ?? '-' }}</td>
                    <td>{{ optional($dokumentasi)->foto ?? '-' }}</td>
                    <td>{{ optional($lokasi)->sumber_data ?? '-' }}</td>
                    <td>{{ optional(optional($lokasi)->waktu_kirim)->format('d-m-Y H:i:s') ?? '-' }}</td>
                    <td>{{ optional(optional($dokumentasi)->tanggal_upload)->format('d-m-Y H:i:s') ?? '-' }}</td>
                    <td>{{ optional($item->created_at)->format('d-m-Y H:i:s') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" style="text-align:center;">Belum ada data reklame.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>
    <table>
        <tr>
            <td><strong>Total Data</strong></td>
            <td>{{ $reklames->count() }}</td>
        </tr>
    </table>
</body>
</html>
