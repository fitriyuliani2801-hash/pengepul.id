@extends('index')
@section('title', 'Disposisi')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-file-signature text-warning"></i> Disposisi Surat
        </h1>
        <p class="text-muted mb-0">Kelola instruksi dan penugasan disposisi surat secara terstruktur.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="toggleCollapse('formTambahDisposisi')" aria-controls="formTambahDisposisi" aria-expanded="false">
        <i class="fa-solid fa-plus me-1"></i> Tambah Disposisi
    </button>
</div>

<div class="collapse mb-4" id="formTambahDisposisi">
    <div class="page-card p-4">
        <h2 class="h5 mb-3 fw-bold text-dark"><i class="fa-solid fa-file-circle-plus me-2 text-warning"></i> Formulir Tambah Disposisi</h2>
        <form action="{{ route('disposisi.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">ID Surat Masuk</label>
                <input class="form-control" type="number" name="id_surat_masuk" placeholder="Contoh: 1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">Tujuan Disposisi</label>
                <input class="form-control" name="tujuan_disposisi" placeholder="Nama pejabat / unit kerja" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">Sifat Disposisi</label>
                <select class="form-select" name="sifat_disposisi" required>
                    <option value="">-- Pilih Sifat --</option>
                    <option value="Biasa">Biasa</option>
                    <option value="Penting">Penting</option>
                    <option value="Segera">Segera</option>
                    <option value="Rahasia">Rahasia</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Batas Waktu</label>
                <input class="form-control" type="date" name="batas_waktu" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Isi Disposisi / Instruksi</label>
                <textarea class="form-control" name="isi_disposisi" rows="2" placeholder="Catatan instruksi pimpinan..." required></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Disposisi
                </button>
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
                    <th>Isi Disposisi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($disposisi as $index=>$bk)
                    <tr>
                        <td class="fw-semibold">{{ $index + $disposisi->firstItem() }}</td>
                        <td><span class="badge badge-soft-info badge-pill-custom">#{{ $bk->id_surat_masuk }}</span></td>
                        <td class="fw-semibold text-dark">{{ $bk->tujuan_disposisi }}</td>
                        <td>
                            @php($sifatBadge = match($bk->sifat_disposisi){'Segera'=>'badge-soft-danger','Penting'=>'badge-soft-warning','Rahasia'=>'badge-soft-info', default=>'badge-soft-primary'})
                            <span class="badge {{ $sifatBadge }} badge-pill-custom">{{ $bk->sifat_disposisi }}</span>
                        </td>
                        <td><i class="fa-regular fa-clock me-1 text-muted"></i> {{ $bk->batas_waktu }}</td>
                        <td>{{ $bk->isi_disposisi }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Ubah
                                </summary>
                                <form action="{{ route('disposisi.update', $bk->id_disposisi) }}" method="POST" class="page-card p-3 mt-2 shadow-lg" style="min-width:300px; z-index:10;">
                                    @csrf @method('PUT')
                                    <label class="form-label fw-semibold small mb-1">ID Surat Masuk</label>
                                    <input class="form-control mb-2" type="number" name="id_surat_masuk" value="{{ $bk->id_surat_masuk }}" required>
                                    <label class="form-label fw-semibold small mb-1">Tujuan</label>
                                    <input class="form-control mb-2" name="tujuan_disposisi" value="{{ $bk->tujuan_disposisi }}" required>
                                    <label class="form-label fw-semibold small mb-1">Sifat</label>
                                    <select class="form-select mb-2" name="sifat_disposisi" required>
                                        <option value="Biasa" {{ $bk->sifat_disposisi=='Biasa'?'selected':'' }}>Biasa</option>
                                        <option value="Penting" {{ $bk->sifat_disposisi=='Penting'?'selected':'' }}>Penting</option>
                                        <option value="Segera" {{ $bk->sifat_disposisi=='Segera'?'selected':'' }}>Segera</option>
                                        <option value="Rahasia" {{ $bk->sifat_disposisi=='Rahasia'?'selected':'' }}>Rahasia</option>
                                    </select>
                                    <label class="form-label fw-semibold small mb-1">Batas Waktu</label>
                                    <input class="form-control mb-2" type="date" name="batas_waktu" value="{{ $bk->batas_waktu }}" required>
                                    <label class="form-label fw-semibold small mb-1">Isi Disposisi</label>
                                    <textarea class="form-control mb-2" name="isi_disposisi" required>{{ $bk->isi_disposisi }}</textarea>
                                    <button class="btn btn-sm btn-primary w-100 rounded-pill mt-2">Simpan Perubahan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('disposisi.destroy', $bk->id_disposisi) }}" method="POST" onsubmit="return confirm('Hapus disposisi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state py-5"><i class="fa-solid fa-file-signature fs-2 mb-2 d-block text-muted"></i>Belum ada data disposisi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $disposisi->links() }}</div>
@endsection