@extends('index')

@section('title', 'Riwayat Absensi Saya')

@section('isihalaman')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-history text-primary"></i> Riwayat Absensi Saya
            </h1>
            <p class="text-muted mb-0">Catatan histori kehadiran harian Anda lengkap dengan foto verifikasi scan wajah.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('absensi.scan') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                📸 Buka Scan Kamera
            </a>
        </div>
    </div>

    <div class="page-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Absensi</th>
                        <th>Jam Masuk & Foto</th>
                        <th>Status Masuk</th>
                        <th>Jam Pulang & Foto</th>
                        <th>Status Pulang</th>
                        <th>Kemiripan Wajah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $index => $abs)
                        <tr>
                            <td class="fw-bold">{{ $riwayat->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($abs->tgl_absensi)->isoFormat('D MMMM YYYY') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($abs->tgl_absensi)->format('l') }}</small>
                            </td>
                            <td>
                                @if($abs->jam_masuk)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($abs->foto_masuk)
                                            <img src="{{ asset($abs->foto_masuk) }}" class="rounded-3 border shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
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
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                @if($abs->jam_pulang)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($abs->foto_pulang)
                                            <img src="{{ asset($abs->foto_pulang) }}" class="rounded-3 border shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
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
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info text-dark fw-bold px-2 py-1">
                                    {{ $abs->skor_kemiripan }}% Match
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat absensi. Silakan lakukan Absen Scan Wajah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $riwayat->links() }}</div>
    </div>
</div>
@endsection
