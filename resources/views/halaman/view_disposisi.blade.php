@extends('index')
@section('title', 'Disposisi')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Disposisi</h1>
        <p class="text-muted mb-0">Kelola disposisi surat secara terstruktur.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="toggleCollapse('formTambahDisposisi')" aria-controls="formTambahDisposisi" aria-expanded="false">+ Tambah Disposisi</button>
</div>

<div class="collapse mb-4" id="formTambahDisposisi">
    <div class="page-card p-4">
        <h2 class="h5 mb-3">Tambah Disposisi</h2>
        <form action="{{ route('disposisi.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">ID Surat Masuk</label>
                <input class="form-control" type="number" name="id_surat_masuk" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tujuan Disposisi</label>
                <input class="form-control" name="tujuan_disposisi" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sifat Disposisi</label>
                <select class="form-select" name="sifat_disposisi" required>
                    <option value="">-- Pilih --</option>
                    <option value="Biasa">Biasa</option>
                    <option value="Penting">Penting</option>
                    <option value="Segera">Segera</option>
                    <option value="Rahasia">Rahasia</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Batas Waktu</label>
                <input class="form-control" type="date" name="batas_waktu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Isi Disposisi</label>
                <textarea class="form-control" name="isi_disposisi" rows="2" required></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan Disposisi</button>
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
                    <th>ID Surat</th>
                    <th>Tujuan</th>
                    <th>Sifat</th>
                    <th>Batas Waktu</th>
                    <th>Isi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disposisi as $index=>$bk)
                    <tr>
                        <td>{{ $index + $disposisi->firstItem() }}</td>
                        <td>{{ $bk->id_surat_masuk }}</td>
                        <td>{{ $bk->tujuan_disposisi }}</td>
                        <td>{{ $bk->sifat_disposisi }}</td>
                        <td>{{ $bk->batas_waktu }}</td>
                        <td>{{ $bk->isi_disposisi }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary">Ubah</summary>
                                <form action="{{ route('disposisi.update', $bk->id_disposisi) }}" method="POST" class="page-card p-3 mt-2" style="min-width:300px">
                                    @csrf @method('PUT')
                                    <input class="form-control mb-2" type="number" name="id_surat_masuk" value="{{ $bk->id_surat_masuk }}" required>
                                    <input class="form-control mb-2" name="tujuan_disposisi" value="{{ $bk->tujuan_disposisi }}" required>
                                    <select class="form-select mb-2" name="sifat_disposisi" required>
                                        <option value="Biasa" {{ $bk->sifat_disposisi=='Biasa'?'selected':'' }}>Biasa</option>
                                        <option value="Penting" {{ $bk->sifat_disposisi=='Penting'?'selected':'' }}>Penting</option>
                                        <option value="Segera" {{ $bk->sifat_disposisi=='Segera'?'selected':'' }}>Segera</option>
                                        <option value="Rahasia" {{ $bk->sifat_disposisi=='Rahasia'?'selected':'' }}>Rahasia</option>
                                    </select>
                                    <input class="form-control mb-2" type="date" name="batas_waktu" value="{{ $bk->batas_waktu }}" required>
                                    <textarea class="form-control mb-2" name="isi_disposisi" required>{{ $bk->isi_disposisi }}</textarea>
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('disposisi.destroy', $bk->id_disposisi) }}" method="POST" onsubmit="return confirm('Hapus disposisi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">Belum ada disposisi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $disposisi->links() }}</div>
@endsection