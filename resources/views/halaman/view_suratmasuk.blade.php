@extends('index')
@section('title', 'Surat Masuk')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-inbox text-primary"></i> Surat Masuk
        </h1>
        <p class="text-muted mb-0">@if(auth()->user()->role === 'customer') Menampilkan surat milik Anda. @else Menampilkan seluruh arsip surat masuk. @endif</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="toggleCollapse('formTambahSuratMasuk')" aria-controls="formTambahSuratMasuk" aria-expanded="false">
        <i class="fa-solid fa-plus me-1"></i> Tambah Surat Masuk
    </button>
</div>

<div class="collapse mb-4" id="formTambahSuratMasuk">
    <div class="page-card p-4">
        <h2 class="h5 mb-3 fw-bold text-dark"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i> Formulir Tambah Surat Masuk</h2>
        <form action="{{ route('suratmasuk.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">No. Agenda</label>
                <input class="form-control" name="no_agenda" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">No. Surat</label>
                <input class="form-control" name="no_surat" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">Asal Surat</label>
                <input class="form-control" name="asal_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Tanggal Surat</label>
                <input class="form-control" type="date" name="tgl_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Tanggal Diterima</label>
                <input class="form-control" type="date" name="tgl_diterima" required>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold text-secondary small">Isi Ringkas</label>
                <textarea class="form-control" name="isi_ringkas" rows="2" required></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-secondary small">File PDF <span class="text-danger">*</span></label>
                <input class="form-control" type="file" name="file_surat" accept="application/pdf" required>
                <small class="text-muted">Wajib unggah file PDF (Maks 2MB).</small>
                <div class="file-feedback mt-1 small"></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                <input class="form-control" name="keterangan">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Status Progres</label>
                @if(auth()->user()->isAdmin())
                    <select class="form-select" name="status_proses">
                        <option value="baru">Baru</option>
                        <option value="diterima">Diterima</option>
                        <option value="sedang diproses">Sedang Diproses</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                @else
                    <input class="form-control" value="Baru" readonly>
                    <small class="text-muted">Status hanya dapat diubah oleh admin.</small>
                @endif
            </div>
            <div class="col-md-6">
                <div class="alert alert-info py-2 px-3 mb-0 h-100 d-flex align-items-center rounded-3">
                    <div><i class="fa-solid fa-user me-1"></i> <strong>Pengirim:</strong> {{ auth()->user()->name }} <span class="text-muted">({{ ucfirst(auth()->user()->role) }})</span></div>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Surat
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
                    <th>Agenda</th>
                    <th>Nomor Surat</th>
                    <th>Asal</th>
                    <th>Tanggal</th>
                    <th>Pengirim</th>
                    <th>Status Proses</th>
                    <th>File</th>
                    <th>Balasan</th>
                    <th>Keterangan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surat_masuk as $sm)
                    <tr>
                        <td class="fw-semibold">{{ $sm->no_agenda }}</td>
                        <td>{{ $sm->no_surat }}</td>
                        <td>{{ $sm->asal_surat }}</td>
                        <td><i class="fa-regular fa-calendar me-1 text-muted"></i> {{ $sm->tgl_diterima }}</td>
                        <td>
                            @php($pengirim = $sm->user_id ? App\Models\User::find($sm->user_id) : null)
                            @if($pengirim)
                                <div class="fw-semibold text-dark">{{ $pengirim->name }}</div>
                                <small class="text-muted text-capitalize">{{ $pengirim->role }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php($status = $sm->status_proses ?? 'baru')
                            @php($badgeClass = match($status){'diterima'=>'badge-soft-success','sedang diproses'=>'badge-soft-warning','ditolak'=>'badge-soft-danger','dibatalkan'=>'badge-soft-danger', default=>'badge-soft-primary'})
                            <span class="badge {{ $badgeClass }} badge-pill-custom text-capitalize">{{ str_replace('-', ' ', $status) }}</span>
                            @if($sm->diproses_oleh)
                                @php($admin = App\Models\User::find($sm->diproses_oleh))
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;"><i class="fa-solid fa-user-check me-1"></i> {{ $admin?->name ?? '-' }}</div>
                            @endif
                        </td>
                        <td>
                            @if($sm->file_surat)
                                <a href="{{ asset('storage/'.$sm->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-file-pdf me-1 text-danger"></i> PDF
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($sm->file_balasan)
                                <a href="{{ asset('storage/'.$sm->file_balasan) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-file-circle-check me-1"></i> Balasan
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $sm->keterangan ?: '—' }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Ubah
                                </summary>
                                <form action="{{ route('suratmasuk.update', $sm->id_surat_masuk) }}" method="POST" enctype="multipart/form-data" class="page-card p-3 mt-2 shadow-lg" style="min-width:320px; z-index: 10;">
                                    @csrf @method('PUT')
                                    <label class="form-label fw-semibold small mb-1">No. Agenda</label>
                                    <input class="form-control mb-2" name="no_agenda" value="{{ $sm->no_agenda }}" required>
                                    <label class="form-label fw-semibold small mb-1">No. Surat</label>
                                    <input class="form-control mb-2" name="no_surat" value="{{ $sm->no_surat }}" required>
                                    <label class="form-label fw-semibold small mb-1">Asal Surat</label>
                                    <input class="form-control mb-2" name="asal_surat" value="{{ $sm->asal_surat }}" required>
                                    <label class="form-label fw-semibold small mb-1">Isi Ringkas</label>
                                    <textarea class="form-control mb-2" name="isi_ringkas" required>{{ $sm->isi_ringkas }}</textarea>
                                    <label class="form-label fw-semibold small mb-1">Tanggal Surat</label>
                                    <input class="form-control mb-2" type="date" name="tgl_surat" value="{{ $sm->tgl_surat }}" required>
                                    <label class="form-label fw-semibold small mb-1">Tanggal Diterima</label>
                                    <input class="form-control mb-2" type="date" name="tgl_diterima" value="{{ $sm->tgl_diterima }}" required>
                                    <label class="form-label fw-semibold small mb-1">File PDF</label>
                                    <input class="form-control mb-2" type="file" name="file_surat" accept="application/pdf">
                                    <small class="text-muted d-block mb-2" style="font-size: 0.72rem;">Kosongkan jika tidak memperbarui PDF.</small>
                                    <div class="file-feedback mb-2 small"></div>
                                    @if(auth()->user()->hasRole('admin', 'staff'))
                                        <label class="form-label fw-semibold small mb-1">File Balasan (PDF)</label>
                                        <input class="form-control mb-2" type="file" name="file_balasan" accept="application/pdf">
                                        <small class="text-muted d-block mb-2" style="font-size: 0.72rem;">Unggah file balasan jika ingin mengirimkan balasan.</small>
                                        <div class="file-feedback mb-2 small"></div>
                                    @endif
                                    <label class="form-label fw-semibold small mb-1">Keterangan</label>
                                    <input class="form-control mb-2" name="keterangan" value="{{ $sm->keterangan }}">
                                    <label class="form-label fw-semibold small mb-1">Status Progres</label>
                                    @if(auth()->user()->isAdmin())
                                        <select class="form-select mb-2" name="status_proses">
                                            <option value="baru" {{ ($sm->status_proses ?? 'baru') === 'baru' ? 'selected' : '' }}>Baru</option>
                                            <option value="diterima" {{ ($sm->status_proses ?? 'baru') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                            <option value="sedang diproses" {{ ($sm->status_proses ?? 'baru') === 'sedang diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                            <option value="ditolak" {{ ($sm->status_proses ?? 'baru') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            <option value="dibatalkan" {{ ($sm->status_proses ?? 'baru') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                    @else
                                        <input class="form-control mb-2" value="{{ ucwords($sm->status_proses ?? 'baru') }}" readonly>
                                    @endif
                                    <button class="btn btn-sm btn-primary w-100 mt-2 rounded-pill">Simpan Perubahan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('suratmasuk.destroy', $sm->id_surat_masuk) }}" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="empty-state py-5"><i class="fa-solid fa-inbox fs-2 mb-2 d-block text-muted"></i>Belum ada data surat masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $surat_masuk->links() }}</div>
@endsection
