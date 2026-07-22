@extends('index')

@section('title', 'Menu Utama')
@section('isihalaman')
@php($user = auth()->user())
<div class="container-fluid px-0">
    <!-- Welcome Banner (Tanpa Kotak Putih / page-card) -->
    <div class="p-4 mb-4 text-white" style="background: linear-gradient(135deg, #12355b, #1c6ea4); border-radius: 1.25rem; border: 0; box-shadow: 0 4px 15px rgba(18, 53, 91, 0.15);">
        <span class="badge bg-white bg-opacity-25 text-white text-uppercase fw-semibold role-badge mb-2 px-3 py-1.5" style="letter-spacing: 0.05em; font-size: 0.7rem;">{{ ucfirst($user->role) }}</span>
        <h1 class="h2 mb-1 fw-bold text-white">Halo, {{ $user->name }}! Selamat datang kembali.</h1>
        <p class="text-white-50 mb-0">Silakan pilih menu di bawah untuk mengelola aktivitas Anda.</p>
    </div>

    <!-- GRUP 1: SISTEM PERSURATAN -->
    @if($user->hasRole('admin', 'staff', 'customer'))
        <div class="mb-5">
            <h2 class="h5 mb-3 text-dark fw-bold d-flex align-items-center gap-2">
                <span class="fs-4">✉️</span> Sistem Persuratan
            </h2>
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('suratmasuk.index') }}" class="text-decoration-none">
                        <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-primary">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Arsip Masuk</span>
                                <span class="fs-3">📥</span>
                            </div>
                            <h2 class="h5 text-dark mb-1 fw-bold">Surat Masuk</h2>
                            <p class="text-muted small mb-0" style="line-height:1.4;">Kelola surat yang diterima dan pantau statusnya.</p>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <a href="{{ route('suratkeluar.index') }}" class="text-decoration-none">
                        <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-success">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Arsip Keluar</span>
                                <span class="fs-3">📤</span>
                            </div>
                            <h2 class="h5 text-dark mb-1 fw-bold">Surat Keluar</h2>
                            <p class="text-muted small mb-0" style="line-height:1.4;">Catat dan pantau surat yang dikirim.</p>
                        </div>
                    </a>
                </div>
                @if($user->hasRole('admin', 'staff'))
                    <div class="col-12 col-sm-6 col-xl-3">
                        <a href="{{ route('disposisi.index') }}" class="text-decoration-none">
                            <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-warning">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-warning bg-opacity-10 text-warning text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Tindak Lanjut</span>
                                    <span class="fs-3">📝</span>
                                </div>
                                <h2 class="h5 text-dark mb-1 fw-bold">Disposisi</h2>
                                <p class="text-muted small mb-0" style="line-height:1.4;">Atur penugasan, catatan, dan batas waktu surat.</p>
                            </div>
                        </a>
                    </div>
                @endif
                @if($user->isAdmin())
                    <div class="col-12 col-sm-6 col-xl-3">
                        <a href="{{ route('klasifikasi.index') }}" class="text-decoration-none">
                            <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-danger">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-danger bg-opacity-10 text-danger text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Kategori</span>
                                    <span class="fs-3">🗂️</span>
                                </div>
                                <h2 class="h5 text-dark mb-1 fw-bold">Klasifikasi</h2>
                                <p class="text-muted small mb-0" style="line-height:1.4;">Kelola jenis dan kategori surat secara terpusat.</p>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- GRUP 2: PENGEPUL DIGITAL -->
    <div class="mb-5">
        <h2 class="h5 mb-3 text-dark fw-bold d-flex align-items-center gap-2">
            <span class="fs-4">♻️</span> Pengepul Digital
        </h2>
        <div class="row g-4">
            @if($user->hasRole('admin', 'staff', 'customer'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.warga.index') }}" class="text-decoration-none">
                        <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-info">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-info bg-opacity-10 text-info text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Warga Area</span>
                                <span class="fs-3">♻️</span>
                            </div>
                            <h2 class="h5 text-dark mb-1 fw-bold">Jual Sampah (Warga)</h2>
                            <p class="text-muted small mb-0" style="line-height:1.4;">Lihat harga, kumpulkan keranjang, pin lokasi maps, dan checkout.</p>
                        </div>
                    </a>
                </div>
            @endif
            @if($user->hasRole('admin', 'staff'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.admin.index') }}" class="text-decoration-none">
                        <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-dark">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-dark bg-opacity-10 text-dark text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Manajemen</span>
                                <span class="fs-3">📊</span>
                            </div>
                            <h2 class="h5 text-dark mb-1 fw-bold">Panel Pengepul (Admin)</h2>
                            <p class="text-muted small mb-0" style="line-height:1.4;">Atur harga material, peta jemputan, kas keuangan, dan stok.</p>
                        </div>
                    </a>
                </div>
            @endif
            @if($user->hasRole('driver'))
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('pengepul.driver.index') }}" class="text-decoration-none">
                        <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-success">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Kurir Area</span>
                                <span class="fs-3">🚚</span>
                            </div>
                            <h2 class="h5 text-dark mb-1 fw-bold">Tugas Kurir (Driver)</h2>
                            <p class="text-muted small mb-0" style="line-height:1.4;">Lihat rute jemputan, masukkan timbangan real, dan bayar digital.</p>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- GRUP 3: PENGATURAN -->
    <div class="mb-4">
        <h2 class="h5 mb-3 text-dark fw-bold d-flex align-items-center gap-2">
            <span class="fs-4">⚙️</span> Pengaturan
        </h2>
        <div class="row g-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <a href="{{ route('profil.index') }}" class="text-decoration-none">
                    <div class="page-card dashboard-card p-3 h-100 border-start border-5 border-secondary">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary text-uppercase fw-semibold" style="letter-spacing: 0.05em; font-size: 0.65rem;">Pengguna</span>
                            <span class="fs-3">👤</span>
                        </div>
                        <h2 class="h5 text-dark mb-1 fw-bold">Profil Saya</h2>
                        <p class="text-muted small mb-0" style="line-height:1.4;">Kelola data diri, kata sandi, dan rekening pencairan dana.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection