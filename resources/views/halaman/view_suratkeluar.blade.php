@extends('index')
@section('title', 'Surat Keluar')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-paper-plane text-primary"></i> Surat Keluar
        </h1>
        <p class="text-muted mb-0">@if(auth()->user()->role === 'customer') Menampilkan surat milik Anda. @else Menampilkan seluruh arsip surat keluar. @endif</p>
    </div>
    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="toggleCollapse('formTambahSuratKeluar')" aria-controls="formTambahSuratKeluar" aria-expanded="false">
        <i class="fa-solid fa-plus me-1"></i> Tambah Surat Keluar
    </button>
</div>

<div class="collapse mb-4" id="formTambahSuratKeluar">
    <div class="page-card p-4">
        <h2 class="h5 mb-3 fw-bold text-dark"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i> Formulir Tambah Surat Keluar</h2>
        <form action="{{ route('suratkeluar.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
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
                <label class="form-label fw-semibold text-secondary small">Tujuan Surat</label>
                <input class="form-control" name="tujuan_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Tanggal Surat</label>
                <input class="form-control" type="date" name="tgl_surat" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">File PDF / Dokumen Surat Jalan <span class="text-muted">(Opsional)</span></label>
                <input class="form-control" type="file" name="file_surat" accept="application/pdf">
                <small class="text-muted">Upload file PDF jika ada (opsional).</small>
                <div class="file-feedback mt-1 small"></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Isi Ringkas</label>
                <textarea class="form-control" name="isi_ringkas" rows="2" required></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                <input class="form-control" name="keterangan">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">Status Progres</label>
                <select class="form-select" name="status_proses">
                    <option value="baru">Baru</option>
                    <option value="diterima">Diterima</option>
                    <option value="sedang diproses">Sedang Diproses</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
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
                    @php
                        $pengirim = $sk->user_id ? App\Models\User::find($sk->user_id) : null;
                        $status = $sk->status_proses ?? 'baru';
                        $badgeClass = match($status) {
                            'diterima' => 'badge-soft-success',
                            'sedang diproses' => 'badge-soft-warning',
                            'ditolak' => 'badge-soft-danger',
                            'dibatalkan' => 'badge-soft-danger',
                            default => 'badge-soft-primary'
                        };
                        
                        $orderPenjemputan = null;
                        if (!empty($sk->id_surat_keluar)) {
                            $orderPenjemputan = \App\Models\PenjemputanOrder::where('id_surat_keluar', $sk->id_surat_keluar)->first();
                        }
                        if (!$orderPenjemputan && !empty($sk->no_surat) && str_contains($sk->no_surat, 'SJ/JEMPUT/')) {
                            $orderNo = str_replace('SJ/JEMPUT/', '', $sk->no_surat);
                            $orderPenjemputan = \App\Models\PenjemputanOrder::where('order_no', $orderNo)->first();
                        }
                        $distSupplier = null;
                        if (!$orderPenjemputan) {
                            $distSupplier = \App\Models\DistribusiSupplier::where('id_surat_keluar', $sk->id_surat_keluar)->orWhere('no_surat_jalan', $sk->no_surat)->first();
                        }
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $sk->no_agenda }}</td>
                        <td>{{ $sk->no_surat }}</td>
                        <td>{{ $sk->tujuan_surat }}</td>
                        <td><i class="fa-regular fa-calendar me-1 text-muted"></i> {{ $sk->tgl_surat }}</td>
                        <td>
                            @if($pengirim)
                                <div class="fw-semibold text-dark">{{ $pengirim->name }}</div>
                                <small class="text-muted text-capitalize">{{ $pengirim->role }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }} badge-pill-custom text-capitalize">{{ str_replace('-', ' ', $status) }}</span>
                            @if($sk->diproses_oleh)
                                @php($admin = App\Models\User::find($sk->diproses_oleh))
                                <div class="small text-muted mt-1" style="font-size: 0.72rem;"><i class="fa-solid fa-user-check me-1"></i> {{ $admin?->name ?? '-' }}</div>
                            @endif
                        </td>
                        <td>
                            @if(!empty($orderPenjemputan))
                                <button type="button" class="btn btn-sm btn-primary text-white fw-bold shadow-sm rounded-pill px-3 py-1" style="font-size:0.78rem;" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $orderPenjemputan->id) }}')">
                                    <i class="fa-solid fa-scroll me-1"></i> Surat Jalan Warga
                                </button>
                            @elseif(!empty($distSupplier))
                                <button type="button" class="btn btn-sm btn-primary text-white fw-bold shadow-sm rounded-pill px-3 py-1" style="font-size:0.78rem;" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $distSupplier->id_surat_keluar ?: $distSupplier->id) }}')">
                                    <i class="fa-solid fa-truck-ramp-box me-1"></i> Surat Jalan Supplier
                                </button>
                            @elseif(!empty($sk->file_surat))
                                <a href="{{ asset('storage/'.$sk->file_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                    <i class="fa-solid fa-file-pdf me-1 text-danger"></i> Buka PDF
                                </a>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $sk->id_surat_keluar) }}')">
                                    <i class="fa-solid fa-scroll me-1"></i> Surat Jalan
                                </button>
                            @endif
                        </td>
                        <td>{{ $sk->keterangan ?? '—' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                @if(auth()->user()->hasRole('admin', 'staff'))
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;" onclick="toggleCollapse('editSuratKeluarModal-{{ $sk->id_surat_keluar }}')">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('suratkeluar.destroy', $sk->id_surat_keluar) }}" method="POST" onsubmit="return confirm('Hapus surat keluar ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" style="font-size:0.78rem;">
                                            <i class="fa-solid fa-trash me-1"></i> Hapus
                                        </button>
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
                                        <h5 class="h6 mb-3 fw-bold text-dark"><i class="fa-solid fa-pen-to-square me-1 text-primary"></i> Edit Surat Keluar #{{ $sk->no_surat }}</h5>
                                        <form action="{{ route('suratkeluar.update', $sk->id_surat_keluar) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                                            @csrf @method('PUT')
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">No. Agenda</label>
                                                <input class="form-control" name="no_agenda" value="{{ $sk->no_agenda }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">No. Surat</label>
                                                <input class="form-control" name="no_surat" value="{{ $sk->no_surat }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold small mb-1">Tujuan Surat</label>
                                                <input class="form-control" name="tujuan_surat" value="{{ $sk->tujuan_surat }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small mb-1">Tanggal Surat</label>
                                                <input class="form-control" type="date" name="tgl_surat" value="{{ $sk->tgl_surat }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small mb-1">Ganti File Surat (PDF)</label>
                                                <input class="form-control" type="file" name="file_surat" accept="application/pdf">
                                                <small class="text-muted" style="font-size: 0.72rem;">Biarkan kosong jika tidak memperbarui file.</small>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small mb-1">Isi Ringkas</label>
                                                <textarea class="form-control" name="isi_ringkas" rows="2" required>{{ $sk->isi_ringkas }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold small mb-1">Keterangan</label>
                                                <input class="form-control" name="keterangan" value="{{ $sk->keterangan }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small mb-1">Status Progres</label>
                                                <select class="form-select" name="status_proses">
                                                    <option value="baru" {{ $sk->status_proses==='baru'?'selected':'' }}>Baru</option>
                                                    <option value="diterima" {{ $sk->status_proses==='diterima'?'selected':'' }}>Diterima</option>
                                                    <option value="sedang diproses" {{ $sk->status_proses==='sedang diproses'?'selected':'' }}>Sedang Diproses</option>
                                                    <option value="ditolak" {{ $sk->status_proses==='ditolak'?'selected':'' }}>Ditolak</option>
                                                    <option value="dibatalkan" {{ $sk->status_proses==='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                                                </select>
                                            </div>
                                            <div class="col-12 d-flex gap-2 mt-3">
                                                <button class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                                <button type="button" class="btn btn-secondary rounded-pill px-4" onclick="toggleCollapse('editSuratKeluarModal-{{ $sk->id_surat_keluar }}')">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" class="empty-state py-5"><i class="fa-solid fa-paper-plane fs-2 mb-2 d-block text-muted"></i>Belum ada data surat keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Preview Surat Jalan -->
<div class="modal fade" id="suratJalanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-scroll text-warning me-2"></i> Surat Jalan Penjemputan Sampah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <iframe id="suratJalanIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" onclick="printSuratJalanIframe()">
                    <i class="fa-solid fa-print me-1"></i> Cetak / Print Dokumen
                </button>
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openSuratJalanModal(url) {
        document.getElementById('suratJalanIframe').src = url;
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#suratJalanModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('suratJalanModal')) || new bootstrap.Modal(document.getElementById('suratJalanModal'));
            modal.show();
        } else {
            var el = document.getElementById('suratJalanModal');
            if (el) {
                el.classList.add('show');
                el.style.display = 'block';
            }
        }
    }

    function printSuratJalanIframe() {
        var iframe = document.getElementById('suratJalanIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }
</script>
@endsection
