<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Supplier {{ $distribusi->no_surat_jalan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            border-bottom: 3px double #2563eb;
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
            height: 75px;
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
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
        🖨️ Cetak / Simpan PDF Surat Jalan
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-lg rounded-pill px-4 ms-2">
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
            <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">SURAT JALAN SUPPLIER</span>
        </div>
    </div>

    <!-- NOMOR & TANGGAL SURAT -->
    <div class="row mb-4">
        <div class="col-6">
            <table class="table table-borderless table-sm mb-0">
                <tr>
                    <td class="text-muted" style="width: 140px;">No. Surat Jalan</td>
                    <td>: <strong>{{ $distribusi->no_surat_jalan }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Tanggal Kirim</td>
                    <td>: <strong>{{ $distribusi->created_at ? $distribusi->created_at->format('d F Y') : date('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Jam Pengiriman</td>
                    <td>: {{ $distribusi->created_at ? $distribusi->created_at->format('H:i') : date('H:i') }} WIB</td>
                </tr>
                <tr>
                    <td class="text-muted">Status Dokumen</td>
                    <td>: <span class="badge bg-success text-capitalize">Diterima / Terkirim</span></td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <div class="p-3 border rounded bg-light">
                <div class="fw-bold text-primary mb-1">🏭 SUPPLIER / PABRIK TUJUAN:</div>
                <div class="fw-bold text-dark fs-6">{{ $distribusi->supplier_name }}</div>
                <div class="small text-muted mt-1">Armada / Catatan: {{ $distribusi->keterangan ?: 'Truk Pengangkut Pengepul' }}</div>
            </div>
        </div>
    </div>

    <!-- RINCIAN BARANG / MATERIAL SAMPAH -->
    <h5 class="fw-bold text-dark mb-2">📦 Rincian Bahan Baku Daur Ulang yang Didistribusikan:</h5>
    <table class="table table-bordered table-surat align-middle mb-4">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Jenis Material Sampah</th>
                <th class="text-end">Jumlah Berat (kg)</th>
                <th class="text-end">Harga Pabrik / kg</th>
                <th class="text-end">Total Nilai Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong>{{ $distribusi->material->icon ?? '📦' }} {{ $distribusi->material->nama_material ?? 'Material' }}</strong>
                </td>
                <td class="text-end fw-bold">{{ number_format($distribusi->jumlah_kg, 1, ',', '.') }} kg</td>
                <td class="text-end">Rp {{ number_format($distribusi->harga_jual_per_kg, 0, ',', '.') }}</td>
                <td class="text-end fw-bold text-primary">Rp {{ number_format($distribusi->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="table-primary fw-bold">
                <td colspan="2" class="text-end">TOTAL DIKIRIM & DITERIMA:</td>
                <td class="text-end">{{ number_format($distribusi->jumlah_kg, 1, ',', '.') }} kg</td>
                <td></td>
                <td class="text-end fs-6">Rp {{ number_format($distribusi->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="alert alert-secondary border py-2 px-3 small mb-4">
        <strong>Catatan Pengiriman:</strong> Barang yang tercantum di atas telah ditimbang secara sah dan diserahkan dalam kondisi baik ke pihak supplier/pabrik daur ulang.
    </div>

    <!-- TANDA TANGAN -->
    <div class="row signature-box text-center">
        <div class="col-4">
            <div class="small text-muted">Disiapkan Oleh (Admin),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark border-top pt-1 text-decoration-underline">{{ $distribusi->admin->name ?? 'Admin Pengepul' }}</div>
            <small class="text-muted">Admin Operasional</small>
        </div>
        <div class="col-4">
            <div class="small text-muted">Pengangkut (Sopir/Driver),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark border-top pt-1 text-decoration-underline">___________________</div>
            <small class="text-muted">Kurir Pengangkut</small>
        </div>
        <div class="col-4">
            <div class="small text-muted">Diterima Oleh (Pabrik/Supplier),</div>
            <div class="signature-space"></div>
            <div class="fw-bold text-dark border-top pt-1 text-decoration-underline">{{ $distribusi->supplier_name }}</div>
            <small class="text-muted">Petugas Penerima Pabrik</small>
        </div>
    </div>
</div>

</body>
</html>
