@extends('index')
@section('title', 'Tugas Kurir Driver Pengepul')
@section('isihalaman')
<style>
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
        background-color: #198754;
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
                <i class="fa-solid fa-truck-moving text-warning"></i> Tugas Kurir Penjemputan Sampah
            </h1>
            <p class="text-muted mb-0">Lihat tugas penjemputan, navigasi peta, input timbangan real, dan bayar di lokasi (Transfer / Cash).</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <!-- LIST TUGAS DRIVER -->
        <div class="col-lg-7">
            <div class="page-card p-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>🚚</span> Tugas Penjemputan Anda</h2>
                
                @forelse($myTasks as $task)
                    @php
                        $borderStatus = match($task->status) {
                            'completed' => 'border-start border-4 border-success bg-white shadow-sm',
                            'processing' => 'border-start border-4 border-warning bg-white shadow-sm',
                            'scheduled' => 'border-start border-4 border-info bg-white shadow-sm',
                            'cancelled' => 'border-start border-4 border-danger bg-white shadow-sm',
                            default => 'border-start border-4 border-secondary bg-white shadow-sm'
                        };
                        $badgeClass = match($task->status) {
                            'completed' => 'badge-soft-success',
                            'processing' => 'badge-soft-warning',
                            'scheduled' => 'badge-soft-info',
                            'cancelled' => 'badge-soft-danger',
                            default => 'badge-soft-secondary'
                        };
                    @endphp
                    <div class="p-4 rounded-3 mb-3 border {{ $borderStatus }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="font-monospace fw-bold text-dark fs-6">#{{ $task->order_no }}</span>
                                <div class="text-dark small mt-1">Warga: <strong class="text-primary">{{ $task->warga->name ?? '-' }}</strong> ({{ $task->warga->no_hp ?? 'No HP -' }})</div>
                                <div class="text-secondary small"><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $task->warga->alamat ?? 'Lihat Pin Peta' }}</div>
                            </div>
                            <span class="badge {{ $badgeClass }} badge-pill-custom text-capitalize px-3 py-1.5 fw-bold">
                                {{ $task->status === 'completed' ? '✓ Selesai' : $task->status }}
                            </span>
                        </div>

                        <div class="row g-2 my-2 py-2 border-top border-bottom small text-dark">
                            <div class="col-6"><i class="fa-regular fa-calendar me-1 text-primary"></i> Jadwal: <strong>{{ $task->tgl_jemput }}</strong> ({{ $task->jam_jemput }})</div>
                            <div class="col-6"><i class="fa-solid fa-route me-1 text-info"></i> Jarak: <strong>{{ round($task->jarak_km, 2) }} km</strong></div>
                            <div class="col-12 mt-2">
                                <strong><i class="fa-solid fa-boxes-stacked me-1 text-warning"></i> Estimasi Jenis Sampah:</strong>
                                <ul class="mb-0 mt-1 text-secondary ps-3">
                                    @foreach($task->items as $itm)
                                        <li><strong>{{ $itm->material->nama_material ?? 'Material' }}</strong> (Estimasi: {{ $itm->estimasi_berat }} kg)</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @if($task->status === 'scheduled')
                                <form action="{{ route('pengepul.driver.pickup.start', $task->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button class="btn btn-sm btn-warning w-100 py-2 fw-bold text-dark rounded-pill">
                                        <i class="fa-solid fa-play me-1"></i> Mulai Perjalanan Ke Lokasi
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showRouteOnMap({{ $task->latitude }}, {{ $task->longitude }}, '{{ addslashes($task->warga->name ?? 'Warga') }}')">
                                    <i class="fa-solid fa-map me-1"></i> Peta Rute
                                </button>
                            @elseif($task->status === 'processing')
                                <button type="button" 
                                        class="btn btn-sm btn-success flex-grow-1 py-2 fw-bold rounded-pill shadow-sm" 
                                        onclick="triggerDriverTimbangModal({{ $task->id }})">
                                    <i class="fa-solid fa-scale-balanced me-1"></i> Timbang & Bayar (Transfer/Cash)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="showRouteOnMap({{ $task->latitude }}, {{ $task->longitude }}, '{{ addslashes($task->warga->name ?? 'Warga') }}')">
                                    <i class="fa-solid fa-map me-1"></i> Peta Rute
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="updateDriverLiveLocation({{ $task->id }})">
                                    <i class="fa-solid fa-location-arrow me-1"></i> Kirim Posisi
                                </button>
                            @elseif($task->status === 'completed')
                                <div class="w-100 bg-success bg-opacity-10 text-success fw-bold small p-3 border border-success rounded-3 text-center mb-2">
                                    <i class="fa-solid fa-circle-check me-1"></i> Penjemputan Selesai! Final Bayar ke Warga: <strong>Rp {{ number_format($task->total_final_harga, 0, ',', '.') }}</strong> ({{ strtoupper($task->metode_pembayaran ?: 'CASH') }})
                                    @if($task->bukti_transfer)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-success text-white fw-bold py-1 px-3 rounded-pill shadow-sm" onclick="openBuktiTransferModal('{{ asset($task->bukti_transfer) }}')">
                                                <i class="fa-solid fa-receipt me-1"></i> Lihat Struk Pembayaran
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Chat, Surat Jalan, & WA Buttons -->
                            <div class="d-flex flex-wrap gap-2 w-100 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1.5 px-3" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $task->id) }}')">
                                    <i class="fa-solid fa-scroll me-1"></i> Surat Jalan
                                </button>

                                <button type="button" class="btn btn-sm btn-dark rounded-pill py-1.5 px-3 flex-grow-1" onclick="openDriverChatModal({{ $task->id }}, '{{ $task->order_no }}')">
                                    <i class="fa-solid fa-comments me-1"></i> Chat Web Warga
                                </button>

                                @php
                                    $wargaHp = preg_replace('/[^0-9]/', '', (string)($task->warga->no_hp ?? ''));
                                    if (str_starts_with($wargaHp, '0')) {
                                        $wargaHp = '62' . substr($wargaHp, 1);
                                    }
                                    $waMsg = rawurlencode("Halo Kak " . ($task->warga->name ?? '') . ", saya petugas pengepul untuk penjemputan order #" . $task->order_no . ".");
                                @endphp
                                @if(!empty($wargaHp))
                                    <a href="https://wa.me/{{ $wargaHp }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-success rounded-pill py-1.5 px-3 fw-bold">
                                        <i class="fa-brands fa-whatsapp me-1"></i> WA
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <div class="fs-1 mb-2">📦</div>
                        <div>Tidak ada tugas penjemputan sampah yang ditugaskan kepada Anda saat ini.</div>
                    </div>
                @endforelse
                <div class="mt-3">{{ $myTasks->links() }}</div>
            </div>
        </div>

        <!-- PETA NAVIGASI RUTE DRIVER -->
        <div class="col-lg-5">
            <div class="page-card p-4 h-100 d-flex flex-column">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>🗺️</span> Peta Navigasi Penjemputan</h2>
                <div id="driverMap" style="height: 350px; border-radius: 0.5rem;" class="border mb-2"></div>
                <small class="text-muted">Garis biru menunjukkan rute penjemputan dari gudang ke titik lokasi rumah warga.</small>
            </div>
        </div>
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

