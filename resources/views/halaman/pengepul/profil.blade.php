@extends('index')
@section('title', 'Profil & Pencairan Dana')
@section('isihalaman')
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

<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Pengaturan Profil & Rekening</h1>
            <p class="text-muted mb-0">Kelola data diri, koordinat rumah penjemputan, dan akun pembayaran (pencairan dana).</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">← Kembali</a>
    </div>

    <form action="{{ route('profil.update') }}" method="POST" class="row g-4">
        @csrf
        
        <!-- DATA DIRI & MAPS -->
        <div class="col-lg-7">
            <div class="page-card p-4 mb-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>👤</span> Data Diri & Alamat Rumah</h2>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nomor HP / WhatsApp</label>
                        <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <label class="form-label fw-semibold">Alamat Lengkap Rumah</label>
                    <input type="text" id="profAlamatSearch" class="form-control mb-1" name="alamat" value="{{ old('alamat', $user->alamat) }}" placeholder="Ketik nama jalan atau kelurahan..." autocomplete="off">
                    <div id="profSearchResults" class="search-results-list d-none"></div>
                    <small class="text-muted">Ketik alamat Anda, peta di bawah akan bergerak otomatis tanpa perlu input koordinat manual.</small>
                </div>

                <hr class="my-4">

                <h3 class="h6 mb-2 text-dark fw-bold">Lokasi Rumah di Peta</h3>
                <p class="text-muted small">Titik lokasi rumah Anda di peta digunakan untuk perhitungan ongkir & rute penjemputan driver.</p>
                
                <div id="profilMap" style="height: 250px; border-radius: 0.5rem;" class="border mb-3"></div>

                <input type="hidden" name="latitude" id="profLat" value="{{ old('latitude', $user->latitude) }}">
                <input type="hidden" name="longitude" id="profLng" value="{{ old('longitude', $user->longitude) }}">
            </div>

            <!-- GANTI PASSWORD -->
            <div class="page-card p-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>🔒</span> Ganti Kata Sandi (Opsional)</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Kata Sandi Baru</label>
                        <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diganti">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Konfirmasi kata sandi baru">
                    </div>
                </div>
            </div>
        </div>

        <!-- REKENING & E-WALLET -->
        <div class="col-lg-5">
            <div class="page-card p-4 mb-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>💳</span> Informasi Pencairan Dana / Transfer</h2>
                <p class="text-muted small">Silakan pilih dan isi akun rekening bank atau e-wallet Anda. Pembayaran hasil penjualan sampah akan ditransfer ke akun ini.</p>

                <!-- Transfer Bank -->
                <div class="p-3 border rounded-3 mb-3 bg-light">
                    <h3 class="h6 mb-3 text-dark fw-bold">1. Akun Rekening Bank</h3>
                    <div class="mb-2">
                        <label class="form-label small">Nama Bank</label>
                        <select class="form-select" name="bank_nama">
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA" {{ old('bank_nama', $user->bank_nama) == 'BCA' ? 'selected' : '' }}>Bank Central Asia (BCA)</option>
                            <option value="Mandiri" {{ old('bank_nama', $user->bank_nama) == 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                            <option value="BRI" {{ old('bank_nama', $user->bank_nama) == 'BRI' ? 'selected' : '' }}>Bank Rakyat Indonesia (BRI)</option>
                            <option value="BNI" {{ old('bank_nama', $user->bank_nama) == 'BNI' ? 'selected' : '' }}>Bank Negara Indonesia (BNI)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small">Nomor Rekening</label>
                        <input type="text" class="form-control" name="bank_nomor" value="{{ old('bank_nomor', $user->bank_nomor) }}" placeholder="Contoh: 123456789">
                    </div>
                </div>

                <!-- E-Wallet -->
                <div class="p-3 border rounded-3 mb-3 bg-light">
                    <h3 class="h6 mb-3 text-dark fw-bold">2. Akun E-Wallet</h3>
                    <div class="mb-2">
                        <label class="form-label small">Layanan E-Wallet</label>
                        <select class="form-select" name="ewallet_nama">
                            <option value="">-- Pilih E-Wallet --</option>
                            <option value="DANA" {{ old('ewallet_nama', $user->ewallet_nama) == 'DANA' ? 'selected' : '' }}>DANA</option>
                            <option value="OVO" {{ old('ewallet_nama', $user->ewallet_nama) == 'OVO' ? 'selected' : '' }}>OVO</option>
                            <option value="GoPay" {{ old('ewallet_nama', $user->ewallet_nama) == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                            <option value="LinkAja" {{ old('ewallet_nama', $user->ewallet_nama) == 'LinkAja' ? 'selected' : '' }}>LinkAja</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small">Nomor E-Wallet (No. HP Terdaftar)</label>
                        <input type="text" class="form-control" name="ewallet_nomor" value="{{ old('ewallet_nomor', $user->ewallet_nomor) }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="alert alert-warning small py-2 px-3">
                    <strong>Catatan:</strong> Pastikan nama pemilik rekening/e-wallet sesuai dengan nama akun Anda untuk kelancaran transaksi pencairan.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">Simpan Perubahan</button>
        </div>
    </form>
</div>

<!-- Leaflet Peta Script untuk Profil -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const warehouseLat = {{ $warehouseLat }};
        const warehouseLng = {{ $warehouseLng }};
        
        let defaultLat = parseFloat(document.getElementById('profLat').value) || (warehouseLat - 0.005);
        let defaultLng = parseFloat(document.getElementById('profLng').value) || (warehouseLng + 0.005);

        document.getElementById('profLat').value = defaultLat.toFixed(6);
        document.getElementById('profLng').value = defaultLng.toFixed(6);

        const map = L.map('profilMap').setView([defaultLat, defaultLng], 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([warehouseLat, warehouseLng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#12355b; color:white; padding:4px 8px; border-radius:5px; border:2px solid white; font-weight:bold; font-size:10px; white-space:nowrap;'>🏬 Gudang Pengepul</div>",
                iconSize: [100, 24],
                iconAnchor: [50, 12]
            })
        }).addTo(map);

        const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('profLat').value = lat.toFixed(6);
            document.getElementById('profLng').value = lng.toFixed(6);
        }

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        // Profile Address Autocomplete Search
        var searchInput = document.getElementById('profAlamatSearch');
        var searchResults = document.getElementById('profSearchResults');
        var debounceTimer = null;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var query = this.value.trim();
                clearTimeout(debounceTimer);

                if (query.length < 3) {
                    searchResults.classList.add('d-none');
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
                                        marker.setLatLng([lat, lon]);
                                        map.panTo([lat, lon]);
                                        updateCoords(lat, lon);
                                    });
                                    searchResults.appendChild(div);
                                });
                            } else {
                                searchResults.classList.add('d-none');
                            }
                        });
                }, 400);
            });
        }
    });
</script>
@endsection
