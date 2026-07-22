@extends('index')
@section('title', 'Surat Keluar')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Surat Keluar</h1>
        <p class="text-muted mb-0">@if(auth()->user()->role === 'customer') Menampilkan surat milik Anda. @else Menampilkan seluruh surat keluar. @endif</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="toggleCollapse('formTambahSuratKeluar')" aria-controls="formTambahSuratKeluar" aria-expanded="false">+ Tambah Surat</button>
</div>

<div class="collapse mb-4" id="formTambahSuratKeluar">
    <div class="page-card p-4">
        <h2 class="h5 mb-3">Tambah Surat Keluar</h2>
        <form action="{{ route('suratkeluar.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
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
                <label class="form-label">Tujuan Surat</label>
                <input class="form-control" name="tujuan_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Surat</label>
                <input class="form-control" type="date" name="tgl_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">File PDF <span class="text-danger">*</span></label>
                <input class="form-control" type="file" name="file_surat" accept="application/pdf" required>
                <small class="text-muted">Wajib upload PDF.</small>
                <div class="file-feedback mt-1 small"></div>
            </div>
            <div class="col-12">
                <label class="form-label">Isi Ringkas</label>
                <textarea class="form-control" name="isi_ringkas" rows="2" required></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Keterangan</label>
                <input class="form-control" name="keterangan">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status Progres</label>
                <select class="form-select" name="status_proses">
                    <option value="baru">Baru</option>
                    <option value="diterima">Diterima</option>
                    <option value="sedang diproses">Sedang Diproses</option>
                    <option value="ditolak">Ditolak</option>
                </select>
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
                    <th>Tujuan</th>
                    <th>Tanggal</th>
                    <th>Pengirim</th>
                    <th>Status Proses</th>
                    <th>File Surat</th>
                    <th>Keterangan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surat_keluar as $sk)
                    <tr>
                        <td>{{ $sk->no_agenda }}</td>
                        <td>{{ $sk->no_surat }}</td>
                        <td>{{ $sk->tujuan_surat }}</td>
                        <td>{{ $sk->tgl_surat }}</td>
                        <td>
                            @php($pengirim = $sk->user_id ? App\Models\User::find($sk->user_id) : null)
                            @if($pengirim)
                                <div class="fw-semibold">{{ $pengirim->name }}</div>
                                <small class="text-muted text-capitalize">{{ $pengirim->role }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php($status = $sk->status_proses ?? 'baru')
                            @php($badge = match($status){'diterima'=>'success','sedang diproses'=>'warning','ditolak'=>'danger', default=>'secondary'})
                            <span class="badge bg-{{ $badge }} text-capitalize">{{ str_replace('-', ' ', $status) }}</span>
                            @if($sk->diproses_oleh)
                                @php($admin = App\Models\User::find($sk->diproses_oleh))
                                <div class="small text-muted mt-1">Oleh: {{ $admin?->name ?? '-' }}</div>
                            @endif
                        </td>
                        <td>
                            @if($sk->file_surat)
                                <a href="{{ asset('storage/'.$sk->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Buka PDF</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $sk->keterangan ?? '—' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                @if(auth()->user()->hasRole('admin', 'staff'))
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleCollapse('editSuratKeluarModal-{{ $sk->id_surat_keluar }}')">Edit</button>
                                    <form action="{{ route('suratkeluar.destroy', $sk->id_surat_keluar) }}" method="POST" onsubmit="return confirm('Hapus surat keluar ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Hanya Lihat</span>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @if(auth()->user()->hasRole('admin', 'staff'))
                        <tr class="p-0 border-0">
                            <td colspan="9" class="p-0 border-0">
                                <div class="collapse" id="editSuratKeluarModal-{{ $sk->id_surat_keluar }}">
                                    <div class="p-4 bg-light border-bottom">
                                        <h5 class="h6 mb-3">Edit Surat Keluar #{{ $sk->no_surat }}</h5>
                                        <form action="{{ route('suratkeluar.update', $sk->id_surat_keluar) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                                            @csrf @method('PUT')
                                            <div class="col-md-4">
                                                <label class="form-label">No. Agenda</label>
                                                <input class="form-control" name="no_agenda" value="{{ $sk->no_agenda }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">No. Surat</label>
                                                <input class="form-control" name="no_surat" value="{{ $sk->no_surat }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tujuan Surat</label>
                                                <input class="form-control" name="tujuan_surat" value="{{ $sk->tujuan_surat }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Tanggal Surat</label>
                                                <input class="form-control" type="date" name="tgl_surat" value="{{ $sk->tgl_surat }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ganti File Surat (PDF)</label>
                                                <input class="form-control" type="file" name="file_surat" accept="application/pdf">
                                                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file.</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Isi Ringkas</label>
                                                <textarea class="form-control" name="isi_ringkas" rows="2" required>{{ $sk->isi_ringkas }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Keterangan</label>
                                                <input class="form-control" name="keterangan" value="{{ $sk->keterangan }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status Progres</label>
                                                <select class="form-select" name="status_proses">
                                                    <option value="baru" {{ $sk->status_proses==='baru'?'selected':'' }}>Baru</option>
                                                    <option value="diterima" {{ $sk->status_proses==='diterima'?'selected':'' }}>Diterima</option>
                                                    <option value="sedang diproses" {{ $sk->status_proses==='sedang diproses'?'selected':'' }}>Sedang Diproses</option>
                                                    <option value="ditolak" {{ $sk->status_proses==='ditolak'?'selected':'' }}>Ditolak</option>
                                                </select>
                                            </div>
                                            <div class="col-12 d-flex gap-2">
                                                <button class="btn btn-primary">Simpan Perubahan</button>
                                                <button type="button" class="btn btn-secondary" onclick="toggleCollapse('editSuratKeluarModal-{{ $sk->id_surat_keluar }}')">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data surat keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    {{ $surat_keluar->links() }}
</div>
@endsection
