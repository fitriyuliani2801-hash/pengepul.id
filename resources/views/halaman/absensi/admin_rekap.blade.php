@extends('index')

@section('title', 'Rekapitulasi Absensi Karyawan (Admin)')

@section('isihalaman')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-clipboard-user text-primary"></i> Rekapitulasi Absensi Karyawan
            </h1>
            <p class="text-muted mb-0">Pantau kehadiran harian karyawan (`staff`, `driver`, `admin`), statistik ketepatan waktu, & bukti scan foto.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('absensi.scan') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                📸 Buka Scanner Wajah
            </a>
        </div>
    </div>

    <!-- SUMMARY STATISTIK HARI INI / PERIODE -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="page-card p-4 border-start border-4 border-success h-100">
                <div class="text-muted small fw-semibold text-uppercase">Total Hadir</div>
                <div class="fs-2 fw-bold text-success mt-1 mb-0">{{ $totalHadir }}</div>
                <small class="text-muted">Karyawan melakukan scan hari ini</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="page-card p-4 border-start border-4 border-primary h-100">
                <div class="text-muted small fw-semibold text-uppercase">Hadir Tepat Waktu</div>
                <div class="fs-2 fw-bold text-primary mt-1 mb-0">{{ $totalTepatWaktu }}</div>
                <small class="text-muted">Scan sebelum jam 08:00 WIB</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="page-card p-4 border-start border-4 border-danger h-100">
                <div class="text-muted small fw-semibold text-uppercase">Hadir Terlambat</div>
                <div class="fs-2 fw-bold text-danger mt-1 mb-0">{{ $totalTerlambat }}</div>
                <small class="text-muted">Scan setelah jam 08:00 WIB</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="page-card p-4 border-start border-4 border-warning h-100">
                <div class="text-muted small fw-semibold text-uppercase">Belum Absen</div>
                <div class="fs-2 fw-bold text-warning mt-1 mb-0">{{ $totalBelumAbsen }}</div>
                <small class="text-muted">Karyawan aktif belum scan</small>
            </div>
        </div>
    </div>

    <!-- FILTER PERIODE TANGGAL & ROLE -->
    <div class="page-card p-4 mb-4">
        <form action="{{ route('absensi.admin.rekap') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-dark">Pilih Tanggal Absensi</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" onchange="this.form.submit()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-dark">Filter Peran Karyawan</label>
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Peran (Staff, Driver, Admin) --</option>
                    <option value="staff" {{ $roleFilter === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="driver" {{ $roleFilter === 'driver' ? 'selected' : '' }}>Driver / Kurir</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill">Terapkan Filter</button>
                <a href="{{ route('absensi.admin.rekap') }}" class="btn btn-outline-secondary rounded-pill">Reset</a>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR HADIR -->
    <div class="page-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i> Daftar Hadir Tanggal {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMMM YYYY') }}</h5>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Karyawan</th>
                        <th>Peran / Jabatan</th>
                        <th>Jam Masuk & Foto</th>
                        <th>Status Masuk</th>
                        <th>Jam Pulang & Foto</th>
                        <th>Status Pulang</th>
                        <th>Kemiripan Wajah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensiList as $index => $abs)
                        <tr>
                            <td class="fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $abs->user->name ?? 'Karyawan' }}</div>
                                <small class="text-muted">{{ $abs->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-capitalize">{{ $abs->user->role ?? '-' }}</span>
                            </td>
                            <td>
                                @if($abs->jam_masuk)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($abs->foto_masuk)
                                            <img src="{{ asset($abs->foto_masuk) }}" class="rounded-3 border shadow-sm" style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;" onclick="previewFaceModal('{{ asset($abs->foto_masuk) }}', 'Masuk - {{ $abs->user->name ?? '' }}')">
                                        @endif
                                        <div class="fw-bold text-success">{{ $abs->jam_masuk }} WIB</div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($abs->jam_masuk)
                                    <span class="badge bg-{{ $abs->status_masuk === 'terlambat' ? 'danger' : 'success' }} px-3 py-1 text-capitalize">
                                        {{ $abs->status_masuk === 'terlambat' ? '⚠️ Terlambat' : '✓ Tepat Waktu' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($abs->jam_pulang)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($abs->foto_pulang)
                                            <img src="{{ asset($abs->foto_pulang) }}" class="rounded-3 border shadow-sm" style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;" onclick="previewFaceModal('{{ asset($abs->foto_pulang) }}', 'Pulang - {{ $abs->user->name ?? '' }}')">
                                        @endif
                                        <div class="fw-bold text-primary">{{ $abs->jam_pulang }} WIB</div>
                                    </div>
                                @else
                                    <span class="text-muted">Belum Pulang</span>
                                @endif
                            </td>
                            <td>
                                @if($abs->jam_pulang)
                                    <span class="badge bg-{{ $abs->status_pulang === 'pulang_cepat' ? 'warning' : 'success' }} px-3 py-1 text-capitalize">
                                        {{ $abs->status_pulang === 'pulang_cepat' ? '⚠️ Pulang Cepat' : '✓ Tepat Waktu' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark fw-bold px-2 py-1">{{ $abs->skor_kemiripan }}% Match</span>
                            </td>
                            <td>
                                @if($abs->latitude_masuk && $abs->longitude_masuk)
                                    <a href="https://www.google.com/maps?q={{ $abs->latitude_masuk }},{{ $abs->longitude_masuk }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:0.75rem;">
                                        📍 Peta GPS
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada data kehadiran pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL KARYAWAN BELUM ABSEN -->
    @if($belumAbsen->count() > 0)
        <div class="page-card p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-xmark text-danger me-2"></i> Karyawan Belum Absen Hari Ini ({{ $belumAbsen->count() }} Orang)</h5>
            <div class="row g-3">
                @foreach($belumAbsen as $kb)
                    <div class="col-sm-6 col-md-4 col-xl-3">
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3">
                            <div class="stat-icon-wrapper stat-icon-warning" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ $kb->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Peran: {{ ucfirst($kb->role) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Modal Pratinjau Foto Scan Wajah -->
<div class="modal fade" id="previewFaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold" id="previewFaceTitle">Foto Scan Wajah</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <img id="previewFaceImg" src="" class="img-fluid rounded border shadow" style="max-height: 380px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
    function previewFaceModal(src, title) {
        document.getElementById('previewFaceImg').src = src;
        document.getElementById('previewFaceTitle').textContent = 'Bukti Scan: ' + title;
        var modal = new bootstrap.Modal(document.getElementById('previewFaceModal'));
        modal.show();
    }
</script>
@endsection
