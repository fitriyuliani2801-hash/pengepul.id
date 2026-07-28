<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru | Pengepul Digital</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/surat-app.css') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .search-results-list {
            position: absolute;
            z-index: 1050;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
        }
        .search-result-item {
            padding: 10px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            transition: background 0.15s ease;
        }
        .search-result-item:hover {
            background-color: #f1f5f9;
            color: var(--primary-teal);
        }
    </style>
</head>
<body class="auth-wrapper py-5">
    <main class="w-100 d-flex justify-content-center">
        <div class="auth-card" style="max-width: 600px;">
            <div class="text-center mb-4">
                <div class="brand-mark mx-auto mb-3" style="width: 54px; height: 54px; background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                    <i class="fa-solid fa-user-plus text-white fs-4"></i>
                </div>
                <h1 class="h4 fw-extrabold text-dark mb-1">Daftar Akun Baru</h1>
                <p class="text-muted small mb-0">Lengkapi data akun Anda untuk bergabung di Pengepul Digital</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 small">
                    <i class="fa-solid fa-circle-exclamation text-danger"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                @csrf
                
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="name">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control border-start-0" placeholder="Nama lengkap Anda" required autofocus style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="email">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control border-start-0" placeholder="nama@email.com" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="no_hp">Nomor HP / WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control border-start-0" placeholder="08123456789" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="role">Peran (Role)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                            <select id="role" name="role" class="form-select border-start-0" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                                <option value="customer">Warga / Nasabah (Customer)</option>
                                <option value="staff">Staff Pengepul / Lapangan</option>
                                <option value="admin">Admin / Pengepul Utama</option>
                                <option value="driver">Sopir / Kurir (Driver)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Alamat Lengkap & Autocomplete Maps -->
                <div class="mt-3 mb-3 position-relative">
                    <label class="form-label fw-semibold text-secondary small" for="alamat_search">Alamat Lengkap & Peta Penjemputan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </span>
                        <input id="alamat_search" type="text" name="alamat" value="{{ old('alamat') }}" class="form-control border-start-0" placeholder="Ketik nama jalan, kelurahan, atau kota..." autocomplete="off" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                    </div>
                    <div id="searchResults" class="search-results-list d-none"></div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">Ketik alamat Anda, pin peta akan otomatis mengarahkan ke titik penjemputan.</small>
                    
                    <!-- Hidden Latitude & Longitude -->
                    <input type="hidden" id="reg_lat" name="latitude" value="{{ old('latitude', '-6.2088') }}">
                    <input type="hidden" id="reg_lng" name="longitude" value="{{ old('longitude', '106.8456') }}">
                    
                    <div id="regMap" class="mt-2 border rounded-3 overflow-hidden shadow-sm" style="height: 200px;"></div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="password">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input id="password" type="password" name="password" class="form-control border-start-0 border-end-0" placeholder="Min. 4 karakter" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light text-muted px-2.5" type="button" onclick="togglePasswordVisibility('password', 'toggleEyePass1')" style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                                <i class="fa-solid fa-eye" id="toggleEyePass1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary small" for="password_confirmation">Konfirmasi Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control border-start-0 border-end-0" placeholder="Ulangi sandi" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light text-muted px-2.5" type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleEyePass2')" style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                                <i class="fa-solid fa-eye" id="toggleEyePass2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary w-100 btn-lg shadow fs-6 py-2.5 mt-4 mb-3">
                    <i class="fa-solid fa-user-check me-2"></i> Buat Akun Sekarang
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <p class="small text-muted mb-0">Sudah memiliki akun? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">Masuk di sini</a></p>
            </div>
        </div>
    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var defaultLat = parseFloat(document.getElementById('reg_lat').value) || -6.2088;
            var defaultLng = parseFloat(document.getElementById('reg_lng').value) || 106.8456;

            var map = L.map('regMap').setView([defaultLat, defaultLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

            function updateMarker(lat, lng, addressText) {
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
                document.getElementById('reg_lat').value = lat.toFixed(7);
                document.getElementById('reg_lng').value = lng.toFixed(7);
                if (addressText) {
                    marker.bindPopup(addressText).openPopup();
                }
            }

            marker.on('dragend', function (e) {
                var coord = e.target.getLatLng();
                var lat = coord.lat;
                var lng = coord.lng;

                document.getElementById('reg_lat').value = lat.toFixed(7);
                document.getElementById('reg_lng').value = lng.toFixed(7);

                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            document.getElementById('alamat_search').value = data.display_name;
                            marker.bindPopup(data.display_name).openPopup();
                        }
                    }).catch(err => console.error(err));
            });

            var searchInput = document.getElementById('alamat_search');
            var searchResults = document.getElementById('searchResults');
            var debounceTimer = null;

            searchInput.addEventListener('input', function () {
                var query = this.value.trim();
                clearTimeout(debounceTimer);

                if (query.length < 3) {
                    searchResults.classList.add('d-none');
                    searchResults.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(function () {
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id`)
                        .then(res => res.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            if (data && data.length > 0) {
                                searchResults.classList.remove('d-none');
                                data.forEach(item => {
                                    var div = document.createElement('div');
                                    div.className = 'search-result-item';
                                    div.innerText = item.display_name;
                                    div.addEventListener('click', function () {
                                        searchInput.value = item.display_name;
                                        searchResults.classList.add('d-none');
                                        var lat = parseFloat(item.lat);
                                        var lon = parseFloat(item.lon);
                                        updateMarker(lat, lon, item.display_name);
                                    });
                                    searchResults.appendChild(div);
                                });
                            } else {
                                searchResults.classList.add('d-none');
                            }
                        }).catch(err => console.error(err));
                }, 400);
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('d-none');
                }
            });
        });

        function togglePasswordVisibility(inputId, eyeIconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(eyeIconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