<!-- Modal Timbang Sampah & Pembayaran (Cash / Transfer) -->
<div class="modal fade" id="timbangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="timbangForm" action="" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">⚖️ Timbang Sampah & Pembayaran - <span id="timbangOrderNo"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Opsi Metode Pembayaran ke Warga:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked onchange="toggleTransferFields()">
                            <label class="btn btn-outline-success w-100 p-3 text-start" for="payCash">
                                <div class="fw-bold fs-6">💵 Bayar Tunai (Cash)</div>
                                <small class="text-muted d-block">Serahkan uang tunai langsung ke warga di lokasi.</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="payTransfer" value="transfer" onchange="toggleTransferFields()">
                            <label class="btn btn-outline-primary w-100 p-3 text-start" for="payTransfer">
                                <div class="fw-bold fs-6">💳 Transfer ke Saldo Customer</div>
                                <small class="text-muted d-block">Otomatis masuk langsung ke Saldo Akun Customer.</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Info Box -->
                <div id="cashInfoBox" class="p-3 bg-success bg-opacity-10 border border-success rounded-3 mb-3">
                    <div class="d-flex align-items-center gap-2 text-success fw-bold">
                        <span>💵</span> Pembayaran Tunai (Cash)
                    </div>
                    <small class="text-muted d-block mt-1">Serahkan uang tunai langsung ke warga di lokasi penjemputan.</small>
                </div>

                <!-- Transfer Info Box -->
                <div id="transferInfoBox" class="p-3 bg-primary bg-opacity-10 border border-primary rounded-3 mb-3 d-none">
                    <div class="d-flex align-items-center gap-2 text-primary fw-bold mb-1">
                        <span>💳</span> Transfer Otomatis ke Saldo Customer
                    </div>
                    <small class="text-muted d-block">Sistem akan secara otomatis mentransfer total pembayaran langsung ke **Saldo Digital Akun Customer** tanpa perlu mengunggah bukti fisik.</small>
                </div>

                <div id="timbangItemsContainer" class="mb-3">
                    <!-- Dynamic inputs populated in JS -->
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Biaya Transport (Deducted)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="timbangBiayaJemput" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Pembayaran / Transaksi (Opsional)</label>
                    <input type="text" name="catatan_pembayaran" class="form-control" placeholder="Misal: Sudah ditransfer via BCA a.n Warga">
                </div>

                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mt-2 border">
                    <div class="fw-bold text-dark">Total Pembayaran Beres ke Warga:</div>
                    <div class="fs-4 fw-bold text-success" id="timbangTotalFinalDisplay">Rp 0</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold py-2 px-4">✓ Selesaikan & Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Driver Web Chat -->
