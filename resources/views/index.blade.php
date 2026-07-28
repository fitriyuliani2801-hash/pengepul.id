<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Surat & Pengepul | @yield('title', 'Dashboard')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheets -->
    <link class="styles" rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link class="styles" rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
    <style>
        .btn-toggle {
            transition: all 0.2s ease;
            color: #64748b !important;
            font-size: 0.85rem !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: var(--radius-md);
        }
        .btn-toggle:hover {
            color: var(--primary-teal) !important;
            background: #f1f5f9 !important;
        }
        .btn-toggle .chevron {
            display: inline-block;
            transition: transform 0.25s ease;
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .btn-toggle[aria-expanded="true"] .chevron {
            transform: rotate(90deg);
        }
        .side-nav .nav-link {
            font-size: 0.85rem !important;
            padding: 0.55rem 0.75rem !important;
            margin: 0.15rem 0 !important;
        }
    </style>
</head>
<body>
    @php($user = auth()->user())
    @php($jumlahNotifikasi = $user ? App\Models\Notifikasi::where('user_id', $user->id)->whereNull('read_at')->count() : 0)
    
    <header class="app-header navbar navbar-dark px-3 px-md-4 py-2 sticky-top">
        <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="{{ route('home') }}">
            <span class="brand-mark">
                <i class="fa-solid fa-recycle text-white fs-5"></i>
            </span>
            <div class="d-flex flex-column">
                <span class="fw-bold tracking-tight text-white" style="font-size: 1.05rem; line-height: 1.2;">Manajemen Surat</span>
                <span class="text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.04em;">& Pengepul Digital</span>
            </div>
        </a>

        <div class="d-flex align-items-center gap-2 gap-sm-3 text-white">
            @if($user)
                <!-- Notification Bell Icon Button -->
                <a class="btn btn-outline-light btn-sm position-relative p-0 rounded-circle d-inline-flex align-items-center justify-content-center border-0" href="{{ route('notifikasi.index') }}" aria-label="Notifikasi" style="width: 38px; height: 38px; background: rgba(255,255,255,0.15);" title="Notifikasi">
                    <i class="fa-solid fa-bell text-white" style="font-size: 1rem;"></i>
                    @if($jumlahNotifikasi)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pulse-badge" style="font-size: 0.65rem; padding: 0.25em 0.55em;">
                            {{ $jumlahNotifikasi }}
                        </span>
                    @endif
                </a>
                
                <!-- User Profile Label Info -->
                <div class="d-none d-md-flex align-items-center gap-2 px-2 py-1 rounded-pill" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.15);">
                    <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.85rem; color: #2563eb !important;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="text-start me-2">
                        <div class="fw-semibold text-white" style="font-size: 0.85rem; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user->name }}</div>
                        <small class="text-white-50 text-capitalize d-block" style="font-size: 0.7rem; line-height: 1;">{{ $user->role }}</small>
                    </div>
                </div>
                
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-light btn-sm px-3 rounded-pill fw-semibold text-dark border-0 shadow-sm" style="font-size: 0.82rem;">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1 text-danger"></i> Keluar
                    </button>
                </form>
            @else
                <a class="btn btn-light btn-sm px-4 rounded-pill fw-semibold border-0 shadow-sm" href="{{ route('login') }}">Masuk</a>
            @endif
        </div>
    </header>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <aside class="col-md-3 col-lg-2 side-nav py-3 px-2">
                <nav class="nav flex-column gap-1">
                    <!-- Kategori: Umum -->
                    <div class="side-nav-header">Main Menu</div>
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
                    </a>
                    
                    @if($user)
                        <!-- Kategori: Sistem Persuratan -->
                        @if($user->hasRole('admin', 'staff', 'customer'))
                            @php($isPersuratanActive = request()->routeIs('suratmasuk.*') || request()->routeIs('suratkeluar.*') || request()->routeIs('disposisi.*') || request()->routeIs('klasifikasi.*'))
                            <div class="mb-1 mt-2">
                                <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#persuratan-collapse" data-target="#persuratan-collapse" aria-expanded="{{ $isPersuratanActive ? 'true' : 'false' }}">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-envelope text-primary me-1"></i> Sistem Persuratan
                                    </span>
                                    <span class="chevron"><i class="fa-solid fa-chevron-right"></i></span>
                                </button>
                                <div class="collapse {{ $isPersuratanActive ? 'show' : '' }} ms-2 ps-2 border-start" id="persuratan-collapse" style="border-left: 2px solid #e2e8f0 !important;">
                                    <nav class="nav flex-column gap-1 mt-1">
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('suratmasuk.*') ? 'active' : '' }}" href="{{ route('suratmasuk.index') }}">
                                            <i class="fa-solid fa-inbox"></i> <span>Surat Masuk</span>
                                        </a>
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('suratkeluar.*') ? 'active' : '' }}" href="{{ route('suratkeluar.index') }}">
                                            <i class="fa-solid fa-paper-plane"></i> <span>Surat Keluar</span>
                                        </a>
                                        @if($user->hasRole('admin', 'staff'))
                                            <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('disposisi.*') ? 'active' : '' }}" href="{{ route('disposisi.index') }}">
                                                <i class="fa-solid fa-file-signature"></i> <span>Disposisi</span>
                                            </a>
                                        @endif
                                        @if($user->isAdmin())
                                            <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('klasifikasi.*') ? 'active' : '' }}" href="{{ route('klasifikasi.index') }}">
                                                <i class="fa-solid fa-folder-tree"></i> <span>Klasifikasi</span>
                                            </a>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        @endif

                        <!-- Kategori: Pengepul Digital -->
                        @php($isPengepulActive = request()->routeIs('pengepul.warga.*') || request()->routeIs('pengepul.admin.*') || request()->routeIs('pengepul.driver.*'))
                        <div class="mb-1 mt-2">
                            <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#pengepul-collapse" data-target="#pengepul-collapse" aria-expanded="{{ $isPengepulActive ? 'true' : 'false' }}">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-recycle text-success me-1"></i> Pengepul Digital
                                </span>
                                <span class="chevron"><i class="fa-solid fa-chevron-right"></i></span>
                            </button>
                            <div class="collapse {{ $isPengepulActive ? 'show' : '' }} ms-2 ps-2 border-start" id="pengepul-collapse" style="border-left: 2px solid #e2e8f0 !important;">
                                <nav class="nav flex-column gap-1 mt-1">
                                    @if($user->hasRole('customer'))
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.warga.*') ? 'active' : '' }}" href="{{ route('pengepul.warga.index') }}">
                                            <i class="fa-solid fa-truck-ramp-box"></i> <span>Jual Sampah (Warga)</span>
                                        </a>
                                    @endif
                                    @if($user->hasRole('admin', 'staff'))
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.admin.*') ? 'active' : '' }}" href="{{ route('pengepul.admin.index') }}">
                                            <i class="fa-solid fa-industry"></i> <span>Distribusi ke Supplier</span>
                                        </a>
                                    @endif
                                    @if($user->hasRole('driver'))
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.driver.*') ? 'active' : '' }}" href="{{ route('pengepul.driver.index') }}">
                                            <i class="fa-solid fa-truck-moving"></i> <span>Tugas Kurir (Driver)</span>
                                        </a>
                                    @endif
                                </nav>
                            </div>
                        </div>

                        <!-- Kategori: Absensi Karyawan -->
                        @if($user->hasRole('admin', 'staff', 'driver'))
                            @php($isAbsensiActive = request()->routeIs('absensi.*'))
                            <div class="mb-1 mt-2">
                                <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#absensi-collapse" data-target="#absensi-collapse" aria-expanded="{{ $isAbsensiActive ? 'true' : 'false' }}">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-camera-retro text-primary me-1"></i> Absensi Karyawan
                                    </span>
                                    <span class="chevron"><i class="fa-solid fa-chevron-right"></i></span>
                                </button>
                                <div class="collapse {{ $isAbsensiActive ? 'show' : '' }} ms-2 ps-2 border-start" id="absensi-collapse" style="border-left: 2px solid #e2e8f0 !important;">
                                    <nav class="nav flex-column gap-1 mt-1">
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('absensi.scan') || request()->routeIs('absensi.enrollment') ? 'active' : '' }}" href="{{ route('absensi.scan') }}">
                                            <i class="fa-solid fa-face-smile"></i> <span>Scan Wajah</span>
                                        </a>
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('absensi.riwayat') ? 'active' : '' }}" href="{{ route('absensi.riwayat') }}">
                                            <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Saya</span>
                                        </a>
                                        @if($user->hasRole('admin', 'staff'))
                                            <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('absensi.admin.rekap') ? 'active' : '' }}" href="{{ route('absensi.admin.rekap') }}">
                                                <i class="fa-solid fa-clipboard-user"></i> <span>Rekap Absensi</span>
                                            </a>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        @endif

                        <!-- Kategori: Pengaturan -->
                        @php($isPengaturanActive = request()->routeIs('profil.*'))
                        <div class="mb-1 mt-2">
                            <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#pengaturan-collapse" data-target="#pengaturan-collapse" aria-expanded="{{ $isPengaturanActive ? 'true' : 'false' }}">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-gear text-secondary me-1"></i> Pengaturan
                                </span>
                                <span class="chevron"><i class="fa-solid fa-chevron-right"></i></span>
                            </button>
                            <div class="collapse {{ $isPengaturanActive ? 'show' : '' }} ms-2 ps-2 border-start" id="pengaturan-collapse" style="border-left: 2px solid #e2e8f0 !important;">
                                <nav class="nav flex-column gap-1 mt-1">
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('profil.*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                                        <i class="fa-solid fa-user-gear"></i> <span>Profil Saya</span>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    @endif
                </nav>
            </aside>
            
            <main class="col-md-9 col-lg-10 p-3 p-md-4">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 rounded-3 mb-4">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                            <i class="fa-solid fa-circle-exclamation fs-5"></i> Terjadi Kesalahan:
                        </div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('isihalaman')
            </main>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%; height: 85vh; margin: 1.75rem auto;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="height: 100%; display: flex; flex-direction: column;">
                <div class="modal-header py-2 px-3 bg-dark text-white" style="flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between;">
                    <h5 class="modal-title fs-6 fw-bold" id="pdfViewerModalLabel"><i class="fa-solid fa-file-pdf text-danger me-2"></i> Pratinjau Berkas PDF</h5>
                    <div class="d-flex align-items-center gap-2 ms-auto me-3">
                        <a id="pdfViewerExternalLink" href="" target="_blank" class="btn btn-sm btn-outline-light py-1 px-3 rounded-pill" style="font-size: 0.78rem; text-decoration: none;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka di Tab Baru
                        </a>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="flex: 1 1 auto; height: calc(100% - 50px); overflow: hidden; background: #525659;">
                    <iframe id="pdfViewerFrame" src="" width="100%" height="100%" style="border: none; display: block;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function toggleCollapse(targetId) {
            const target = document.getElementById(targetId);
            if (!target) return;
            target.classList.toggle('show');
            target.style.display = target.classList.contains('show') ? 'block' : 'none';
        }

        (function() {
            document.addEventListener('click', function(e) {
                const anchor = e.target.closest('a');
                if (anchor) {
                    const href = anchor.getAttribute('href');
                    if (href && (href.toLowerCase().includes('.pdf') || href.startsWith('blob:'))) {
                        e.preventDefault();
                        
                        const pdfFrame = document.getElementById('pdfViewerFrame');
                        const pdfExternalLink = document.getElementById('pdfViewerExternalLink');
                        
                        if (pdfFrame) {
                            pdfFrame.src = href;
                        }
                        if (pdfExternalLink) {
                            pdfExternalLink.href = href;
                        }
                        
                        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                            $('#pdfViewerModal').modal('show');
                        } else {
                            window.open(href, '_blank');
                        }
                    }
                }
            });

            if (typeof $ !== 'undefined') {
                $(document).ready(function() {
                    $('#pdfViewerModal').on('hidden.bs.modal', function () {
                        const pdfFrame = document.getElementById('pdfViewerFrame');
                        if (pdfFrame) {
                            pdfFrame.src = '';
                        }
                    });
                });
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const fileInputs = document.querySelectorAll('input[type="file"][name="file_surat"], input[type="file"][name="file_balasan"]');
            fileInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    if (this.previewUrl) {
                        URL.revokeObjectURL(this.previewUrl);
                        this.previewUrl = null;
                    }
                    const file = this.files[0];
                    let feedback = this.nextElementSibling;
                    while (feedback && !feedback.classList.contains('file-feedback')) {
                        feedback = feedback.nextElementSibling;
                    }
                    if (!feedback) return;

                    if (!file) {
                        feedback.textContent = '';
                        feedback.className = 'file-feedback mt-1 small';
                        this.classList.remove('is-invalid', 'is-valid');
                        return;
                    }

                    const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                    const maxSize = 2 * 1024 * 1024; // 2MB

                    if (isPdf && file.size <= maxSize) {
                        const fileUrl = URL.createObjectURL(file);
                        this.previewUrl = fileUrl;
                        feedback.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> File PDF valid <a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-success ms-2 py-0 px-2 rounded-pill" style="font-size: 0.75rem;">Pratinjau</a>';
                        feedback.className = 'file-feedback mt-1 small text-success fw-semibold d-inline-flex align-items-center';
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        feedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Format harus PDF (Maks 2MB)';
                        feedback.className = 'file-feedback mt-1 small text-danger fw-semibold';
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                });
            });
        });
    </script>
</body>
</html>
