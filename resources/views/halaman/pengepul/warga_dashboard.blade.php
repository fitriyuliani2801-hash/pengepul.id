@extends('index')
@section('title', 'Jual Sampah Warga')
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
    .chat-box {
        height: 320px;
        overflow-y: auto;
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
    }
    .chat-bubble {
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 12px;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }
    .chat-bubble-me {
        background-color: #0d6efd;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 2px;
    }
    .chat-bubble-other {
        background-color: #e2e8f0;
        color: #1e293b;
        margin-right: auto;
        border-bottom-left-radius: 2px;
    }
</style>

<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-success"></i> Setor & Jual Sampah
            </h1>
            <p class="text-muted mb-0">Tawarkan sampah daur ulang Anda ke Pengepul langsung dari rumah.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- WALLET & SALDO DIGITAL WARGA -->
    <div class="page-card p-4 mb-4 hero-gradient-bg text-white shadow-lg" style="border-radius: var(--radius-xl);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative z-1">
            <div>
                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 mb-2 small fw-semibold" style="letter-spacing: 0.05em; backdrop-filter: blur(8px);">
                    <i class="fa-solid fa-wallet me-1"></i> Saldo Digital Penjualan Sampah
                </span>
                <h2 class="display-6 fw-extrabold mb-1 text-white">
                    Rp {{ number_format(auth()->user()->saldo ?? 0, 0, ',', '.') }}
                </h2>
                <p class="text-white-50 mb-0 small">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> Transfer otomatis masuk setiap transaksi penjemputan selesai.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-light text-primary rounded-pill px-4 fw-bold shadow-sm" onclick="openModalTarikSaldo()">
                    <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Tarik Dana / Transfer
                </button>
                <button type="button" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm" onclick="openGeneralChatModal()">
                    <i class="fa-solid fa-comments me-1"></i> 💬 Chat Driver / Admin
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- KATALOG & KERANJANG -->
        <div class="col-lg-7">
            <!-- Katalog Harga -->
            <div class="page-card p-4 mb-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>♻️</span> Daftar Harga Sampah Hari Ini</h2>
                <div class="row g-2">
                    @foreach($katalog as $kat)
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fs-2">{{ $kat->icon ?: '📦' }}</span>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $kat->nama_material }}</div>
                                        <div class="text-success small fw-semibold">Rp {{ number_format($kat->harga_beli_per_kg, 0, ',', '.') }} / kg</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="openAddCartModal({{ $kat->id }}, '{{ $kat->nama_material }}', {{ $kat->harga_beli_per_kg }})">+ Jual</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Keranjang Penjualan -->
            <div class="page-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2"><span>🛒</span> Keranjang Sampah</h2>
                    @if(count($cart) > 0)
                        <form action="{{ route('pengepul.warga.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan keranjang?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger">Kosongkan</button>
                        </form>
                    @endif
                </div>

                @if(count($cart) > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Estimasi Berat</th>
                                    <th>Harga / kg</th>
                                    <th>Estimasi Subtotal</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                    <tr>
                                        <td><span class="fs-4 me-2">{{ $item['icon'] }}</span> <strong>{{ $item['nama'] }}</strong></td>
                                        <td>{{ $item['berat'] }} kg</td>
                                        <td>Rp {{ number_format($item['harga_per_kg'], 0, ',', '.') }}</td>
                                        <td class="text-success fw-bold">Rp {{ number_format($item['estimasi_subtotal'], 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('pengepul.warga.cart.remove', $id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger py-0 px-2">&times;</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="3" class="fw-bold">Total Estimasi Pendapatan:</td>
                                    <td colspan="2" class="fw-bold text-success fs-5">Rp {{ number_format(collect($cart)->sum('estimasi_subtotal'), 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <div class="fs-1 mb-2">📥</div>
                        <div>Keranjang kosong. Pilih material sampah di atas untuk ditawarkan.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- PETA & CHECKOUT -->
        <div class="col-lg-5">
            <div class="page-card p-4 h-100 d-flex flex-column">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>📍</span> Lokasi Penjemputan</h2>
                
                @if(count($cart) > 0)
                    <form action="{{ route('pengepul.warga.checkout') }}" method="POST" class="d-flex flex-column flex-grow-1">
                        @csrf
                        
                        <!-- Autocomplete Alamat -->
                        <div class="mb-3 position-relative">
                            <label class="form-label small fw-semibold">Cari Alamat Penjemputan:</label>
                            <input type="text" id="alamat_search_checkout" class="form-control form-control-sm" placeholder="Ketik nama jalan atau area..." autocomplete="off" value="{{ auth()->user()->alamat }}">
                            <div id="searchResultsCheckout" class="search-results-list d-none"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Lokasi Penjemputan di Peta:</label>
                            <div id="map" style="height: 220px; border-radius: 0.5rem;" class="border mb-2"></div>
                            <small class="text-muted d-block">Geser penanda jika lokasi belum pas.</small>
                        </div>

                        <!-- Hidden Lat Long -->
                        <input type="hidden" name="latitude" id="latInput" value="{{ auth()->user()->latitude ?: '-6.2088' }}" required>
                        <input type="hidden" name="longitude" id="lngInput" value="{{ auth()->user()->longitude ?: '106.8456' }}" required>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Jarak dari Gudang:</span>
                                <span id="distDisplay" class="fw-bold text-dark">—</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Biaya Penjemputan:</span>
                                <span id="feeDisplay" class="fw-bold text-danger">—</span>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size:0.75rem;">* Gratis jemput untuk jarak &le; 2 km ATAU berat sampah &ge; 10 kg.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tanggal Penjemputan</label>
                            <input type="date" class="form-control" name="tgl_jemput" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Pilih Jam Penjemputan</label>
                            <select class="form-select" name="jam_jemput" required>
                                <option value="Pagi (08:00 - 12:00)">Pagi (08:00 - 12:00)</option>
                                <option value="Siang (12:00 - 15:00)">Siang (12:00 - 15:00)</option>
                                <option value="Sore (15:00 - 17:00)">Sore (15:00 - 17:00)</option>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100 py-2 mt-auto">Pesan Penjemputan (Checkout)</button>
                    </form>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-center py-5 text-muted">
                        <div class="fs-1 mb-2">🛒</div>
                        <div>Isi keranjang Anda terlebih dahulu untuk menjadwalkan penjemputan.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIWAYAT PENJUALAN -->
    <div class="page-card p-4 mt-4">
        <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>📜</span> Riwayat Permintaan Penjemputan</h2>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Jadwal Jemput</th>
                        <th>Jarak & Biaya</th>
                        <th>Estimasi / Final Pembayaran</th>
                        <th>Metode Bayar</th>
                        <th>Petugas / Kurir</th>
                        <th>Status</th>
                        <th class="text-center">Aksi Komunikasi & Tracking</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myOrders as $ord)
                        <tr>
                            <td><span class="font-monospace fw-bold text-dark">{{ $ord->order_no }}</span></td>
                            <td>
                                <div>{{ $ord->tgl_jemput }}</div>
                                <small class="text-muted">{{ $ord->jam_jemput }}</small>
                            </td>
                            <td>
                                <div>{{ round($ord->jarak_km, 2) }} km</div>
                                @if($ord->biaya_jemput > 0)
                                    <small class="text-danger">Ongkir: Rp {{ number_format($ord->biaya_jemput, 0, ',', '.') }}</small>
                                @else
                                    <span class="badge bg-success">Gratis Ongkir</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-primary small">Est: Rp {{ number_format($ord->total_estimasi_harga, 0, ',', '.') }}</div>
                                <div class="text-success fw-bold">
                                    @if($ord->status === 'completed')
                                        Final: Rp {{ number_format($ord->total_final_harga, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted small">Menunggu penimbangan</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($ord->status === 'completed')
                                     <span class="badge bg-{{ $ord->metode_pembayaran === 'transfer' ? 'primary' : 'success' }} text-white text-capitalize">
                                         {{ $ord->metode_pembayaran === 'transfer' ? '💳 Transfer Bank/E-Wallet' : '💵 Cash (Tunai)' }}
                                     </span>
                                     @if($ord->bukti_transfer)
                                         <div class="mt-1">
                                             <button type="button" class="btn btn-sm btn-primary text-white py-0 px-2 fw-semibold shadow-sm" style="font-size: 0.75rem;" onclick="openBuktiTransferModal('{{ asset($ord->bukti_transfer) }}')">
                                                 📄 Bukti Transfer
                                             </button>
                                         </div>
                                     @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($ord->driver)
                                    <div class="fw-semibold text-dark">🚚 {{ $ord->driver->name }}</div>
                                    <small class="text-muted">{{ $ord->driver->no_hp ?? '-' }}</small>
                                @else
                                    <span class="text-muted small">Menunggu Petugas</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match($ord->status) {
                                        'pending' => 'secondary',
                                        'scheduled' => 'info',
                                        'processing' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }} text-capitalize">{{ $ord->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    <!-- Button Cetak Surat Jalan -->
                                    <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $ord->id) }}')">
                                        📜 Surat Jalan
                                    </button>

                                    <!-- Button Live Tracking -->
                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" onclick="openTrackingModal({{ $ord->id }})">
                                        📍 Lacak
                                    </button>

                                    <!-- Button Web Chat -->
                                    <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2" onclick="openChatModal({{ $ord->id }}, '{{ $ord->order_no }}')">
                                        💬 Chat Web
                                    </button>

                                    <!-- Button Direct WhatsApp -->
                                    @php
                                        $targetHp = $ord->driver ? ($ord->driver->no_hp ?? '') : '';
                                        $cleanHp = preg_replace('/[^0-9]/', '', (string)$targetHp);
                                        if (str_starts_with($cleanHp, '0')) {
                                            $cleanHp = '62' . substr($cleanHp, 1);
                                        }
                                        $waText = rawurlencode("Halo Kak, saya Warga pemilik Order #" . $ord->order_no . ". Menanyakan status penjemputan sampah.");
                                    @endphp
                                    @if(!empty($cleanHp))
                                        <a href="https://wa.me/{{ $cleanHp }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-success py-1 px-2">
                                            🟢 WA
                                        </a>
                                    @endif

                                    @if(in_array($ord->status, ['pending', 'scheduled', 'processing']))
                                        <form action="{{ route('pengepul.warga.order.cancel', $ord->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan penjemputan ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                                🚫 Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat transaksi penjemputan sampah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $myOrders->links() }}</div>
    </div>

    <!-- SEKSI CHAT LANGSUNG CUSTOMER & DRIVER -->
    <div class="page-card p-4 mt-4 shadow-sm rounded-4 border-0" id="seksiChatCustomer">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2">
                <span class="fs-4">💬</span> <span>Live Chat Penjemputan (Customer & Driver)</span>
            </h2>
            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size: 0.6rem;"></i> Terhubung Real-Time</span>
        </div>

        @if($myOrders && $myOrders->count() > 0)
            <div class="row g-3">
                <div class="col-md-4 border-end">
                    <label class="form-label fw-bold text-dark small mb-2"><i class="fa-solid fa-list-check me-1"></i> Pilih Pesanan Penjemputan:</label>
                    <div class="list-group list-group-flush overflow-auto" style="max-height: 380px;">
                        @foreach($myOrders as $idx => $ord)
                            <button type="button" 
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-start py-3 rounded-3 mb-1 border {{ $idx === 0 ? 'active' : '' }}" 
                                    onclick="selectCustomerChatOrder({{ $ord->id }}, '{{ $ord->order_no }}', this)">
                                <div>
                                    <div class="fw-bold">Order #{{ $ord->order_no }}</div>
                                    <small class="d-block text-opacity-75">🚚 Driver: {{ $ord->driver ? $ord->driver->name : 'Belum Ditugaskan' }}</small>
                                    <small class="d-block text-opacity-75">📅 {{ $ord->tgl_jemput }}</small>
                                </div>
                                <span class="badge bg-warning text-dark text-capitalize" style="font-size: 0.7rem;">{{ $ord->status }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-md-8 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 px-3 rounded-3 mb-2 border">
                        <span class="fw-bold text-dark" id="customerChatHeaderTitle">💬 Chat Order #{{ $myOrders->first()->order_no }}</span>
                        <small class="text-muted" id="customerChatDriverName">Petugas: {{ $myOrders->first()->driver ? $myOrders->first()->driver->name : 'Pengepul Digital' }}</small>
                    </div>

                    <!-- Preset Quick Reply Buttons -->
                    <div class="mb-2">
                        <small class="text-muted fw-semibold d-block mb-1">Balasan Cepat (Klik untuk Mengirim):</small>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill" style="font-size:0.75rem;" onclick="sendPanelPresetChat('Halo Pak Driver, saya sudah siap di lokasi rumah.')">
                                ⚡ Saya siap di lokasi
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-info py-1 px-2 rounded-pill" style="font-size:0.75rem;" onclick="sendPanelPresetChat('Apakah bapak driver sudah dekat lokasi?')">
                                ⚡ Apakah sudah dekat?
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-success py-1 px-2 rounded-pill" style="font-size:0.75rem;" onclick="sendPanelPresetChat('Sampah daur ulang sudah dibungkus rapi.')">
                                ⚡ Sampah sudah siap
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 rounded-pill" style="font-size:0.75rem;" onclick="sendPanelPresetChat('Terima kasih banyak atas penjemputannya.')">
                                ⚡ Terima kasih
                            </button>
                        </div>
                    </div>

                    <!-- Chat Messages Box -->
                    <div id="customerPanelChatBox" class="chat-box mb-3 d-flex flex-column p-3 bg-light border rounded-3" style="height: 260px; overflow-y: auto;">
                        <div class="text-center text-muted my-auto">Memuat percakapan...</div>
                    </div>

                    <!-- Chat Input Form -->
                    <form id="customerPanelChatForm" onsubmit="submitCustomerPanelChat(event)" class="mt-auto">
                        <div class="input-group">
                            <input type="text" id="customerPanelChatInput" class="form-control rounded-start-pill" placeholder="Ketik balasan pesan ke driver..." required>
                            <button class="btn btn-primary px-4 rounded-end-pill fw-bold" type="submit">
                                <i class="fa-solid fa-paper-plane me-1"></i> Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <div class="fs-2 mb-2">💬</div>
                <div>Belum ada pesanan penjemputan aktif. Silakan ajukan penjemputan sampah terlebih dahulu untuk dapat menggunakan fitur Chat Driver.</div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Preview Surat Jalan -->
<div class="modal fade" id="suratJalanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">📜 Surat Jalan Penjemputan Sampah</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <iframe id="suratJalanIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="printSuratJalanIframe()">🖨️ Cetak / Print Dokumen</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Bukti Transfer -->
<div class="modal fade" id="buktiTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">📄 Bukti Transfer / Resi Struk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-dark bg-opacity-10">
                <img id="buktiTransferImage" src="" class="img-fluid rounded shadow" alt="Bukti Transfer" style="max-height: 480px; object-fit: contain;">
            </div>
            <div class="modal-footer">
                <a id="downloadBuktiTransferLink" href="" target="_blank" class="btn btn-outline-primary btn-sm">⬇️ Unduh Gambar</a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Cart -->
<div class="modal fade" id="addCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pengepul.warga.cart.add') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="material_id" id="modalMaterialId">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMaterialTitle">Jual Sampah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Harga per kg</label>
                    <input type="text" class="form-control" id="modalMaterialPrice" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimasi Berat Sampah (kg)</label>
                    <div class="input-group">
                        <input type="number" step="0.1" class="form-control" name="estimasi_berat" required min="0.1" placeholder="Masukkan berat, misal: 2.5">
                        <span class="input-group-text">kg</span>
                    </div>
                    <small class="text-muted d-block mt-1">Estimasi berat ini akan disesuaikan saat timbangan riil oleh kurir di lapangan.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Masukkan Keranjang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tracking Driver -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="trackingTitle">📍 Lacak Lokasi Petugas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <div class="fw-bold" id="trackDriverName">Driver: —</div>
                        <small class="text-muted" id="trackDriverHp">No. HP: —</small>
                    </div>
                    <div>
                        <span class="badge bg-warning fs-6 text-capitalize" id="trackStatusBadge">Status</span>
                    </div>
                </div>
                <div id="trackingMap" style="height: 350px; border-radius: 8px;" class="border"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Web Chat -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="chatTitle">💬 Chat Penjemputan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Preset Template Message Buttons -->
                <div class="mb-2">
                    <small class="text-muted fw-semibold d-block mb-1">Pesan Otomatis (Klik untuk Mengirim):</small>
                    <div class="d-flex flex-wrap gap-1">
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendPresetChat('Halo Kak, saya sudah siap di lokasi rumah.')">
                            ⚡ Saya siap di lokasi
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendPresetChat('Apakah petugas sudah dekat lokasi?')">
                            ⚡ Apakah sudah dekat?
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendPresetChat('Sampah sudah dibungkus rapi.')">
                            ⚡ Sampah sudah siap
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Box -->
                <div id="chatMessagesBox" class="chat-box mb-3 d-flex flex-column"></div>

                <!-- Chat Input Form -->
                <form id="chatForm" onsubmit="submitChatMessage(event)">
                    <input type="hidden" id="chatOrderId">
                    <div class="input-group">
                        <input type="text" id="chatInputMessage" class="form-control" placeholder="Tulis pesan Anda..." required>
                        <button class="btn btn-primary" type="submit">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet Peta Script dan Haversine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    function openSuratJalanModal(url) {
        document.getElementById('suratJalanIframe').src = url;
        var modal = new bootstrap.Modal(document.getElementById('suratJalanModal'));
        modal.show();
    }

    function printSuratJalanIframe() {
        var iframe = document.getElementById('suratJalanIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }

    function openBuktiTransferModal(imgUrl) {
        document.getElementById('buktiTransferImage').src = imgUrl;
        document.getElementById('downloadBuktiTransferLink').href = imgUrl;
        var modal = new bootstrap.Modal(document.getElementById('buktiTransferModal'));
        modal.show();
    }

    function openAddCartModal(id, name, price) {
        document.getElementById('modalMaterialId').value = id;
        document.getElementById('modalMaterialTitle').textContent = 'Jual ' + name;
        document.getElementById('modalMaterialPrice').value = 'Rp ' + price.toLocaleString('id-ID') + ' / kg';
        
        var myModal = new bootstrap.Modal(document.getElementById('addCartModal'));
        myModal.show();
    }

    @if(count($cart) > 0)
    // Inisialisasi peta Leaflet.js Checkout
    document.addEventListener('DOMContentLoaded', function() {
        const warehouseLat = {{ $warehouseLat }};
        const warehouseLng = {{ $warehouseLng }};
        
        let defaultWargaLat = parseFloat(document.getElementById('latInput').value) || (warehouseLat - 0.005);
        let defaultWargaLng = parseFloat(document.getElementById('lngInput').value) || (warehouseLng + 0.005);

        const map = L.map('map').setView([defaultWargaLat, defaultWargaLng], 14);
        
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

        const wargaMarker = L.marker([defaultWargaLat, defaultWargaLng], { draggable: true }).addTo(map);

        function updateDistanceAndFee(lat, lng) {
            document.getElementById('latInput').value = lat.toFixed(6);
            document.getElementById('lngInput').value = lng.toFixed(6);

            const distance = calculateDistance(warehouseLat, warehouseLng, lat, lng);
            document.getElementById('distDisplay').textContent = distance.toFixed(2) + ' km';

            const totalBerat = {{ collect($cart)->sum('berat') }};
            let fee = 0;
            if (distance > 2.0 && totalBerat < 10.0) {
                fee = Math.round(distance * 2000);
            }
            
            if (fee > 0) {
                document.getElementById('feeDisplay').textContent = 'Rp ' + fee.toLocaleString('id-ID');
                document.getElementById('feeDisplay').className = 'fw-bold text-danger';
            } else {
                document.getElementById('feeDisplay').textContent = 'Gratis';
                document.getElementById('feeDisplay').className = 'fw-bold text-success';
            }
        }

        updateDistanceAndFee(defaultWargaLat, defaultWargaLng);

        wargaMarker.on('dragend', function(e) {
            const position = wargaMarker.getLatLng();
            updateDistanceAndFee(position.lat, position.lng);
        });

        map.on('click', function(e) {
            wargaMarker.setLatLng(e.latlng);
            updateDistanceAndFee(e.latlng.lat, e.latlng.lng);
        });

        // Checkout Address Search Autocomplete
        var searchInput = document.getElementById('alamat_search_checkout');
        var searchResults = document.getElementById('searchResultsCheckout');
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
                                        wargaMarker.setLatLng([lat, lon]);
                                        map.panTo([lat, lon]);
                                        updateDistanceAndFee(lat, lon);
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

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }
    @endif

    // Live Tracking Map Functionality
    let trackingMapInstance = null;
    function openTrackingModal(orderId) {
        var modal = new bootstrap.Modal(document.getElementById('trackingModal'));
        modal.show();

        fetch(`/pengepul/tracking/fetch/${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('trackingTitle').innerText = '📍 Lacak Order #' + data.order_no;
                    document.getElementById('trackDriverName').innerText = 'Driver: ' + data.driver_name;
                    document.getElementById('trackDriverHp').innerText = 'No. HP: ' + (data.driver_hp || '-');
                    document.getElementById('trackStatusBadge').innerText = data.status;

                    setTimeout(() => {
                        if (trackingMapInstance) {
                            trackingMapInstance.remove();
                        }
                        trackingMapInstance = L.map('trackingMap').setView([data.warga_lat, data.warga_lng], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(trackingMapInstance);

                        // Warga Marker
                        L.marker([data.warga_lat, data.warga_lng], {
                            icon: L.divIcon({
                                html: "<div style='background:#0d6efd; color:white; padding:4px 8px; border-radius:5px; font-weight:bold; font-size:11px;'>🏠 Rumah Anda</div>",
                                iconSize: [90, 24]
                            })
                        }).addTo(trackingMapInstance).bindPopup("Lokasi Rumah Anda");

                        // Driver Marker
                        L.marker([data.driver_lat, data.driver_lng], {
                            icon: L.divIcon({
                                html: "<div style='background:#198754; color:white; padding:4px 8px; border-radius:5px; font-weight:bold; font-size:11px;'>🚚 Petugas</div>",
                                iconSize: [80, 24]
                            })
                        }).addTo(trackingMapInstance).bindPopup("Posisi Petugas / Driver").openPopup();
                    }, 300);
                }
            });
    }

    // Web Chat Functionality
    function openChatModal(orderId, orderNo) {
        currentChatOrderId = orderId;
        document.getElementById('chatOrderId').value = orderId;
        document.getElementById('chatTitle').innerText = '💬 Chat Order #' + orderNo;

        var modalEl = document.getElementById('chatModal');
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $(modalEl).modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }

        loadChatMessages(orderId);

        if (chatIntervalId) clearInterval(chatIntervalId);
        chatIntervalId = setInterval(function() {
            if (currentChatOrderId) {
                loadChatMessages(currentChatOrderId);
            }
        }, 3000);

        if (typeof $ !== 'undefined') {
            $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', function () {
                if (chatIntervalId) clearInterval(chatIntervalId);
                chatIntervalId = null;
            });
        }
    }

    function loadChatMessages(orderId) {
        fetch(`/pengepul/chat/fetch/${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    var box = document.getElementById('chatMessagesBox');
                    var isAtBottom = box.scrollHeight - box.clientHeight <= box.scrollTop + 50;
                    box.innerHTML = '';
                    if (data.chats.length === 0) {
                        box.innerHTML = '<div class="text-center text-muted my-auto">Belum ada pesan. Gunakan template di atas atau ketik pesan baru.</div>';
                    } else {
                        data.chats.forEach(chat => {
                            var div = document.createElement('div');
                            div.className = 'chat-bubble ' + (chat.is_me ? 'chat-bubble-me' : 'chat-bubble-other');
                            div.innerHTML = `<strong>${chat.sender_name}</strong> <small style="opacity:0.75;">(${chat.created_at})</small><br>${chat.message}`;
                            box.appendChild(div);
                        });
                        if (isAtBottom || box.children.length <= 1) {
                            box.scrollTop = box.scrollHeight;
                        }
                    }
                }
            });
    }

    function sendPresetChat(msg) {
        document.getElementById('chatInputMessage').value = msg;
        submitChatMessage({ preventDefault: function(){} });
    }

    function submitChatMessage(e) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }
        var msgInput = document.getElementById('chatInputMessage');
        var msg = msgInput.value.trim();
        var orderId = currentChatOrderId;

        if (!msg || !orderId) return;

        fetch('{{ route("pengepul.chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: orderId,
                message: msg
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                msgInput.value = '';
                loadChatMessages(orderId);
            }
        });
    }

    // Dedicated Panel Chat Functionality
    let selectedCustomerChatOrderId = @json($myOrders && $myOrders->count() > 0 ? $myOrders->first()->id : null);

    function selectCustomerChatOrder(orderId, orderNo, btnEl) {
        selectedCustomerChatOrderId = orderId;
        var headerEl = document.getElementById('customerChatHeaderTitle');
        if (headerEl) headerEl.innerText = '💬 Chat Order #' + orderNo;
        
        if (btnEl) {
            document.querySelectorAll('#seksiChatCustomer .list-group-item').forEach(el => el.classList.remove('active'));
            btnEl.classList.add('active');
        }

        loadCustomerPanelChat();
    }

    function loadCustomerPanelChat() {
        if (!selectedCustomerChatOrderId) return;

        fetch(`/pengepul/chat/fetch/${selectedCustomerChatOrderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    var box = document.getElementById('customerPanelChatBox');
                    if (!box) return;
                    var isAtBottom = box.scrollHeight - box.clientHeight <= box.scrollTop + 50;
                    box.innerHTML = '';
                    if (data.chats.length === 0) {
                        box.innerHTML = '<div class="text-center text-muted my-auto">Belum ada pesan. Gunakan balasan cepat di atas atau ketik pesan baru.</div>';
                    } else {
                        data.chats.forEach(chat => {
                            var div = document.createElement('div');
                            div.className = 'chat-bubble ' + (chat.is_me ? 'chat-bubble-me' : 'chat-bubble-other');
                            div.innerHTML = `<strong>${chat.sender_name}</strong> <small style="opacity:0.75;">(${chat.created_at})</small><br>${chat.message}`;
                            box.appendChild(div);
                        });
                        if (isAtBottom || box.children.length <= 1) {
                            box.scrollTop = box.scrollHeight;
                        }
                    }
                }
            });
    }

    function sendPanelPresetChat(msg) {
        var input = document.getElementById('customerPanelChatInput');
        if (input) {
            input.value = msg;
            submitCustomerPanelChat({ preventDefault: function(){} });
        }
    }

    function submitCustomerPanelChat(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        var msgInput = document.getElementById('customerPanelChatInput');
        if (!msgInput) return;
        var msg = msgInput.value.trim();
        var orderId = selectedCustomerChatOrderId;

        if (!msg || !orderId) return;

        fetch('{{ route("pengepul.chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_id: orderId,
                message: msg
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                msgInput.value = '';
                loadCustomerPanelChat();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (selectedCustomerChatOrderId) {
            loadCustomerPanelChat();
            setInterval(function() {
                loadCustomerPanelChat();
            }, 3000);
        }
    });

    function openModalTarikSaldo() {
        var el = document.getElementById('modalTarikSaldo');
        if (!el) return;
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#modalTarikSaldo').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.show();
        } else {
            el.classList.add('show');
            el.style.display = 'block';
        }
    }
</script>

<!-- Modal Tarik Saldo -->
<div class="modal fade" id="modalTarikSaldo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-money-bill-transfer me-2"></i> Tarik Dana / Transfer Otomatis</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengepul.warga.saldo.tarik') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i> Saldo tersedia: <strong>Rp {{ number_format(auth()->user()->saldo ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Penarikan (Rp)</label>
                        <input type="number" class="form-control" name="jumlah" min="10000" max="{{ auth()->user()->saldo ?? 0 }}" placeholder="Contoh: 50000" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Minimal penarikan Rp 10.000</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bank / E-Wallet Tujuan</label>
                        <select class="form-select" name="tujuan_pembayaran" required>
                            <option value="BCA" {{ auth()->user()->bank_nama === 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="Mandiri" {{ auth()->user()->bank_nama === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                            <option value="BRI" {{ auth()->user()->bank_nama === 'BRI' ? 'selected' : '' }}>BRI</option>
                            <option value="BNI" {{ auth()->user()->bank_nama === 'BNI' ? 'selected' : '' }}>BNI</option>
                            <option value="Gopay" {{ auth()->user()->ewallet_nama === 'Gopay' ? 'selected' : '' }}>Gopay</option>
                            <option value="OVO" {{ auth()->user()->ewallet_nama === 'OVO' ? 'selected' : '' }}>OVO</option>
                            <option value="DANA" {{ auth()->user()->ewallet_nama === 'DANA' ? 'selected' : '' }}>DANA</option>
                            <option value="ShopeePay" {{ auth()->user()->ewallet_nama === 'ShopeePay' ? 'selected' : '' }}>ShopeePay</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Rekening / HP E-Wallet</label>
                        <input type="text" class="form-control" name="no_rekening" value="{{ auth()->user()->bank_nomor ?: auth()->user()->ewallet_nomor }}" placeholder="Contoh: 08123456789" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim Transfer Otomatis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('open_payout_receipt'))
    @php($receipt = session('open_payout_receipt'))
    <!-- Modal Struk Transfer Real Bank -->
    <div class="modal fade show d-block" id="payoutReceiptModal" tabindex="-1" style="background: rgba(0,0,0,0.6);" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-circle-check me-2"></i> Bukti Transfer Uang Realtime</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('payoutReceiptModal').remove()"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="fs-1 text-success mb-2">💸</div>
                    <h4 class="fw-extrabold text-success mb-1">Rp {{ number_format($receipt['amount'], 0, ',', '.') }}</h4>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mb-3 font-monospace">TRANSFER BERHASIL (BI-FAST)</span>
                    
                    <div class="border rounded-3 p-3 bg-light text-start small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Penerima:</span>
                            <strong class="text-dark">{{ $receipt['name'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Bank / E-Wallet:</span>
                            <strong class="text-dark">{{ strtoupper($receipt['bank']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">No. Rekening:</span>
                            <strong class="text-primary font-monospace">{{ $receipt['account'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">No. Referensi:</span>
                            <strong class="text-dark font-monospace" style="font-size:0.75rem;">{{ $receipt['ref_no'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Ref BI-FAST Bank:</span>
                            <strong class="text-success font-monospace" style="font-size:0.72rem;">{{ $receipt['bank_ref'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Waktu Eksekusi:</span>
                            <span class="text-dark">{{ $receipt['date'] }}</span>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3" style="font-size:0.75rem;">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Uang dikirim secara otomatis via Gateway Settlement Engine 24/7.
                    </small>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.print()">🖨️ Cetak Struk</button>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-4 fw-bold" onclick="document.getElementById('payoutReceiptModal').remove()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Floating Quick Chat Button (ALWAYS VISIBLE FOR CUSTOMER) -->
<div style="position: fixed; bottom: 25px; right: 25px; z-index: 1050;">
    <button type="button" class="btn btn-primary btn-lg rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0" 
            style="width: 62px; height: 62px; border: 3px solid #ffffff; background-color: #2563eb;" 
            onclick="openGeneralChatModal()"
            title="Buka Chat Petugas/Driver">
        <span style="font-size: 1.8rem;">💬</span>
    </button>
</div>

<script>
    function openGeneralChatModal() {
        var orderId = selectedCustomerChatOrderId || (@json($myOrders && $myOrders->count() > 0 ? $myOrders->first()->id : null)) || 1;
        var orderNo = (@json($myOrders && $myOrders->count() > 0 ? $myOrders->first()->order_no : 'Umum'));
        openChatModal(orderId, orderNo);
    }
</script>
@endsection