<div class="modal fade" id="driverChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="driverChatTitle">💬 Chat Web Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Preset Template Message Buttons -->
                <div class="mb-2">
                    <small class="text-muted fw-semibold d-block mb-1">Pesan Otomatis Petugas (Klik untuk Mengirim):</small>
                    <div class="d-flex flex-wrap gap-1">
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendDriverPresetChat('Halo Kak, saya petugas pengepul sudah menuju ke lokasi Anda.')">
                            ⚡ Sudah meluncur ke lokasi
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendDriverPresetChat('Saya sudah sampai di depan rumah/lokasi.')">
                            ⚡ Sudah di depan lokasi
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendDriverPresetChat('Barang sedang kami timbang ya kak.')">
                            ⚡ Sedang ditimbang
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendDriverPresetChat('Pembayaran telah berhasil kami proses. Terima kasih!')">
                            ⚡ Pembayaran selesai
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Box -->
                <div id="driverChatMessagesBox" class="chat-box mb-3 d-flex flex-column"></div>

                <!-- Chat Input Form -->
                <form id="driverChatForm" onsubmit="submitDriverChatMessage(event)">
                    <input type="hidden" id="driverChatOrderId">
                    <div class="input-group">
                        <input type="text" id="driverChatInputMessage" class="form-control" placeholder="Tulis pesan ke warga..." required>
                        <button class="btn btn-success" type="submit">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Global Driver Data Storage -->
<script>
    window.driverTasksData = {};
    @foreach($myTasks as $task)
        window.driverTasksData[{{ $task->id }}] = {
            id: {{ $task->id }},
            order_no: "{{ $task->order_no }}",
            biaya_jemput: {{ $task->biaya_jemput ?: 0 }},
            warga: @json($task->warga),
            items: @json($task->items)
        };
    @endforeach
</script>

