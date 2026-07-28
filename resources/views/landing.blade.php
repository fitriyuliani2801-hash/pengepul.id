<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengepul Sampah Daur Ulang & Manajemen Surat | Layanan Digital</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
    <style>
        .hero-section {
            background: var(--gradient-hero);
            color: white;
            padding: 110px 0 90px;
            border-bottom-left-radius: 2.5rem;
            border-bottom-right-radius: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-floating-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            padding: 2.5rem;
        }
        .card-price {
            transition: all 0.3s ease;
            border-radius: var(--radius-lg);
        }
        .card-price:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-teal);
        }
        .btn-cta {
            padding: 14px 34px;
            font-weight: 700;
            border-radius: 50rem;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            transition: all 0.25s ease;
        }
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top px-3 py-3 glass-header">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                <span class="brand-mark">
                    <i class="fa-solid fa-recycle text-white fs-5"></i>
                </span>
                <span class="fw-bold tracking-tight text-white fs-5">Pengepul Digital</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link active px-3" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#visi-misi">Visi & Misi</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#harga">Daftar Harga</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-4 rounded-pill fw-semibold">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm">Daftar Akun</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="beranda" class="hero-section text-center text-lg-start">
        <div class="container position-relative z-1">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-white bg-opacity-20 text-white text-uppercase fw-bold mb-3 px-3.5 py-2 rounded-pill" style="font-size:0.75rem; letter-spacing:0.08em; backdrop-filter: blur(8px);">
                        <i class="fa-solid fa-truck-fast me-1"></i> Layanan Penjemputan Sampah Daur Ulang
                    </span>
                    <h1 class="display-4 fw-extrabold mb-3 text-white" style="line-height: 1.15;">
                        Jual Sampah Daur Ulang <span style="color: #99f6e4;">Tanpa Keluar Rumah</span>
                    </h1>
                    <p class="lead mb-4 text-white-50" style="font-size: 1.1rem; line-height: 1.6;">
                        Tawarkan sampah rumah tangga Anda (kardus, plastik, logam, kertas) langsung dari lokasi peta. Petugas kurir kami akan menjemput, menimbang di lokasi, dan melakukan transfer instan.
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('register') }}" class="btn btn-light text-dark btn-cta shadow">
                            <i class="fa-solid fa-user-plus text-primary me-2"></i> Daftar Sekarang
                        </a>
                        <a href="#harga" class="btn btn-outline-light btn-cta">
                            <i class="fa-solid fa-tags me-2"></i> Cek Harga Hari Ini
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="hero-floating-card d-inline-flex flex-column align-items-center justify-content-center w-100">
                        <div class="stat-icon-wrapper stat-icon-success mb-3" style="width: 80px; height: 80px; font-size: 2.5rem; border-radius: var(--radius-lg);">
                            <i class="fa-solid fa-recycle"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2">Sirkular & Ramah Lingkungan</h4>
                        <p class="text-white-50 small mb-0">Ubah sampah rumah tangga menjadi nilai tunai dengan proses yang mudah, akurat, dan transparan.</p>
                        <div class="d-flex gap-3 mt-4">
                            <span class="badge bg-success bg-opacity-25 text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-shield-check me-1"></i> Timbangan Real</span>
                            <span class="badge bg-info bg-opacity-25 text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-bolt me-1"></i> Bayar Instan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge badge-soft-primary badge-pill-custom mb-2">Nilai Utam Kami</span>
                <h2 class="fw-extrabold text-dark">Visi & Misi Pengepul Digital</h2>
                <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Mewujudkan sirkular ekonomi yang bersih, transparan, dan memberikan manfaat finansial langsung bagi masyarakat.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="stat-icon-wrapper stat-icon-success mb-3 mx-auto">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <h3 class="h5 fw-bold text-dark">Lingkungan Bersih</h3>
                        <p class="text-muted small mb-0">Mengurangi penumpukan sampah liar dengan memfasilitasi jalur daur ulang yang terstruktur langsung dari rumah warga.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="stat-icon-wrapper stat-icon-primary mb-3 mx-auto">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <h3 class="h5 fw-bold text-dark">Ekonomi Sirkular</h3>
                        <p class="text-muted small mb-0">Memberikan nilai ekonomis tambahan bagi masyarakat melalui penjualan sampah daur ulang secara transparan.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="stat-icon-wrapper stat-icon-warning mb-3 mx-auto">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="h5 fw-bold text-dark">Layanan Cepat</h3>
                        <p class="text-muted small mb-0">Proses penjemputan terjadwal oleh driver, timbangan real-time di tempat, dan pembayaran transfer digital instan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar Harga Section -->
    <section id="harga" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge badge-soft-success badge-pill-custom mb-2">Update Terkini</span>
                <h2 class="fw-extrabold text-dark">Harga Beli Sampah Terkini</h2>
                <p class="text-muted">Berikut adalah daftar harga beli sampah daur ulang per kilogram hari ini dari Pengepul.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                @foreach($katalog as $kat)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="page-card p-4 text-center card-price h-100">
                            <div class="stat-icon-wrapper stat-icon-info mb-3 mx-auto fs-3">
                                {{ $kat->icon ?: '📦' }}
                            </div>
                            <h3 class="h6 fw-bold text-dark mb-1">{{ $kat->nama_material }}</h3>
                            <div class="text-success fw-extrabold fs-5">
                                Rp {{ number_format($kat->harga_beli_per_kg, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">/ kg</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center text-md-start">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark">
                            <i class="fa-solid fa-recycle text-white fs-5"></i>
                        </span>
                        <h5 class="fw-bold mb-0 text-white">Pengepul Digital</h5>
                    </div>
                    <p class="text-white-50 small mb-0" style="max-width: 450px;">
                        Platform digital untuk penjemputan sampah daur ulang mandiri yang terintegrasi dengan tata kelola administrasi dan penugasan surat kurir secara terstruktur.
                    </p>
                </div>
                
                <div class="col-md-3">
                    <h6 class="fw-bold text-uppercase text-white-50 small mb-3" style="letter-spacing: 0.08em;">Jam Operasional</h6>
                    <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                        <li><i class="fa-regular fa-clock me-2 text-success"></i> Senin - Sabtu: 08:00 - 17:00</li>
                        <li><i class="fa-solid fa-ban me-2 text-danger"></i> Minggu & Libur: Tutup</li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6 class="fw-bold text-uppercase text-white-50 small mb-3" style="letter-spacing: 0.08em;">Kontak Layanan</h6>
                    <ul class="list-unstyled small text-white-50 mb-0 d-flex flex-column gap-2">
                        <li><i class="fa-solid fa-phone me-2 text-info"></i> 0812-3456-7890</li>
                        <li><i class="fa-solid fa-envelope me-2 text-warning"></i> bantuan@pengepuldigital.com</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 border-secondary opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-white-50 small">
                <div>&copy; {{ date('Y') }} Pengepul Digital & Manajemen Surat. Hak Cipta Dilindungi.</div>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <a href="#" class="text-white-50 text-decoration-none">Privasi</a>
                    <a href="#" class="text-white-50 text-decoration-none">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
