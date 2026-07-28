@extends('index')
@section('title', 'Panel Pengepul Admin')
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
    @if(session('open_surat_jalan'))
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4 shadow-sm">
            <div>
                <strong>📜 Surat Jalan Berhasil Dibuat Otomatis!</strong>
                <div class="small">Petugas telah ditugaskan dan dokumen Surat Jalan resmi telah diterbitkan.</div>
            </div>
            <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="openSuratJalanModal('{{ session('open_surat_jalan') }}')">
                📄 Lihat Surat Jalan
            </button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                openSuratJalanModal("{{ session('open_surat_jalan') }}");
            });
        </script>
    @endif

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-industry text-primary"></i> Panel Admin: Kelola & Distribusi Sampah ke Supplier
            </h1>
            <p class="text-muted mb-0">Terima penjemputan warga, kelola stok gudang, kas buku, dan distribusikan/jual material ke supplier/pabrik daur ulang.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- STATS / RINGKASAN KAS & GUDANG -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="page-card p-4 bg-light h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted small fw-semibold text-uppercase">Saldo Kas Buku Pengepul</div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success">💰 Aktif</span>
                    </div>
                    <h2 class="h3 mt-2 mb-1 @if($saldoKas >= 0) text-success @else text-danger @endif">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h2>
                    <small class="text-muted d-block mb-3">Kas bertambah saat jual ke supplier & berkurang saat bayar warga.</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-success fw-bold flex-grow-1" data-toggle="modal" data-target="#topUpKasModal" data-bs-toggle="modal" data-bs-target="#topUpKasModal" onclick="openTopUpKasModal()">
                        ➕ Top Up Saldo
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="openDistribusiModal()">
                        🏭 Jual ke Supplier
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" data-toggle="modal" data-target="#riwayatKasModal" data-bs-toggle="modal" data-bs-target="#riwayatKasModal" onclick="openRiwayatKasModal()">
                        📜 Riwayat
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-muted small fw-semibold text-uppercase">Ketersediaan Stok Gudang Pengepul</div>
                    <button type="button" class="btn btn-sm btn-primary fw-bold rounded-pill px-3 py-1" onclick="openDistribusiModal()">
                        <i class="fa-solid fa-truck-ramp-box me-1"></i> + Distribusi ke Supplier
                    </button>
                </div>
                <div class="row g-2">
                    @forelse($stok as $st)
                        <div class="col-sm-6 col-md-4">
                            <div class="p-2 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-3">{{ $st->icon ?: '📦' }}</span>
                                    <div>
                                        <div class="small fw-bold text-dark">{{ $st->nama_material }}</div>
                                        <div class="text-primary small fw-semibold">{{ number_format($st->total_berat, 1, ',', '.') }} kg</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill fw-semibold" style="font-size:0.7rem;" onclick="openDistribusiModal({{ $st->material_id }})">
                                    🏭 Jual
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-2">Belum ada stok barang yang masuk di gudang.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- PETA SEBARAN JEMPUTAN & PENGATURAN HARGA -->
    <div class="row g-4 mb-4">
        <!-- Peta Sebaran Jemputan -->
        <div class="col-lg-8">
            <div class="page-card p-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>🗺️</span> Peta Sebaran Permintaan Penjemputan</h2>
                <div id="adminMap" style="height: 350px; border-radius: 0.5rem;" class="border mb-2"></div>
                <small class="text-muted">Klik pada penanda peta untuk melihat detail warga, tanggal, dan status penjemputan.</small>
            </div>
        </div>

        <!-- Katalog Harga Material -->
        <div class="col-lg-4">
            <div class="page-card p-4">
                <h2 class="h5 mb-3 text-primary d-flex align-items-center gap-2"><span>💰</span> Pengaturan Harga Sampah</h2>
                <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                    @foreach($katalog as $kat)
                        <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-3">{{ $kat->icon }}</span>
                                <div>
                                    <div class="fw-semibold small text-dark">{{ $kat->nama_material }}</div>
                                    <small class="text-success">Rp {{ number_format($kat->harga_beli_per_kg, 0, ',', '.') }} / kg</small>
                                </div>
                            </div>
                            <form action="{{ route('pengepul.admin.katalog.update-price', $kat->id) }}" method="POST" class="d-flex gap-1" style="max-width: 140px;">
                                @csrf @method('PUT')
                                <input type="number" class="form-control form-control-sm" name="harga_beli_per_kg" value="{{ $kat->harga_beli_per_kg }}" required>
                                <button class="btn btn-sm btn-primary">Ubah</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- DAFTAR PERMINTAAN JEMPUT & HISTORI -->
    <div class="page-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0 text-primary d-flex align-items-center gap-2"><span>📋</span> Daftar Permintaan & Histori Penjemputan Warga</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Warga / Nasabah</th>
                        <th>Jadwal & Lokasi</th>
                        <th>Jarak & Biaya</th>
                        <th>Estimasi Berat</th>
                        <th>Pembayaran</th>
                        <th>Kurir / Sopir</th>
                        <th>Status</th>
                        <th class="text-center">Aksi / Pembayaran / Chat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        <tr class="{{ $ord->status === 'completed' ? 'table-success bg-opacity-10' : '' }}">
                            <td>
                                <div class="fw-bold">{{ $ord->warga->name ?? '-' }}</div>
                                <small class="text-muted">{{ $ord->warga->no_hp ?? ($ord->warga->email ?? '-') }}</small>
                            </td>
                            <td>
                                <div>{{ $ord->tgl_jemput }}</div>
                                <small class="text-muted d-block">{{ $ord->jam_jemput }}</small>
                                <small class="text-muted d-block" style="max-width:180px; font-size:0.75rem;">{{ $ord->warga->alamat ?? '' }}</small>
                            </td>
                            <td>
                                <div>{{ round($ord->jarak_km, 2) }} km</div>
                                @if($ord->biaya_jemput > 0)
                                    <small class="text-danger">Ongkir: Rp {{ number_format($ord->biaya_jemput, 0, ',', '.') }}</small>
                                @else
                                    <span class="badge bg-success">Gratis</span>
                                @endif
                            </td>
                            <td>
                                @php $totalEstWeight = $ord->items ? $ord->items->sum('estimasi_berat') : 0; @endphp
                                <span class="fw-semibold">{{ $totalEstWeight }} kg</span>
                            </td>
                            <td>
                                @if($ord->status === 'completed')
                                    <div class="text-success fw-bold">Rp {{ number_format($ord->total_final_harga, 0, ',', '.') }}</div>
                                    <span class="badge bg-{{ $ord->metode_pembayaran === 'transfer' ? 'primary' : 'success' }} text-white text-capitalize">
                                        {{ $ord->metode_pembayaran === 'transfer' ? '💳 Transfer' : '💵 Cash (Tunai)' }}
                                    </span>
                                    @if($ord->bukti_transfer)
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-sm btn-primary text-white py-0 px-2 fw-semibold shadow-sm" style="font-size: 0.75rem;" onclick="openBuktiTransferModal('{{ asset($ord->bukti_transfer) }}')">
                                                📄 Struk TF
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-primary">Rp {{ number_format($ord->total_estimasi_harga, 0, ',', '.') }}</div>
                                    <small class="text-muted">(Estimasi)</small>
                                @endif
                            </td>
                            <td>
                                @if($ord->driver)
                                    <span class="badge bg-light text-dark border p-2">🚚 {{ $ord->driver->name }}</span>
                                @else
                                    <form action="{{ route('pengepul.admin.assign-driver', $ord->id) }}" method="POST" class="d-flex gap-1" style="max-width: 180px;">
                                        @csrf
                                        <select class="form-select form-select-sm" name="driver_id" required>
                                            <option value="">Pilih Sopir...</option>
                                            @foreach($drivers as $drv)
                                                <option value="{{ $drv->id }}">{{ $drv->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-primary">Assign</button>
                                    </form>
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
                                <span class="badge bg-{{ $badge }} text-capitalize p-2">
                                    {{ $ord->status === 'completed' ? '✓ Selesai' : $ord->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                    @if($ord->status !== 'completed')
                                        <!-- Button Process Pembayaran -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success py-1 px-2" 
                                                style="font-size:0.75rem;"
                                                onclick="triggerAdminBayarModal({{ $ord->id }})">
                                            💳 Bayar / Timbang
                                        </button>
                                    @endif

                                    <!-- Button Cetak Surat Jalan -->
                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:0.75rem;" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $ord->id) }}')">
                                        📜 Surat Jalan
                                    </button>

                                    <!-- Button Chat Web -->
                                    <button type="button" class="btn btn-sm btn-outline-dark py-1 px-2" style="font-size:0.75rem;" onclick="openAdminChatModal({{ $ord->id }}, '{{ $ord->order_no }}')">
                                        💬 Chat
                                    </button>

                                    <!-- Button WhatsApp -->
                                    @php
                                        $wargaHp = preg_replace('/[^0-9]/', '', (string)($ord->warga->no_hp ?? ''));
                                        if (str_starts_with($wargaHp, '0')) {
                                            $wargaHp = '62' . substr($wargaHp, 1);
                                        }
                                        $waMsg = rawurlencode("Halo Kak " . ($ord->warga->name ?? '') . ", kami dari Admin Pengepul Digital mengenai Order #" . $ord->order_no . ".");
                                    @endphp
                                    @if(!empty($wargaHp))
                                        <a href="https://wa.me/{{ $wargaHp }}?text={{ $waMsg }}" target="_blank" class="btn btn-sm btn-success py-1 px-2" style="font-size:0.75rem;">
                                            🟢 WA
                                        </a>
                                    @endif

                                    @if(in_array($ord->status, ['pending', 'scheduled', 'processing']))
                                        <form action="{{ route('pengepul.admin.order.cancel', $ord->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan order penjemputan ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size:0.75rem;">
                                                🚫 Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada order penjemputan masuk dari warga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $orders->links() }}</div>
    </div>

    <!-- RIWAYAT PENJUALAN & DISTRIBUSI KE SUPPLIER / PABRIK -->
    <div class="page-card p-4 mt-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1 text-primary d-flex align-items-center gap-2"><span>🏭</span> Riwayat Distribusi & Penjualan ke Supplier / Pabrik</h2>
                <p class="text-muted small mb-0">Catatan pengiriman bahan baku daur ulang dari gudang ke pabrik/mitra industri.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="openDistribusiModal()">
                <i class="fa-solid fa-truck-ramp-box me-1"></i> + Buat Pengiriman Supplier
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No. Surat Jalan</th>
                        <th>Supplier / Pabrik Tujuan</th>
                        <th>Material Sampah</th>
                        <th>Jumlah (kg)</th>
                        <th>Harga Pabrik / kg</th>
                        <th>Total Pemasukan Kas</th>
                        <th>Waktu & Admin</th>
                        <th class="text-center">Surat Jalan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distribusiList as $dist)
                        <tr>
                            <td><span class="font-monospace fw-bold text-dark">{{ $dist->no_surat_jalan }}</span></td>
                            <td class="fw-bold text-primary">{{ $dist->supplier_name }}</td>
                            <td>
                                <span>{{ $dist->material->icon ?? '📦' }} {{ $dist->material->nama_material ?? 'Material' }}</span>
                            </td>
                            <td class="fw-bold text-dark">{{ number_format($dist->jumlah_kg, 1, ',', '.') }} kg</td>
                            <td class="text-muted small">Rp {{ number_format($dist->harga_jual_per_kg, 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">+ Rp {{ number_format($dist->total_pendapatan, 0, ',', '.') }}</td>
                            <td>
                                <div class="small">{{ $dist->created_at ? $dist->created_at->format('d M Y, H:i') : '-' }}</div>
                                <small class="text-muted"><i class="fa-solid fa-user-check me-1"></i> {{ $dist->admin->name ?? 'Admin' }}</small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size:0.78rem;" onclick="openSuratJalanModal('{{ route('pengepul.surat-jalan', $dist->id_surat_keluar ?: $dist->id) }}')">
                                    📜 Surat Jalan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada pengiriman material ke supplier/pabrik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                <h5 class="modal-title">📄 Bukti Transfer / Struk Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-dark bg-opacity-10">
                <img id="buktiTransferImage" src="" class="img-fluid rounded shadow" alt="Bukti Transfer" style="max-height: 480px; object-fit: contain;">
            </div>
            <div class="modal-footer">
                <a id="downloadBuktiTransferLink" href="" target="_blank" class="btn btn-primary text-white btn-sm fw-bold">⬇️ Download / Buka Penuh</a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Process Pembayaran Admin (Transfer / Cash) -->
<div class="modal fade" id="adminBayarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="adminBayarForm" action="" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">💳 Proses Pembayaran & Penimbangan - <span id="adminBayarOrderNo"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Opsi Metode Pembayaran ke Warga:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="adminPayCash" value="cash" checked onchange="toggleAdminTransferFields()">
                            <label class="btn btn-outline-success w-100 p-3 text-start" for="adminPayCash">
                                <div class="fw-bold fs-6">💵 Bayar Tunai (Cash)</div>
                                <small class="text-muted d-block">Serahkan uang tunai langsung ke warga di lokasi.</small>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="adminPayTransfer" value="transfer" onchange="toggleAdminTransferFields()">
                            <label class="btn btn-outline-primary w-100 p-3 text-start" for="adminPayTransfer">
                                <div class="fw-bold fs-6">💳 Transfer ke Saldo Customer</div>
                                <small class="text-muted d-block">Otomatis masuk langsung ke Saldo Akun Customer.</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Cash Info Box -->
                <div id="adminCashBox" class="p-3 bg-success bg-opacity-10 border border-success rounded-3 mb-3">
                    <div class="d-flex align-items-center gap-2 text-success fw-bold">
                        <span>💵</span> Pembayaran Tunai (Cash)
                    </div>
                    <small class="text-muted d-block mt-1">Uang tunai diserahkan langsung kepada warga saat penimbangan selesai.</small>
                </div>

                <!-- Transfer Info Box -->
                <div id="adminTransferBox" class="p-3 bg-primary bg-opacity-10 border border-primary rounded-3 mb-3 d-none">
                    <div class="d-flex align-items-center gap-2 text-primary fw-bold mb-1">
                        <span>💳</span> Transfer Otomatis ke Saldo Customer
                    </div>
                    <small class="text-muted d-block">Sistem akan secara otomatis mentransfer total pembayaran langsung ke **Saldo Digital Akun Customer** tanpa perlu mengunggah bukti fisik.</small>
                </div>

                <div id="adminItemsContainer" class="mb-3">
                    <!-- Dynamic inputs populated in JS -->
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Biaya Transport (Deducted / Ongkir)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="adminBiayaJemput" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Pembayaran / Referensi Transaksi (Opsional)</label>
                    <input type="text" name="catatan_pembayaran" class="form-control" placeholder="Contoh: Transfer via M-Banking BCA atau Bayar Cash Tunai">
                </div>

                <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mt-2 border">
                    <div class="fw-bold text-dark">Total Pembayaran Beres ke Warga:</div>
                    <div class="fs-4 fw-bold text-success" id="adminTotalFinalDisplay">Rp 0</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold py-2 px-4">✓ Konfirmasi Selesai & Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Admin Web Chat -->
<div class="modal fade" id="adminChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="adminChatTitle">💬 Chat Web Warga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <small class="text-muted fw-semibold d-block mb-1">Pesan Otomatis Admin (Klik untuk Mengirim):</small>
                    <div class="d-flex flex-wrap gap-1">
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendAdminPresetChat('Halo Kak, pesanan penjemputan sampah Anda telah disetujui.')">
                            ⚡ Disetujui
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendAdminPresetChat('Petugas kami telah ditugaskan dan segera meluncur.')">
                            ⚡ Petugas meluncur
                        </button>
                        <button class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size:0.75rem;" onclick="sendAdminPresetChat('Pembayaran sebesar yang disepakati sudah kami transfer.')">
                            ⚡ Pembayaran ditransfer
                        </button>
                    </div>
                </div>

                <div id="adminChatMessagesBox" class="chat-box mb-3 d-flex flex-column"></div>

                <form id="adminChatForm" onsubmit="submitAdminChatMessage(event)">
                    <input type="hidden" id="adminChatOrderId">
                    <div class="input-group">
                        <input type="text" id="adminChatInputMessage" class="form-control" placeholder="Tulis pesan..." required>
                        <button class="btn btn-primary" type="submit">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Top Up Saldo Kas Admin -->
<div class="modal fade" id="topUpKasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pengepul.admin.topup') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">💵 Top Up Saldo Kas Pengepul</h5>
                <button type="button" class="btn-close btn-close-white close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3 mb-3">
                    <div class="small text-muted fw-semibold">Saldo Kas saat ini:</div>
                    <div class="fs-4 fw-bold text-success">Rp {{ number_format($saldoKas, 0, ',', '.') }}</div>
                    <small class="text-muted d-block mt-1">Top up ini menambah kas pemasukan pengepul untuk modal pembayaran sampah ke warga.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Nominal Top Up (Rp)</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text fw-bold">Rp</span>
                        <input type="number" name="jumlah_uang" id="inputTopUpNominal" class="form-control form-control-lg fw-bold text-primary" placeholder="0" required min="1000" step="1000">
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="setTopUpNominal(100000)">+ 100 rb</button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="setTopUpNominal(500000)">+ 500 rb</button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="setTopUpNominal(1000000)">+ 1 Jt</button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="setTopUpNominal(5000000)">+ 5 Jt</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Keterangan / Catatan Transaksi (Opsional)</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Suntik Modal Operasional Kas, Transfer Bank Admin, dll.">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold px-4">✓ Tambah Saldo Kas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Buku Riwayat Transaksi Kas Pengepul -->
<div class="modal fade" id="riwayatKasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">📜 Buku Riwayat Transaksi Kas Pengepul</h5>
                <button type="button" class="btn-close btn-close-white close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Tanggal & Waktu</th>
                                <th>Tipe Transaksi</th>
                                <th>Jumlah Uang</th>
                                <th>Keterangan</th>
                                <th>Ref Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kas as $index => $k)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div>{{ $k->created_at ? $k->created_at->format('d M Y') : '-' }}</div>
                                        <small class="text-muted">{{ $k->created_at ? $k->created_at->format('H:i') : '' }}</small>
                                    </td>
                                    <td>
                                        @if($k->tipe_transaksi === 'pemasukan')
                                            <span class="badge bg-success text-white">⬆️ Pemasukan / Top Up</span>
                                        @else
                                            <span class="badge bg-danger text-white">⬇️ Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $k->tipe_transaksi === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                        {{ $k->tipe_transaksi === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($k->jumlah_uang, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="small text-dark">{{ $k->keterangan ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if($k->order)
                                            <span class="badge bg-secondary text-white">#{{ $k->order->order_no }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada catatan riwayat kas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Global Data Storage -->
<script>
    window.adminOrdersData = {};
    @foreach($orders as $ord)
        window.adminOrdersData[{{ $ord->id }}] = {
            id: {{ $ord->id }},
            order_no: "{{ $ord->order_no }}",
            biaya_jemput: {{ $ord->biaya_jemput ?: 0 }},
            total_estimasi_harga: {{ $ord->total_estimasi_harga ?: 0 }},
            warga: @json($ord->warga),
            items: @json($ord->items)
        };
    @endforeach
</script>

<!-- Leaflet Peta Script untuk Sebaran Order -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const warehouseLat = -6.2088;
        const warehouseLng = 106.8456;

        const map = L.map('adminMap').setView([warehouseLat, warehouseLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        L.marker([warehouseLat, warehouseLng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#12355b; color:white; padding:4px 8px; border-radius:5px; border:2px solid white; font-weight:bold; font-size:10px; white-space:nowrap;'>🏬 Gudang Pengepul (Pusat)</div>",
                iconSize: [140, 24],
                iconAnchor: [70, 12]
            })
        }).addTo(map);

        @foreach($orders as $ord)
            @if($ord->latitude && $ord->longitude)
                (function() {
                    const lat = {{ $ord->latitude }};
                    const lng = {{ $ord->longitude }};
                    const title = "{{ $ord->warga->name ?? 'Warga' }}";
                    const orderNo = "{{ $ord->order_no }}";
                    const status = "{{ $ord->status }}";
                    const detail = "Jadwal: {{ $ord->tgl_jemput }} ({{ $ord->jam_jemput }})";

                    let markerColor = 'gray';
                    if (status === 'pending') markerColor = 'red';
                    if (status === 'scheduled') markerColor = 'blue';
                    if (status === 'processing') markerColor = 'orange';
                    if (status === 'completed') markerColor = 'green';

                    const circleMarker = L.circleMarker([lat, lng], {
                        radius: 8,
                        fillColor: markerColor,
                        color: "#fff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);

                    circleMarker.bindPopup("<strong>Order: " + orderNo + "</strong><br>Warga: " + title + "<br>" + detail + "<br>Status: <span class='text-capitalize fw-bold'>" + status + "</span>");
                })();
            @endif
        @endforeach
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

    function triggerAdminBayarModal(orderId) {
        var data = window.adminOrdersData[orderId];
        if (!data) return;
        openAdminBayarModal(data.id, data.order_no, data.items || [], data.biaya_jemput || 0, data.warga || {}, data.total_estimasi_harga || 0);
    }

    function toggleAdminTransferFields() {
        var isTransfer = document.getElementById('adminPayTransfer').checked;
        var cashBox = document.getElementById('adminCashBox');
        var transferBox = document.getElementById('adminTransferBox');

        if (isTransfer) {
            cashBox.classList.add('d-none');
            transferBox.classList.remove('d-none');
        } else {
            cashBox.classList.remove('d-none');
            transferBox.classList.add('d-none');
        }
    }

    function triggerAdminBayarModal(orderId) {
        var ord = window.adminOrdersData ? window.adminOrdersData[orderId] : null;
        if (!ord) return;
        openAdminBayarModal(ord.id, ord.order_no, ord.items, ord.biaya_jemput, ord.warga, ord.total_estimasi_harga);
    }

    function openAdminBayarModal(orderId, orderNo, items, biayaJemput, warga, totalEst) {
        const actionUrl = "{{ url('/pengepul/admin/pembayaran/proses') }}/" + orderId;
        document.getElementById('adminBayarForm').action = actionUrl;
        document.getElementById('adminBayarOrderNo').textContent = orderNo;
        document.getElementById('adminBiayaJemput').value = biayaJemput.toLocaleString('id-ID');

        var elBankNama = document.getElementById('adminWargaBankNama');
        if (elBankNama) elBankNama.innerText = warga && warga.bank_nama ? warga.bank_nama : 'Belum Diisi';
        var elBankNomor = document.getElementById('adminWargaBankNomor');
        if (elBankNomor) elBankNomor.innerText = warga && warga.bank_nomor ? warga.bank_nomor : '-';
        var elEwalletNama = document.getElementById('adminWargaEwalletNama');
        if (elEwalletNama) elEwalletNama.innerText = warga && warga.ewallet_nama ? warga.ewallet_nama : 'Belum Diisi';
        var elEwalletNomor = document.getElementById('adminWargaEwalletNomor');
        if (elEwalletNomor) elEwalletNomor.innerText = warga && warga.ewallet_nomor ? warga.ewallet_nomor : '-';

        var payCashEl = document.getElementById('adminPayCash');
        if (payCashEl) payCashEl.checked = true;
        toggleAdminTransferFields();

        const container = document.getElementById('adminItemsContainer');
        if (container) {
            container.innerHTML = '';
            items.forEach(function(item) {
                const materialName = item.material ? item.material.nama_material : 'Material Sampah';
                const html = `
                    <div class="row align-items-center mb-3 p-2 border rounded bg-white admin-item-timbang-row" data-price="${item.harga_beli_per_kg}">
                        <div class="col-md-5">
                            <strong>${materialName}</strong><br>
                            <span class="text-muted small">Harga Beli: Rp ${item.harga_beli_per_kg.toLocaleString('id-ID')} / kg</span>
                            <div class="text-primary small">Estimasi Berat: ${item.estimasi_berat} kg</div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Berat Final Hasil Timbangan (kg)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="weights[${item.id}]" class="form-control admin-timbang-weight-input" value="${item.final_berat || item.estimasi_berat}" required min="0" oninput="recalculateAdminTotalPayout()">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += html;
            });
        }

        recalculateAdminTotalPayout();

        var elModal = document.getElementById('adminBayarModal');
        if (!elModal) return;
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#adminBayarModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(elModal) || new bootstrap.Modal(elModal);
            modal.show();
        } else {
            elModal.classList.add('show');
            elModal.style.display = 'block';
        }
    }

    function recalculateAdminTotalPayout() {
        const rows = document.querySelectorAll('.admin-item-timbang-row');
        let total = 0;

        rows.forEach(function(row) {
            const price = parseFloat(row.getAttribute('data-price'));
            const input = row.querySelector('.admin-timbang-weight-input');
            const weight = parseFloat(input.value) || 0;
            total += weight * price;
        });

        const biayaJemput = parseFloat(document.getElementById('adminBiayaJemput').value.replace(/\./g, '')) || 0;
        let finalTotal = total - biayaJemput;
        if (finalTotal < 0) finalTotal = 0;

        document.getElementById('adminTotalFinalDisplay').textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
    }

    function copyTextToClipboard(text) {
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

    // Admin Chat Functionality
    let currentAdminChatOrderId = null;

    function openAdminChatModal(orderId, orderNo) {
        currentAdminChatOrderId = orderId;
        document.getElementById('adminChatOrderId').value = orderId;
        document.getElementById('adminChatTitle').innerText = '💬 Chat Order #' + orderNo;

        var modal = new bootstrap.Modal(document.getElementById('adminChatModal'));
        modal.show();

        loadAdminChatMessages(orderId);
    }

    function loadAdminChatMessages(orderId) {
        fetch(`/pengepul/chat/fetch/${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    var box = document.getElementById('adminChatMessagesBox');
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
                        box.scrollTop = box.scrollHeight;
                    }
                }
            });
    }

    function sendAdminPresetChat(msg) {
        document.getElementById('adminChatInputMessage').value = msg;
        submitAdminChatMessage(new Event('submit'));
    }

    function submitAdminChatMessage(e) {
        e.preventDefault();
        var msgInput = document.getElementById('adminChatInputMessage');
        var msg = msgInput.value.trim();
        var orderId = currentAdminChatOrderId;

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
                loadAdminChatMessages(orderId);
            }
        });
    }

    function setTopUpNominal(amount) {
        var input = document.getElementById('inputTopUpNominal');
        if (input) {
            input.value = amount;
        }
    }

    function openTopUpKasModal() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#topUpKasModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('topUpKasModal')) || new bootstrap.Modal(document.getElementById('topUpKasModal'));
            modal.show();
        } else {
            var el = document.getElementById('topUpKasModal');
            if (el) {
                el.classList.add('show');
                el.style.display = 'block';
            }
        }
    }

    function openRiwayatKasModal() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#riwayatKasModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('riwayatKasModal')) || new bootstrap.Modal(document.getElementById('riwayatKasModal'));
            modal.show();
        } else {
            var el = document.getElementById('riwayatKasModal');
            if (el) {
                el.classList.add('show');
                el.style.display = 'block';
            }
        }
    }

    function openDistribusiModal(materialId = null) {
        var el = document.getElementById('distribusiSupplierModal');
        if (!el) return;
        if (materialId) {
            var select = el.querySelector('select[name="material_id"]');
            if (select) select.value = materialId;
        }
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            $('#distribusiSupplierModal').modal('show');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.show();
        } else {
            el.classList.add('show');
            el.style.display = 'block';
        }
    }
</script>

<!-- Modal Distribusi ke Supplier / Pabrik Daur Ulang -->
<div class="modal fade" id="distribusiSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('pengepul.admin.distribusi.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold fs-6"><i class="fa-solid fa-truck-ramp-box me-2"></i> Distribusi & Penjualan Material ke Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Supplier / Pabrik Tujuan</label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="Contoh: PT Plastik Daur Ulang Jaya, Bandar Kertas Utama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Pilih Material Sampah Gudang</label>
                    <select class="form-select" name="material_id" required>
                        <option value="">-- Pilih Material --</option>
                        @foreach($katalog as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->icon }} {{ $kat->nama_material }} (Harga Beli: Rp {{ number_format($kat->harga_beli_per_kg, 0, ',', '.') }}/kg)</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Jumlah Berat (kg)</label>
                        <input type="number" step="0.1" min="0.1" name="jumlah_kg" class="form-control" placeholder="Contoh: 250" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Harga Jual Pabrik / kg (Rp)</label>
                        <input type="number" min="1" name="harga_jual_per_kg" class="form-control" placeholder="Contoh: 4500" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Catatan / Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Nomor truk, armada pengirim, dll.">
                </div>
                <div class="alert alert-info py-2 px-3 small rounded-3 mb-0">
                    <i class="fa-solid fa-circle-info me-1"></i> Pengiriman ini otomatis memotong stok gudang, menambah kas pemasukan pengepul, dan menerbitkan <strong>Surat Jalan Supplier</strong>.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-paper-plane me-1"></i> Proses Pengiriman & Terbitkan SJ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
