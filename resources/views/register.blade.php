<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru | Pengepul Digital</title>
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
            border-radius: 0.375rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }
        .search-result-item:hover {
            background-color: #f8fafc;
            color: #0d6efd;
        }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100 py-4 bg-light">
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6">
                <section class="page-card p-4 p-md-5 bg-white shadow-sm rounded-4">
                    <div class="text-center mb-4">
                        <div class="brand-mark text-white bg-primary fs-4 mx-auto mb-3">♻️</div>
                        <h1 class="h3 mb-1">Daftar Akun Baru</h1>
                        <p class="text-muted mb-0">Buat akun Anda untuk bergabung ke Pengepul Digital</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 mb-3">{{ $errors->first() }}</div>
                    @endif

                    <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Masukkan nama lengkap Anda" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="no_hp">Nomor HP / WhatsApp</label>
                            <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control" placeholder="Contoh: 08123456789" required>
                        </div>

                        <!-- Alamat Lengkap & Autocomplete Maps -->
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-semibold" for="alamat_search">Alamat Lengkap</label>
                            <input id="alamat_search" type="text" name="alamat" value="{{ old('alamat') }}" class="form-control" placeholder="Ketik nama jalan, kelurahan, atau kota..." autocomplete="off" required>
                            <div id="searchResults" class="search-results-list d-none"></div>
                            <small class="text-muted">Ketik alamat Anda, peta di bawah akan mencari titik secara otomatis.</small>
                            
                            <!-- Hidden Latitude & Longitude -->
                            <input type="hidden" id="reg_lat" name="latitude" value="{{ old('latitude', '-6.2088') }}">
                            <input type="hidden" id="reg_lng" name="longitude" value="{{ old('longitude', '106.8456') }}">
                            
                            <div id="regMap" class="mt-2 border rounded-3" style="height: 220px;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="role">Daftar Sebagai (Akses Hak Peran)</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="customer">Warga / Nasabah (Customer)</option>
                                <option value="staff">Staff Pengepul / Lapangan</option>
                                <option value="admin">Admin / Pengepul Utama</option>
                                <option value="driver">Sopir / Kurir (Driver)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="password">Kata Sandi</label>
                            <input id="password" type="password" name="password" class="form-control" placeholder="Sandi minimal 4 karakter" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="password_confirmation">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                        </div>
                        <button class="btn btn-primary w-100 btn-lg shadow-sm">Daftar Akun</button>
                    </form>
                    <p class="text-center small text-muted mt-4 mb-0">Sudah memiliki akun? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Masuk di sini</a></p>
                </section>
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

                // Reverse Geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.display_name) {
                            document.getElementById('alamat_search').value = data.display_name;
                            marker.bindPopup(data.display_name).openPopup();
                        }
                    }).catch(err => console.error(err));
            });

            // Autocomplete Search
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
    </script>
</body>
</html>
