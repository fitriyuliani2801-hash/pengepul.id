@extends('index')
@section('title', 'Klasifikasi')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-folder-tree text-info"></i> Klasifikasi Kategori Surat
        </h1>
        <p class="text-muted mb-0">Kelola kode indeks dan nama klasifikasi kearsipan surat.</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="toggleCollapse('formTambahKlasifikasi')" aria-controls="formTambahKlasifikasi" aria-expanded="false">
        <i class="fa-solid fa-plus me-1"></i> Tambah Klasifikasi
    </button>
</div>

<div class="collapse mb-4" id="formTambahKlasifikasi">
    <div class="page-card p-4">
        <h2 class="h5 mb-3 fw-bold text-dark"><i class="fa-solid fa-folder-plus me-2 text-info"></i> Formulir Tambah Klasifikasi</h2>
        <form action="{{ route('klasifikasi.store') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Kode Klasifikasi</label>
                <input class="form-control" name="kode_klasifikasi" placeholder="Contoh: 000.1 / ADM" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Nama Klasifikasi</label>
                <input class="form-control" name="nama_klasifikasi" placeholder="Contoh: Surat Keputusan / Undangan" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Klasifikasi
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
                    <th>Kode Klasifikasi</th>
                    <th>Nama Klasifikasi Kategori</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($klasifikasi as $index=>$k)
                    <tr>
                        <td class="fw-semibold">{{ $index + $klasifikasi->firstItem() }}</td>
                        <td><span class="badge badge-soft-info badge-pill-custom fs-6">{{ $k->kode_klasifikasi }}</span></td>
                        <td class="fw-semibold text-dark">{{ $k->nama_klasifikasi }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Ubah
                                </summary>
                                <form action="{{ route('klasifikasi.update', $k->id_klasifikasi) }}" method="POST" class="page-card p-3 mt-2 shadow-lg" style="min-width:280px; z-index:10;">
                                    @csrf @method('PUT')
                                    <label class="form-label fw-semibold small mb-1">Kode Klasifikasi</label>
                                    <input class="form-control mb-2" name="kode_klasifikasi" value="{{ $k->kode_klasifikasi }}" required>
                                    <label class="form-label fw-semibold small mb-1">Nama Klasifikasi</label>
                                    <input class="form-control mb-2" name="nama_klasifikasi" value="{{ $k->nama_klasifikasi }}" required>
                                    <button class="btn btn-sm btn-primary w-100 rounded-pill mt-2">Simpan Perubahan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('klasifikasi.destroy', $k->id_klasifikasi) }}" method="POST" onsubmit="return confirm('Hapus klasifikasi ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state py-5"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-muted"></i>Belum ada data klasifikasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $klasifikasi->links() }}</div>
@endsection