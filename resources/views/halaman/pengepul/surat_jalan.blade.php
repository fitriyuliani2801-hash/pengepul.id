<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Penjemputan #{{ $order->order_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .surat-container {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .kop-surat {
            border-bottom: 3px double #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-logo {
            font-size: 2.5rem;
        }
        .table-surat th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
        }
        .signature-box {
            margin-top: 50px;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            body {
                background: none;
            }
            .surat-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm">
        🖨️ Cetak / Simpan PDF Surat Jalan
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-lg ms-2">
        Tutup
    </button>
</div>

<div class="surat-container">
    <!-- KOP SURAT RESMI -->
    <div class="kop-surat d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="kop-logo">♻️</div>
            <div>
                <h3 class="fw-bold mb-0 text-primary" style="letter-spacing: 1px;">PENGEPUL DIGITAL</h3>
                <div class="fw-semibold text-secondary">Bank Sampah & Layanan Daur Ulang Mandiri</div>
                <small class="text-muted">Jl. Kebon Jeruk No. 45, Jakarta Pusat | Telp: (021) 555-0199 | WA: 0812-3456-7890</small>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-primary fs-6 px-3 py-2">SURAT JALAN</span>
        </div>
    </div>

    <!-- NOMOR & TANGGAL SURAT -->
    <div class="row mb-4">
        <div class="col-6">
            <table class="table table-borderless table-sm mb-0">
                <tr>
                    <td class="text-muted" style="width: 130px;">No. Surat Jalan</td>
                    <td>: <strong>SJ/{{ date('Y/m', strtotime($order->created_at)) }}/ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">No. Order Warga</td>
                    <td>: <strong>#{{ $order->order_no }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Tgl Penjemputan</td>
                    <td>: {{ date('d F Y', strtotime($order->tgl_jemput)) }} ({{ $order->jam_jemput }})</td>
                </tr>
                <tr>
                    <td class="text-muted">Status Transaksi</td>
                    <td>: <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'info' }} text-capitalize">{{ $order->status }}</span></td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <div class="p-3 border rounded bg-light">
                <div class="fw-bold text-primary mb-1">📍 ALAMAT LOKASI PENJEMPUTAN:</div>
                <div class="fw-semibold text-dark">{{ $order->warga->name ?? 'Warga / Nasabah' }}</div>
                <div class="small text-muted">{{ $order->warga->no_hp ?? '-' }}</div>
                <div class="small text-dark mt-1">{{ $order->warga->alamat ?? 'Alamat belum diisi' }}</div>
            </div>
        </div>
    </div>

    <!-- DETAIL PETUGAS & DRIVER -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-3 border rounded bg-white">
                <div class="row">
                    <div class="col-md-6">
                        <span class="text-muted small">Petugas / Kurir Pengangkut:</span>
                        <div class="fw-bold text-dark fs-6">🚚 {{ $order->driver->name ?? 'Belum Ditugaskan' }}</div>
                        <small class="text-muted">No. HP: {{ $order->driver->no_hp ?? '-' }}</small>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small">Jarak dari Gudang Pusat:</span>
                        <div class="fw-bold text-dark fs-6">{{ round($order->jarak_km, 2) }} km</div>
                        <small class="text-muted">Metode Pembayaran: <strong>{{ strtoupper($order->metode_pembayaran ?: 'TUNAI / CASH') }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RINCIAN BARANG / MATERIAL SAMPAH -->
    <h5 class="fw-bold text-dark mb-2">📦 Rincian Sampah Daur Ulang yang Diangkut:</h5>
    <table class="table table-bordered table-surat align-middle mb-4">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Jenis Material Sampah</th>
                <th class="text-end">Harga / kg</th>
                <th class="text-end">Est. Berat</th>
                <th class="text-end">Berat Real (Final)</th>
                <th class="text-end">Subtotal Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $index => $item)
                @php
                    $finalWeight = $item->final_berat ?: $item->estimasi_berat;
                    $subtotal = $finalWeight * $item->harga_beli_per_kg;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="fs-5 me-1">{{ $item->material->icon ?? '📦' }}</span>
                        <strong>{{ $item->material->nama_material ?? 'Material' }}</strong>
                    </td>
                    <td class="text-end">Rp {{ number_format($item->harga_beli_per_kg, 0, ',', '.') }}</td>
                    <td class="text-end">{{ $item->estimasi_berat }} kg</td>
                    <td class="text-end fw-bold">{{ $finalWeight }} kg</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada item material.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-light">
                <td colspan="4" class="fw-bold text-end">Total Nilai Pembelian Sampah:</td>
                <td class="text-end fw-bold">{{ $order->items->sum(fn($i) => $i->final_berat ?: $i->estimasi_berat) }} kg</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($order->items->sum(fn($i) => ($i->final_berat ?: $i->estimasi_berat) * $i->harga_beli_per_kg), 0, ',', '.') }}</td>
            </tr>
            @if($order->biaya_jemput > 0)
                <tr class="table-light text-danger">
                    <td colspan="5" class="fw-bold text-end">Biaya Transport / Ongkir (Deducted):</td>
                    <td class="text-end fw-bold">- Rp {{ number_format($order->biaya_jemput, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="table-primary fs-6">
                <td colspan="5" class="fw-bold text-end">TOTAL BERSIH DITERIMA WARGA:</td>
                <td class="text-end fw-bold text-primary">Rp {{ number_format($order->total_final_harga ?: $order->total_estimasi_harga, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- CATATAN PEMBAYARAN -->
    @if($order->catatan_pembayaran)
        <div class="p-3 bg-light rounded border mb-4">
            <small class="text-muted fw-bold d-block">Catatan Pembayaran / Transaksi:</small>
            <span class="small text-dark">{{ $order->catatan_pembayaran }}</span>
        </div>
    @endif

    <!-- SIGNATURE BOX -->
    <div class="row text-center signature-box">
        <div class="col-4">
            <div class="small text-muted">Diserahkan Oleh (Warga/Nasabah),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark">( {{ $order->warga->name ?? 'Warga' }} )</div>
        </div>
        <div class="col-4">
            <div class="small text-muted">Diangkut Oleh (Driver/Kurir),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark">( {{ $order->driver->name ?? 'Petugas Driver' }} )</div>
        </div>
        <div class="col-4">
            <div class="small text-muted">Diterima Gudang (Admin Kasir),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark">( Admin Pengepul )</div>
        </div>
    </div>
</div>

</body>
</html>
