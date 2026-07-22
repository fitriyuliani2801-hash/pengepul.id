<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengepul Sampah Daur Ulang | Layanan Penjemputan Mandiri</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
    <style>
        .hero-section {
            background: linear-gradient(115deg, #12355b, #1c6ea4);
            color: white;
            padding: 100px 0;
            border-bottom-left-radius: 2rem;
            border-bottom-right-radius: 2rem;
        }
        .card-price {
            transition: transform 0.2s;
        }
        .card-price:hover {
            transform: translateY(-5px);
        }
        .btn-cta {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top px-3 py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing') }}">
                <span class="brand-mark">♻️</span> <span class="fw-bold">Pengepul Digital</span>
            </a>
            <button class="navbar-expand-lg navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link active" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#visi-misi">Visi & Misi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#harga">Daftar Harga</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-3">Daftar Akun</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="beranda" class="hero-section text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-light text-primary text-uppercase fw-bold mb-3 px-3 py-2" style="font-size:0.8rem; letter-spacing:0.05em;">Layanan Penjemputan Sampah Daur Ulang</span>
                    <h1 class="display-4 fw-bold mb-3" style="line-height: 1.15;">Jual Sampah Daur Ulang Tanpa Keluar Rumah</h1>
                    <p class="lead mb-4 text-white-50">Tawarkan sampah rumah tangga Anda (kardus, plastik, logam) langsung dari peta. Petugas kami akan menjemput, menimbang di lokasi, dan melakukan pembayaran digital instan.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('register') }}" class="btn btn-light text-primary btn-cta">Daftar Sekarang</a>
                        <a href="#harga" class="btn btn-outline-light btn-cta">Cek Harga Hari Ini</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="fs-1 py-5 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 250px; height: 250px; backdrop-filter: blur(10px);">
                        ♻️📦🚚
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Visi & Misi Kami</h2>
                <p class="text-muted">Mewujudkan sirkular ekonomi yang bersih, transparan, dan bermanfaat bagi masyarakat luas.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="fs-1 mb-3">🌱</div>
                        <h3 class="h5 fw-bold text-dark">Lingkungan Bersih</h3>
                        <p class="text-muted small mb-0">Mengurangi penumpukan sampah liar dengan memfasilitasi jalur daur ulang yang terstruktur langsung dari rumah warga.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="fs-1 mb-3">🤝</div>
                        <h3 class="h5 fw-bold text-dark">Ekonomi Sirkular</h3>
                        <p class="text-muted small mb-0">Memberikan nilai ekonomis tambahan bagi masyarakat melalui penjualan sampah daur ulang secara transparan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="page-card p-4 h-100 text-center">
                        <div class="fs-1 mb-3">⚡</div>
                        <h3 class="h5 fw-bold text-dark">Layanan Cepat</h3>
                        <p class="text-muted small mb-0">Proses penjemputan terjadwal oleh driver, timbangan real-time di tempat, dan pembayaran transfer e-wallet instan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Daftar Harga Section -->
    <section id="harga" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Harga Beli Sampah Terkini</h2>
                <p class="text-muted">Berikut adalah daftar harga beli sampah daur ulang hari ini per kilogram dari Pengepul.</p>
            </div>
            <div class="row g-3 justify-content-center">
                @foreach($katalog as $kat)
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="page-card p-4 text-center card-price h-100">
                            <div class="fs-1 mb-3">{{ $kat->icon ?: '📦' }}</div>
                            <h3 class="h6 fw-bold text-dark mb-1">{{ $kat->nama_material }}</h3>
                            <div class="text-success fw-bold">Rp {{ number_format($kat->harga_beli_per_kg, 0, ',', '.') }} / kg</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center text-md-start">
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-bold">Pengepul Digital</h5>
                    <p class="text-white-50 small mb-0">Platform digital untuk penjemputan sampah daur ulang mandiri yang terintegrasi dengan administrasi surat penugasan.</p>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold small text-white-50">Operasional Gudang</h5>
                    <ul class="list-unstyled small text-white-50 mb-0">
                        <li>Senin - Sabtu: 08:00 - 17:00</li>
                        <li>Minggu & Hari Libur: Tutup</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold small text-white-50">Kontak Layanan</h5>
                    <ul class="list-unstyled small text-white-50 mb-0">
                        <li>📞 0812-3456-7890</li>
                        <li>✉️ bantuan@pengepuldigital.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="text-center text-white-50 small">
                &copy; {{ date('Y') }} Pengepul Digital. Hak Cipta Dilindungi.
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
