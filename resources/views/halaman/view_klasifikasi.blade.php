@extends('index')
@section('title', 'Klasifikasi')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Klasifikasi</h1>
        <p class="text-muted mb-0">Kelola kode dan nama klasifikasi surat.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="toggleCollapse('formTambahKlasifikasi')" aria-controls="formTambahKlasifikasi" aria-expanded="false">+ Tambah Klasifikasi</button>
</div>

<div class="collapse mb-4" id="formTambahKlasifikasi">
    <div class="page-card p-4">
        <h2 class="h5 mb-3">Tambah Klasifikasi</h2>
        <form action="{{ route('klasifikasi.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label">Kode Klasifikasi</label>
                <input class="form-control" name="kode_klasifikasi" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Klasifikasi</label>
                <input class="form-control" name="nama_klasifikasi" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan Klasifikasi</button>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($klasifikasi as $index=>$k)
                    <tr>
                        <td>{{ $index + $klasifikasi->firstItem() }}</td>
                        <td>{{ $k->kode_klasifikasi }}</td>
                        <td>{{ $k->nama_klasifikasi }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary">Ubah</summary>
                                <form action="{{ route('klasifikasi.update', $k->id_klasifikasi) }}" method="POST" class="page-card p-3 mt-2" style="min-width:280px">
                                    @csrf @method('PUT')
                                    <input class="form-control mb-2" name="kode_klasifikasi" value="{{ $k->kode_klasifikasi }}" required>
                                    <input class="form-control mb-2" name="nama_klasifikasi" value="{{ $k->nama_klasifikasi }}" required>
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('klasifikasi.destroy', $k->id_klasifikasi) }}" method="POST" onsubmit="return confirm('Hapus klasifikasi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">Belum ada klasifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $klasifikasi->links() }}</div>
@endsection