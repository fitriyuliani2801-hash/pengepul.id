<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Surat | @yield('title', 'Dashboard')</title>
    <link class="styles" rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link class="styles" rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
    <style>
        .btn-toggle {
            transition: color 0.15s ease;
            color: #526273 !important;
        }
        .btn-toggle:hover {
            color: var(--blue) !important;
            background: #eaf3fb !important;
        }
        .btn-toggle .chevron {
            display: inline-block;
            transition: transform .2s ease;
            transform: rotate(0deg);
            font-size: 0.7rem;
            color: #a0aec0;
        }
        .btn-toggle[aria-expanded="true"] .chevron {
            transform: rotate(90deg);
        }
        .side-nav .nav-link {
            font-size: 0.85rem !important;
            padding: 0.5rem 0.75rem !important;
            margin: 0.1rem 0 !important;
        }
        .side-nav .btn-toggle {
            font-size: 0.85rem !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: .6rem;
        }
    </style>
</head>
<body>
    @php($user = auth()->user())
    @php($jumlahNotifikasi = $user ? App\Models\Notifikasi::where('user_id', $user->id)->whereNull('read_at')->count() : 0)
    <header class="app-header navbar navbar-dark px-3 px-md-4 py-2">
        <a class="navbar-brand d-flex align-items-center gap-2 mb-0" href="{{ route('home') }}">
            <span class="brand-mark">✉</span><span>Manajemen Surat</span>
        </a>
        <div class="d-flex align-items-center gap-2 gap-sm-3 text-white">
            @if($user)
                <!-- Notification Bell Icon Button -->
                <a class="btn btn-outline-light btn-sm position-relative p-0 rounded-circle d-inline-flex align-items-center justify-content-center" href="{{ route('notifikasi.index') }}" aria-label="Notifikasi" style="width: 38px; height: 38px;" title="Notifikasi">
                    <span style="font-size: 1.1rem; line-height: 1;">🔔</span>
                    @if($jumlahNotifikasi)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                            {{ $jumlahNotifikasi }}
                        </span>
                    @endif
                </a>
                
                <!-- User Profile Label Info -->
                <div class="text-end d-none d-md-block px-1" style="border-right: 1px solid rgba(255,255,255,0.25); padding-right: 12px; margin-right: 4px;">
                    <div class="fw-semibold" style="font-size: 0.9rem; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $user->name }}</div>
                    <small class="text-white-50 text-capitalize" style="font-size: 0.75rem;">{{ $user->role }}</small>
                </div>
                
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-outline-light btn-sm px-3">Keluar</button>
                </form>
            @else
                <a class="btn btn-outline-light btn-sm px-3" href="{{ route('login') }}">Masuk</a>
            @endif
        </div>
    </header>
    <div class="container-fluid p-0"><div class="row g-0">
        <aside class="col-md-3 col-lg-2 side-nav py-3 px-2">
            <nav class="nav flex-column gap-1">
                <!-- Kategori: Umum -->
                <div class="px-3 mb-1 mt-1">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em; color: #a0aec0;">Umum</span>
                </div>
                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <span>🏠</span> Dashboard
                </a>
                
                @if($user)
                    <!-- Kategori: Sistem Persuratan -->
                    @if($user->hasRole('admin', 'staff', 'customer'))
                        @php($isPersuratanActive = request()->routeIs('suratmasuk.*') || request()->routeIs('suratkeluar.*') || request()->routeIs('disposisi.*') || request()->routeIs('klasifikasi.*'))
                        <div class="mb-1 mt-2">
                            <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#persuratan-collapse" data-target="#persuratan-collapse" aria-expanded="{{ $isPersuratanActive ? 'true' : 'false' }}">
                                <span class="d-flex align-items-center gap-2"><span>✉️</span> Sistem Persuratan</span>
                                <span class="chevron">▶</span>
                            </button>
                            <div class="collapse {{ $isPersuratanActive ? 'show' : '' }} ms-2 ps-2 border-start" id="persuratan-collapse" style="border-left: 2px solid #e8edf4 !important;">
                                <nav class="nav flex-column gap-1 mt-1">
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('suratmasuk.*') ? 'active' : '' }}" href="{{ route('suratmasuk.index') }}">
                                        <span>📥</span> Surat Masuk
                                    </a>
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('suratkeluar.*') ? 'active' : '' }}" href="{{ route('suratkeluar.index') }}">
                                        <span>📤</span> Surat Keluar
                                    </a>
                                    @if($user->hasRole('admin', 'staff'))
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('disposisi.*') ? 'active' : '' }}" href="{{ route('disposisi.index') }}">
                                            <span>📝</span> Disposisi
                                        </a>
                                    @endif
                                    @if($user->isAdmin())
                                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('klasifikasi.*') ? 'active' : '' }}" href="{{ route('klasifikasi.index') }}">
                                            <span>🗂️</span> Klasifikasi
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
                            <span class="d-flex align-items-center gap-2"><span>♻️</span> Pengepul Digital</span>
                            <span class="chevron">▶</span>
                        </button>
                        <div class="collapse {{ $isPengepulActive ? 'show' : '' }} ms-2 ps-2 border-start" id="pengepul-collapse" style="border-left: 2px solid #e8edf4 !important;">
                            <nav class="nav flex-column gap-1 mt-1">
                                @if($user->hasRole('admin', 'staff', 'customer'))
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.warga.*') ? 'active' : '' }}" href="{{ route('pengepul.warga.index') }}">
                                        <span>♻️</span> Jual Sampah (Warga)
                                    </a>
                                @endif
                                @if($user->hasRole('admin', 'staff'))
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.admin.*') ? 'active' : '' }}" href="{{ route('pengepul.admin.index') }}">
                                        <span>📊</span> Panel Pengepul (Admin)
                                    </a>
                                @endif
                                @if($user->hasRole('driver'))
                                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pengepul.driver.*') ? 'active' : '' }}" href="{{ route('pengepul.driver.index') }}">
                                        <span>🚚</span> Tugas Kurir (Driver)
                                    </a>
                                @endif
                            </nav>
                        </div>
                    </div>

                    <!-- Kategori: Pengaturan -->
                    @php($isPengaturanActive = request()->routeIs('profil.*'))
                    <div class="mb-1 mt-2">
                        <button type="button" class="btn btn-toggle d-inline-flex align-items-center justify-content-between w-100 border-0 bg-transparent text-start fw-bold" data-bs-toggle="collapse" data-toggle="collapse" data-bs-target="#pengaturan-collapse" data-target="#pengaturan-collapse" aria-expanded="{{ $isPengaturanActive ? 'true' : 'false' }}">
                            <span class="d-flex align-items-center gap-2"><span>⚙️</span> Pengaturan</span>
                            <span class="chevron">▶</span>
                        </button>
                        <div class="collapse {{ $isPengaturanActive ? 'show' : '' }} ms-2 ps-2 border-start" id="pengaturan-collapse" style="border-left: 2px solid #e8edf4 !important;">
                            <nav class="nav flex-column gap-1 mt-1">
                                <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('profil.*') ? 'active' : '' }}" href="{{ route('profil.index') }}">
                                    <span>👤</span> Profil Saya
                                </a>
                            </nav>
                        </div>
                    </div>
                @endif
            </nav>
        </aside>
        <main class="col-md-9 col-lg-10 p-3 p-md-4">
            @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger border-0 shadow-sm"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('isihalaman')
        </main>
    </div></div>

    <!-- PDF Viewer Modal -->
    <div class="modal fade" id="pdfViewerModal" tabindex="-1" aria-labelledby="pdfViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%; height: 85vh; margin: 1.75rem auto;">
            <div class="modal-content" style="height: 100%; display: flex; flex-direction: column;">
                <div class="modal-header py-2" style="flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between;">
                    <h5 class="modal-title" id="pdfViewerModalLabel">Pratinjau Berkas PDF</h5>
                    <div class="d-flex align-items-center gap-2 ms-auto me-3">
                        <a id="pdfViewerExternalLink" href="" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem; text-decoration: none;">Buka di Tab Baru / Unduh</a>
                    </div>
                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
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

        // Intercept PDF link clicks globally and show them in a modal instead of a new tab
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
                        
                        // Use jQuery modal if jQuery and bootstrap modal plugin are available (Bootstrap v4 support)
                        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                            $('#pdfViewerModal').modal('show');
                        } else {
                            // Fallback to opening in new tab if Bootstrap modal fails
                            window.open(href, '_blank');
                        }
                    }
                }
            });

            // Clear iframe source on modal close (Bootstrap v4 event syntax)
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

        // File validation script
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
                        feedback.innerHTML = 'file sesuai <a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-outline-success ms-2 py-0 px-2" style="font-size: 0.75rem; text-decoration: none;">Pratinjau File</a>';
                        feedback.className = 'file-feedback mt-1 small text-success fw-semibold d-inline-flex align-items-center';
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        feedback.textContent = 'file tidak sesuai';
                        feedback.className = 'file-feedback mt-1 small text-danger fw-semibold';
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                });
            });

            // Prevent form submission if file is invalid
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const inputs = form.querySelectorAll('input[type="file"][name="file_surat"], input[type="file"][name="file_balasan"]');
                    inputs.forEach(function(fileInput) {
                        if (fileInput && fileInput.files && fileInput.files[0]) {
                            const file = fileInput.files[0];
                            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                            const maxSize = 2 * 1024 * 1024;
                            if (!isPdf || file.size > maxSize) {
                                e.preventDefault();
                                alert('File tidak sesuai. Harap unggah file PDF dengan ukuran maksimal 2MB.');
                                fileInput.focus();
                            }
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