<!-- Leaflet Peta Script untuk Rute Driver -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let driverMap = null;
    let gudangMarker = null;
    let wargaMarker = null;
    let routeLine = null;

    window.driverTasksData = {};
    @if(isset($myTasks))
        @foreach($myTasks as $task)
            window.driverTasksData[{{ $task->id }}] = {
                id: {{ $task->id }},
                order_no: "{{ $task->order_no }}",
                biaya_jemput: {{ $task->biaya_jemput ?: 0 }},
                warga: @json($task->warga),
                items: @json($task->items)
            };
        @endforeach
    @endif

    function triggerDriverTimbangModal(orderId) {
        var tsk = window.driverTasksData ? window.driverTasksData[orderId] : null;
        if (!tsk) return;
        openTimbangModal(tsk.id, tsk.order_no, tsk.items, tsk.biaya_jemput, tsk.warga);
    }

    const warehouseLat = -6.2088;
    const warehouseLng = 106.8456;

    document.addEventListener('DOMContentLoaded', function() {
        driverMap = L.map('driverMap').setView([warehouseLat, warehouseLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map = driverMap);

        gudangMarker = L.marker([warehouseLat, warehouseLng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#12355b; color:white; padding:4px 8px; border-radius:5px; border:2px solid white; font-weight:bold; font-size:10px;'>🏬 Gudang</div>",
                iconSize: [60, 24],
                iconAnchor: [30, 12]
            })
        }).addTo(driverMap);
    });

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

    function triggerDriverTimbangModal(taskId) {
        var data = window.driverTasksData[taskId];
        if (!data) return;
        openTimbangModal(data.id, data.order_no, data.items || [], data.biaya_jemput || 0, data.warga || {});
    }

    function showRouteOnMap(lat, lng, wargaName) {
        if (!driverMap) return;

        if (wargaMarker) driverMap.removeLayer(wargaMarker);
        if (routeLine) driverMap.removeLayer(routeLine);

        wargaMarker = L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#28a745; color:white; padding:4px 8px; border-radius:5px; border:2px solid white; font-weight:bold; font-size:10px; white-space:nowrap;'>👤 Rumah " + wargaName + "</div>",
                iconSize: [120, 24],
                iconAnchor: [60, 12]
            })
        }).addTo(driverMap);

        routeLine = L.polyline([
            [warehouseLat, warehouseLng],
            [lat, lng]
        ], {
            color: 'blue',
            weight: 4,
            opacity: 0.7,
            dashArray: '5, 10'
        }).addTo(driverMap);

        const group = new L.featureGroup([gudangMarker, wargaMarker]);
        driverMap.fitBounds(group.getBounds().pad(0.1));
    }

    function toggleTransferFields() {
        var isTransfer = document.getElementById('payTransfer').checked;
        var cashBox = document.getElementById('cashInfoBox');
        var transferBox = document.getElementById('transferInfoBox');

        if (isTransfer) {
            cashBox.classList.add('d-none');
            transferBox.classList.remove('d-none');
        } else {
            cashBox.classList.remove('d-none');
            transferBox.classList.add('d-none');
        }
    }

    function openTimbangModal(orderId, orderNo, items, biayaJemput, warga) {
        const actionUrl = "{{ url('/pengepul/driver/pickup/complete') }}/" + orderId;
        document.getElementById('timbangForm').action = actionUrl;
        document.getElementById('timbangOrderNo').textContent = orderNo;
        document.getElementById('timbangBiayaJemput').value = biayaJemput.toLocaleString('id-ID');

        var elBankNama = document.getElementById('wargaBankNama');
        if (elBankNama) elBankNama.innerText = warga && warga.bank_nama ? warga.bank_nama : 'Belum Diisi';
        var elBankNomor = document.getElementById('wargaBankNomor');
        if (elBankNomor) elBankNomor.innerText = warga && warga.bank_nomor ? warga.bank_nomor : '-';
        var elEwalletNama = document.getElementById('wargaEwalletNama');
        if (elEwalletNama) elEwalletNama.innerText = warga && warga.ewallet_nama ? warga.ewallet_nama : 'Belum Diisi';
        var elEwalletNomor = document.getElementById('wargaEwalletNomor');
        if (elEwalletNomor) elEwalletNomor.innerText = warga && warga.ewallet_nomor ? warga.ewallet_nomor : '-';

        var payCashEl = document.getElementById('payCash');
        if (payCashEl) payCashEl.checked = true;
        toggleTransferFields();

        const container = document.getElementById('timbangItemsContainer');
        if (container) {
            container.innerHTML = '';
            items.forEach(function(item) {
                const materialName = item.material ? item.material.nama_material : 'Material Sampah';
                const html = `
                    <div class="row align-items-center mb-3 p-2 border rounded bg-white item-timbang-row" data-price="${item.harga_beli_per_kg}">
                        <div class="col-md-5">
                            <strong>${materialName}</strong><br>
                            <span class="text-muted small">Harga Beli: Rp ${item.harga_beli_per_kg.toLocaleString('id-ID')} / kg</span>
                            <div class="text-primary small">Estimasi Berat: ${item.estimasi_berat} kg</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Berat Timbangan Real (kg)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="weights[${item.id}]" class="form-control timbang-weight-input" value="${item.estimasi_berat}" required min="0" oninput="recalculateTotalPayout()">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += html;
            });
        }

        recalculateTotalPayout();
        
        var elModal = document.getElementById('timbangModal');
        if (!elModal) return;
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#timbangModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(elModal) || new bootstrap.Modal(elModal);
            modal.show();
        } else {
            elModal.classList.add('show');
            elModal.style.display = 'block';
        }
    }

    function recalculateTotalPayout() {
        const rows = document.querySelectorAll('.item-timbang-row');
        let total = 0;

        rows.forEach(function(row) {
            const price = parseFloat(row.getAttribute('data-price'));
            const input = row.querySelector('.timbang-weight-input');
            const weight = parseFloat(input.value) || 0;
            total += weight * price;
        });

        const biayaJemput = parseFloat(document.getElementById('timbangBiayaJemput').value.replace(/\./g, '')) || 0;
        let finalTotal = total - biayaJemput;
        if (finalTotal < 0) finalTotal = 0;

        document.getElementById('timbangTotalFinalDisplay').textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
    }

    function copyDriverClipboard(text) {
        if (!text || text === '-') {
            alert('Nomor rekening/e-wallet tidak tersedia.');
            return;
        }
        navigator.clipboard.writeText(text).then(function() {
            alert('Nomor disalin ke clipboard: ' + text);
        }).catch(function() {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            alert('Nomor disalin: ' + text);
        });
    }

    function updateDriverLiveLocation(orderId) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                fetch(`/pengepul/driver/location/update/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(res => res.json())
                .then(data => {
                    alert('Posisi terkini lokasi Anda berhasil dikirim ke warga!');
                });
            }, function() {
                alert('Gagal mengakses GPS perangkat Anda.');
            });
        } else {
            alert('Browser tidak mendukung Geolocation.');
        }
    }

    // Driver Chat Functionality
    let currentDriverChatOrderId = null;
    let driverChatIntervalId = null;

    function openDriverChatModal(orderId, orderNo) {
        currentDriverChatOrderId = orderId;
        document.getElementById('driverChatOrderId').value = orderId;
        document.getElementById('driverChatTitle').innerText = '💬 Chat Order #' + orderNo;

        var modalEl = document.getElementById('driverChatModal');
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $(modalEl).modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }

        loadDriverChatMessages(orderId);

        if (driverChatIntervalId) clearInterval(driverChatIntervalId);
        driverChatIntervalId = setInterval(function() {
            if (currentDriverChatOrderId) {
                loadDriverChatMessages(currentDriverChatOrderId);
            }
        }, 3000);

        if (typeof $ !== 'undefined') {
            $(modalEl).off('hidden.bs.modal').on('hidden.bs.modal', function () {
                if (driverChatIntervalId) clearInterval(driverChatIntervalId);
                driverChatIntervalId = null;
            });
        }
    }

    function loadDriverChatMessages(orderId) {
        fetch(`/pengepul/chat/fetch/${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    var box = document.getElementById('driverChatMessagesBox');
                    var isAtBottom = box.scrollHeight - box.clientHeight <= box.scrollTop + 50;
                    box.innerHTML = '';
                    if (data.chats.length === 0) {
                        box.innerHTML = '<div class="text-center text-muted my-auto">Belum ada pesan dengan warga.</div>';
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

    function sendDriverPresetChat(msg) {
        document.getElementById('driverChatInputMessage').value = msg;
        submitDriverChatMessage({ preventDefault: function(){} });
    }

    function submitDriverChatMessage(e) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }
        var msgInput = document.getElementById('driverChatInputMessage');
        var msg = msgInput.value.trim();
        var orderId = currentDriverChatOrderId;

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
                loadDriverChatMessages(orderId);
            }
        });
    }
</script>
@endsection
