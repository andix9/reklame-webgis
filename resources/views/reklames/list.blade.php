<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Reklame</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('reklames.index') }}">Web GIS Reklame</a>
        <div class="d-flex gap-2">
            <a href="{{ route('reklames.create') }}" class="btn btn-success btn-sm">Tambah Data</a>
            <a href="{{ route('reklames.index') }}" class="btn btn-outline-light btn-sm">Lihat Peta</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Daftar Data Reklame</h4>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('reklames.exportExcel') }}" class="btn btn-success btn-sm">Export Excel</a>
                    <span class="badge bg-primary">{{ $reklames->count() }} data</span>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Pemilik</th>
                            <th>Alamat</th>
                            <th>Status Pajak</th>
                            <th>Date Exp</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reklames as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_pemilik }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pajak === 'Lunas' ? 'bg-success' : ($item->status_pajak === 'Tidak Terdaftar' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $item->status_pajak }}
                                    </span>
                                </td>
                                <td>
                                    {{ $item->date_exp ? \Illuminate\Support\Carbon::parse($item->date_exp)->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td>
                                    @if($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto Reklame" width="100" class="img-thumbnail">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('reklames.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('reklames.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data reklame.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
