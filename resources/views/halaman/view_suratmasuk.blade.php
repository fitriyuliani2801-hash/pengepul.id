@extends('index')
@section('title', 'Surat Masuk')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Surat Masuk</h1>
        <p class="text-muted mb-0">@if(auth()->user()->role === 'customer') Menampilkan surat milik Anda. @else Menampilkan seluruh surat masuk. @endif</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="toggleCollapse('formTambahSuratMasuk')" aria-controls="formTambahSuratMasuk" aria-expanded="false">+ Tambah Surat</button>
</div>

<div class="collapse mb-4" id="formTambahSuratMasuk">
    <div class="page-card p-4">
        <h2 class="h5 mb-3">Tambah Surat Masuk</h2>
        <form action="{{ route('suratmasuk.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">No. Agenda</label>
                <input class="form-control" name="no_agenda" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">No. Surat</label>
                <input class="form-control" name="no_surat" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Asal Surat</label>
                <input class="form-control" name="asal_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Surat</label>
                <input class="form-control" type="date" name="tgl_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Diterima</label>
                <input class="form-control" type="date" name="tgl_diterima" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Isi Ringkas</label>
                <textarea class="form-control" name="isi_ringkas" rows="2" required></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">File PDF <span class="text-danger">*</span></label>
                <input class="form-control" type="file" name="file_surat" accept="application/pdf" required>
                <small class="text-muted">Wajib upload file PDF.</small>
                <div class="file-feedback mt-1 small"></div>
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <input class="form-control" name="keterangan">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status Progres</label>
                @if(auth()->user()->isAdmin())
                    <select class="form-select" name="status_proses">
                        <option value="baru">Baru</option>
                        <option value="diterima">Diterima</option>
                        <option value="sedang diproses">Sedang Diproses</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                @else
                    <input class="form-control" value="Baru" readonly>
                    <small class="text-muted">Status hanya dapat diubah oleh admin.</small>
                @endif
            </div>
            <div class="col-md-6">
                <div class="alert alert-info py-2 px-3 mb-0 h-100 d-flex align-items-center">
                    <div><strong>Pengirim:</strong> {{ auth()->user()->name }} <span class="text-muted">({{ ucfirst(auth()->user()->role) }})</span></div>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Simpan Surat</button>
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
                        <td>{{ $sm->no_agenda }}</td>
                        <td>{{ $sm->no_surat }}</td>
                        <td>{{ $sm->asal_surat }}</td>
                        <td>{{ $sm->tgl_diterima }}</td>
                        <td>
                            @php($pengirim = $sm->user_id ? App\Models\User::find($sm->user_id) : null)
                            @if($pengirim)
                                <div class="fw-semibold">{{ $pengirim->name }}</div>
                                <small class="text-muted text-capitalize">{{ $pengirim->role }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php($status = $sm->status_proses ?? 'baru')
                            @php($badge = match($status){'diterima'=>'success','sedang diproses'=>'warning','ditolak'=>'danger', default=>'secondary'})
                            <span class="badge bg-{{ $badge }} text-capitalize">{{ str_replace('-', ' ', $status) }}</span>
                            @if($sm->diproses_oleh)
                                @php($admin = App\Models\User::find($sm->diproses_oleh))
                                <div class="small text-muted mt-1">Oleh: {{ $admin?->name ?? '-' }}</div>
                            @endif
                        </td>
                        <td>
                            @if($sm->file_surat)
                                <a href="{{ asset('storage/'.$sm->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Buka PDF</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($sm->file_balasan)
                                <a href="{{ asset('storage/'.$sm->file_balasan) }}" target="_blank" class="btn btn-sm btn-outline-success">Buka Balasan</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $sm->keterangan ?: '—' }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary">Ubah</summary>
                                <form action="{{ route('suratmasuk.update', $sm->id_surat_masuk) }}" method="POST" enctype="multipart/form-data" class="page-card p-3 mt-2" style="min-width:300px">
                                    @csrf @method('PUT')
                                    <input class="form-control mb-2" name="no_agenda" value="{{ $sm->no_agenda }}" required>
                                    <input class="form-control mb-2" name="no_surat" value="{{ $sm->no_surat }}" required>
                                    <input class="form-control mb-2" name="asal_surat" value="{{ $sm->asal_surat }}" required>
                                    <textarea class="form-control mb-2" name="isi_ringkas" required>{{ $sm->isi_ringkas }}</textarea>
                                    <input class="form-control mb-2" type="date" name="tgl_surat" value="{{ $sm->tgl_surat }}" required>
                                    <input class="form-control mb-2" type="date" name="tgl_diterima" value="{{ $sm->tgl_diterima }}" required>
                                    <input class="form-control mb-2" type="file" name="file_surat" accept="application/pdf">
                                    <small class="text-muted d-block mb-2">Kosongkan jika tidak ingin memperbarui file PDF.</small>
                                    <div class="file-feedback mb-2 small"></div>
                                    @if(auth()->user()->hasRole('admin', 'staff'))
                                        <label class="form-label">File Balasan (PDF)</label>
                                        <input class="form-control mb-2" type="file" name="file_balasan" accept="application/pdf">
                                        <small class="text-muted d-block mb-2">Unggah file balasan jika ingin mengirimkan/memperbarui balasan.</small>
                                        <div class="file-feedback mb-2 small"></div>
                                    @endif
                                    <input class="form-control mb-2" name="keterangan" value="{{ $sm->keterangan }}">
                                    <label class="form-label">Status Progres</label>
                                    @if(auth()->user()->isAdmin())
                                        <select class="form-select mb-2" name="status_proses">
                                            <option value="baru" {{ ($sm->status_proses ?? 'baru') === 'baru' ? 'selected' : '' }}>Baru</option>
                                            <option value="diterima" {{ ($sm->status_proses ?? 'baru') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                            <option value="sedang diproses" {{ ($sm->status_proses ?? 'baru') === 'sedang diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                            <option value="ditolak" {{ ($sm->status_proses ?? 'baru') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    @else
                                        <input class="form-control mb-2" value="{{ ucwords($sm->status_proses ?? 'baru') }}" readonly>
                                        <small class="text-muted d-block mb-2">Status hanya dapat diubah oleh admin.</small>
                                    @endif
                                    <div class="alert alert-info py-2 px-3 mb-2">
                                        <strong>Pengirim saat ini:</strong> {{ $sm->user_id ? App\Models\User::find($sm->user_id)->name : '-' }} <span class="text-muted">({{ $sm->user_id ? ucfirst(App\Models\User::find($sm->user_id)->role) : '-' }})</span>
                                    </div>
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('suratmasuk.destroy', $sm->id_surat_masuk) }}" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    {{--
                            @if($pengirim)
                                <div class="fw-semibold">{{ $pengirim->name }}</div>
                                <small class="text-muted text-capitalize">{{ $pengirim->role }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($sm->file_surat)
                                <a href="{{ asset('storage/'.$sm->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Buka PDF</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $sm->keterangan ?: '—' }}</td>
                        <td class="text-end">
                            <details class="d-inline-block text-start">
                                <summary class="btn btn-sm btn-outline-primary">Ubah</summary>
                                <form action="{{ route('suratmasuk.update', $sm->id_surat_masuk) }}" method="POST" enctype="multipart/form-data" class="page-card p-3 mt-2" style="min-width:300px">
                                    @csrf @method('PUT')
                                    <input class="form-control mb-2" name="no_agenda" value="{{ $sm->no_agenda }}" required>
                                    <input class="form-control mb-2" name="no_surat" value="{{ $sm->no_surat }}" required>
                                    <input class="form-control mb-2" name="asal_surat" value="{{ $sm->asal_surat }}" required>
                                    <textarea class="form-control mb-2" name="isi_ringkas" required>{{ $sm->isi_ringkas }}</textarea>
                                    <input class="form-control mb-2" type="date" name="tgl_surat" value="{{ $sm->tgl_surat }}" required>
                                    <input class="form-control mb-2" type="date" name="tgl_diterima" value="{{ $sm->tgl_diterima }}" required>
                                    <input class="form-control mb-2" type="file" name="file_surat" accept="application/pdf">
                                    <small class="text-muted d-block mb-2">Kosongkan jika tidak ingin memperbarui file PDF.</small>
                                    <input class="form-control mb-2" name="keterangan" value="{{ $sm->keterangan }}">
                                    <label class="form-label">Status Progres</label>
                                    <select class="form-select mb-2" name="status_proses">
                                        <option value="baru" {{ ($sm->status_proses ?? 'baru') === 'baru' ? 'selected' : '' }}>Baru</option>
                                        <option value="diterima" {{ ($sm->status_proses ?? 'baru') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                                        <option value="sedang diproses" {{ ($sm->status_proses ?? 'baru') === 'sedang diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                        <option value="ditolak" {{ ($sm->status_proses ?? 'baru') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    <div class="alert alert-info py-2 px-3 mb-2">
                                        <strong>Pengirim saat ini:</strong> {{ $sm->user_id ? App\Models\User::find($sm->user_id)->name : '-' }} <span class="text-muted">({{ $sm->user_id ? ucfirst(App\Models\User::find($sm->user_id)->role) : '-' }})</span>
                                    </div>
                                    <button class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </details>
                            <form class="d-inline" action="{{ route('suratmasuk.destroy', $sm->id_surat_masuk) }}" method="POST" onsubmit="return confirm('Hapus surat ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    --}}
                @empty
                    <tr><td colspan="10" class="empty-state">Belum ada surat masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $surat_masuk->links() }}</div>
@endsection
