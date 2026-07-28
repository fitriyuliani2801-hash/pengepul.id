@extends('index')

@section('title', 'Menu Utama')
@section('isihalaman')
@php($user = auth()->user())
<div class="container-fluid px-0">
    <!-- Welcome Hero Banner -->
    <div class="p-4 p-md-5 mb-4 text-white hero-gradient-bg position-relative overflow-hidden" style="border-radius: var(--radius-xl); box-shadow: var(--shadow-lg);">
        <div class="position-relative z-1">
            <span class="badge bg-white bg-opacity-25 text-white text-uppercase fw-semibold mb-3 px-3 py-2 rounded-pill" style="letter-spacing: 0.08em; font-size: 0.72rem;">
                <i class="fa-solid fa-shield-halved me-1"></i> Peran: {{ ucfirst($user->role) }}
            </span>
            <h1 class="display-6 mb-2 fw-extrabold text-white">Selamat Datang, <span style="color: #38bdf8;">{{ $user->name }}</span>! 👋</h1>
            <p class="text-white-50 mb-0 lead" style="font-size: 1.05rem; max-width: 650px;">
                Kelola aktivitas persuratan organisasi dan transaksi daur ulang sampah digital Anda secara efisien dalam satu tempat.
            </p>
        </div>
    </div>

    <!-- GRUP 1: SISTEM PERSURATAN -->
    @if($user->hasRole('admin', 'staff', 'customer'))
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                    <span class="stat-icon-wrapper stat-icon-primary" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </span>
                    Sistem Persuratan Digital
                </h2>
            </div>
            
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('suratmasuk.index') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-primary badge-pill-custom">Arsip Masuk</span>
                                <div class="stat-icon-wrapper stat-icon-primary">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Surat Masuk</h3>
                            <p class="text-muted small mb-0">Kelola surat yang diterima dan pantau proses verifikasinya.</p>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('suratkeluar.index') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-success badge-pill-custom">Arsip Keluar</span>
                                <div class="stat-icon-wrapper stat-icon-success">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Surat Keluar</h3>
                            <p class="text-muted small mb-0">Catat, kirim, dan pantau arsip surat yang dikirimkan.</p>
                        </div>
                    </a>
                </div>

                @if($user->hasRole('admin', 'staff'))
                    <div class="col-12 col-sm-6 col-xl-3">
                        <a href="{{ route('disposisi.index') }}" class="text-decoration-none">
                            <div class="dashboard-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge badge-soft-warning badge-pill-custom">Tindak Lanjut</span>
                                    <div class="stat-icon-wrapper stat-icon-warning">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </div>
                                </div>
                                <h3 class="h5 text-dark mb-1 fw-bold">Disposisi</h3>
                                <p class="text-muted small mb-0">Atur penugasan, instruksi kerja, dan batas waktu surat.</p>
                            </div>
                        </a>
                    </div>
                @endif

                @if($user->isAdmin())
                    <div class="col-12 col-sm-6 col-xl-3">
                        <a href="{{ route('klasifikasi.index') }}" class="text-decoration-none">
                            <div class="dashboard-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge badge-soft-info badge-pill-custom">Kategori</span>
                                    <div class="stat-icon-wrapper stat-icon-info">
                                        <i class="fa-solid fa-folder-tree"></i>
                                    </div>
                                </div>
                                <h3 class="h5 text-dark mb-1 fw-bold">Klasifikasi</h3>
                                <p class="text-muted small mb-0">Kelola kode dan indeks kategori surat secara terpusat.</p>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- GRUP 2: PENGEPUL DIGITAL -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="stat-icon-wrapper stat-icon-success" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="fa-solid fa-recycle"></i>
                </span>
                Pengepul Digital & Daur Ulang
            </h2>
        </div>

        <div class="row g-4">
            @if($user->hasRole('customer'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.warga.index') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-success badge-pill-custom">Layanan Warga</span>
                                <div class="stat-icon-wrapper stat-icon-success">
                                    <i class="fa-solid fa-truck-ramp-box"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Jual Sampah (Warga)</h3>
                            <p class="text-muted small mb-0">Cek harga terkini, kumpulkan item, tentukan lokasi penjemputan & checkout.</p>
                        </div>
                    </a>
                </div>
            @endif

            @if($user->hasRole('admin', 'staff'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.admin.index') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-primary badge-pill-custom">Distribusi Supplier</span>
                                <div class="stat-icon-wrapper stat-icon-primary">
                                    <i class="fa-solid fa-industry"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Distribusi & Penjualan ke Supplier</h3>
                            <p class="text-muted small mb-0">Kelola stok gudang, penerimaan dari warga, dan distribusikan/jual material ke supplier/pabrik.</p>
                        </div>
                    </a>
                </div>
            @endif

            @if($user->hasRole('driver'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.driver.index') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-warning badge-pill-custom">Kurir Area</span>
                                <div class="stat-icon-wrapper stat-icon-warning">
                                    <i class="fa-solid fa-truck-moving"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Tugas Kurir (Driver)</h3>
                            <p class="text-muted small mb-0">Pantau rute penjemputan, catat timbangan akhir di lokasi & bayar ke warga.</p>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- GRUP 3: ABSENSI KARYAWAN -->
    @if($user->hasRole('admin', 'staff', 'driver'))
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h5 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                    <span class="stat-icon-wrapper stat-icon-info" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="fa-solid fa-camera-retro"></i>
                    </span>
                    Absensi & Presensi Wajah Karyawan
                </h2>
            </div>

            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('absensi.scan') }}" class="text-decoration-none">
                        <div class="dashboard-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-soft-primary badge-pill-custom">Absen Wajah</span>
                                <div class="stat-icon-wrapper stat-icon-primary">
                                    <i class="fa-solid fa-face-smile"></i>
                                </div>
                            </div>
                            <h3 class="h5 text-dark mb-1 fw-bold">Absensi Scan Wajah</h3>
                            <p class="text-muted small mb-0">Lakukan Absen Masuk atau Absen Pulang dengan verifikasi kamera scan wajah & GPS.</p>
                        </div>
                    </a>
                </div>

                @if($user->hasRole('admin', 'staff'))
                    <div class="col-12 col-sm-6 col-xl-4">
                        <a href="{{ route('absensi.admin.rekap') }}" class="text-decoration-none">
                            <div class="dashboard-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge badge-soft-warning badge-pill-custom">Rekap Kehadiran</span>
                                    <div class="stat-icon-wrapper stat-icon-warning">
                                        <i class="fa-solid fa-clipboard-user"></i>
                                    </div>
                                </div>
                                <h3 class="h5 text-dark mb-1 fw-bold">Rekap Absensi Karyawan</h3>
                                <p class="text-muted small mb-0">Pantau rekapitulasi ketepatan waktu, foto bukti scan, dan status kehadiran harian.</p>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- GRUP 4: PENGATURAN & PROFIL -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 text-dark fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="stat-icon-wrapper stat-icon-purple" style="width: 36px; height: 36px; font-size: 1.1rem;">
                    <i class="fa-solid fa-gear"></i>
                </span>
                Pengaturan Akun
            </h2>
        </div>

        <div class="row g-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <a href="{{ route('profil.index') }}" class="text-decoration-none">
                    <div class="dashboard-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge badge-soft-info badge-pill-custom">Pengguna</span>
                            <div class="stat-icon-wrapper stat-icon-purple">
                                <i class="fa-solid fa-user-gear"></i>
                            </div>
                        </div>
                        <h3 class="h5 text-dark mb-1 fw-bold">Profil Saya</h3>
                        <p class="text-muted small mb-0">Perbarui identitas akun, ubah kata sandi, dan rekening pembayaran digital.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection