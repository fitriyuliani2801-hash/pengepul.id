<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SampahKatalog;
use App\Models\PenjemputanOrder;
use App\Models\User;
use App\Models\KasPengepul;
use App\Models\StokGudang;
use App\Models\SuratKeluarModel;
use App\Models\Notifikasi;
use App\Models\PenjemputanItem;

class PengepulAdminController extends Controller
{
    public function index()
    {
        $orders = PenjemputanOrder::with(['warga', 'driver', 'items.material'])
            ->orderByDesc('id')
            ->paginate(10);

        $drivers = User::where('role', 'driver')->where('status', 'active')->get();
        $katalog = SampahKatalog::all();

        // Ambil data kas
        $kas = KasPengepul::with('order')->orderByDesc('id')->get();
        $totalPengeluaran = KasPengepul::where('tipe_transaksi', 'pengeluaran')->sum('jumlah_uang');
        $totalPemasukan = KasPengepul::where('tipe_transaksi', 'pemasukan')->sum('jumlah_uang');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // Ambil stok gudang ter-agregasi
        $stok = DB::table('stok_gudang')
            ->join('sampah_katalog', 'stok_gudang.material_id', '=', 'sampah_katalog.id')
            ->select('sampah_katalog.id as material_id', 'sampah_katalog.nama_material', 'sampah_katalog.icon',
                DB::raw("SUM(CASE WHEN tipe_stok = 'masuk' THEN jumlah_kg ELSE -jumlah_kg END) as total_berat"))
            ->groupBy('sampah_katalog.id', 'sampah_katalog.nama_material', 'sampah_katalog.icon')
            ->get();

        $distribusiList = \App\Models\DistribusiSupplier::with(['material', 'admin', 'suratKeluar'])
            ->orderByDesc('id')
            ->get();

        return view('halaman/pengepul/admin_dashboard', [
            'orders' => $orders,
            'drivers' => $drivers,
            'katalog' => $katalog,
            'kas' => $kas,
            'saldoKas' => $saldoKas,
            'stok' => $stok,
            'distribusiList' => $distribusiList
        ]);
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'harga_beli_per_kg' => 'required|integer|min:0'
        ]);

        $material = SampahKatalog::findOrFail($id);
        $material->update([
            'harga_beli_per_kg' => $request->harga_beli_per_kg
        ]);

        return redirect()->back()->with('success', 'Harga material ' . $material->nama_material . ' berhasil diperbarui.');
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        $order = PenjemputanOrder::with(['warga', 'driver'])->findOrFail($id);
        $driver = User::find($request->driver_id);

        DB::beginTransaction();
        try {
            $order->update([
                'driver_id' => $request->driver_id,
                'status' => 'scheduled'
            ]);

            // Update atau Buat SPK / Surat Keluar otomatis
            if ($order->id_surat_keluar) {
                $spk = SuratKeluarModel::find($order->id_surat_keluar);
                if ($spk) {
                    $spk->update([
                        'tujuan_surat' => $driver->name,
                        'status_proses' => 'sedang diproses',
                        'keterangan' => 'Surat Jalan / Tugas Penjemputan Sampah untuk Driver: ' . $driver->name
                    ]);
                }
            } else {
                $spk = SuratKeluarModel::create([
                    'no_agenda' => 'SPK-' . date('ymd') . rand(10, 99),
                    'no_surat' => 'SJ/JEMPUT/' . $order->order_no,
                    'tujuan_surat' => $driver->name,
                    'isi_ringkas' => 'Surat Jalan Penjemputan Sampah Daur Ulang Warga ' . ($order->warga->name ?? 'Warga') . ' oleh Driver ' . $driver->name,
                    'tgl_surat' => date('Y-m-d'),
                    'keterangan' => 'Surat Jalan Otomatis Penjemputan Sampah',
                    'user_id' => $order->user_id,
                    'status_proses' => 'sedang diproses',
                    'diproses_oleh' => auth()->id()
                ]);
                $order->update(['id_surat_keluar' => $spk->id_surat_keluar]);
            }

            // Kirim notifikasi ke Driver dengan link Surat Jalan
            Notifikasi::create([
                'user_id' => $driver->id,
                'judul' => 'Tugas Penjemputan Baru & Surat Jalan',
                'pesan' => 'Anda ditugaskan menjemput sampah warga untuk Order No. ' . $order->order_no . '. Klik untuk membuka Surat Jalan.',
                'url' => route('pengepul.surat-jalan', $order->id)
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Petugas ' . $driver->name . ' berhasil ditugaskan. Surat Jalan otomatis dibuat!')
                ->with('open_surat_jalan', route('pengepul.surat-jalan', $order->id));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal menugaskan petugas: ' . $e->getMessage()]);
        }
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer',
            'bukti_transfer' => 'nullable|image|max:2048',
            'catatan_pembayaran' => 'nullable|string|max:255'
        ]);

        $order = PenjemputanOrder::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            $totalHargaFinal = 0;

            foreach ($request->weights as $itemId => $finalWeight) {
                $item = PenjemputanItem::findOrFail($itemId);
                $subtotal = $finalWeight * $item->harga_beli_per_kg;

                $item->update([
                    'final_berat' => $finalWeight,
                    'subtotal_final' => $subtotal
                ]);

                $totalHargaFinal += $subtotal;

                StokGudang::create([
                    'order_id' => $order->id,
                    'material_id' => $item->material_id,
                    'tipe_stok' => 'masuk',
                    'jumlah_kg' => $finalWeight,
                    'keterangan' => 'Masuk stok dari penjemputan Order ' . $order->order_no
                ]);
            }

            $payout = $totalHargaFinal - $order->biaya_jemput;
            if ($payout < 0) {
                $payout = 0;
            }

            $buktiPath = $order->bukti_transfer;
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $filename = time() . '_tf_' . $order->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/bukti_transfer'), $filename);
                $buktiPath = 'uploads/bukti_transfer/' . $filename;
            }

            $order->update([
                'total_final_harga' => $payout,
                'status' => 'completed',
                'metode_pembayaran' => $request->payment_method,
                'status_pembayaran' => 'lunas',
                'bukti_transfer' => $buktiPath,
                'catatan_pembayaran' => $request->catatan_pembayaran
            ]);

            if ($order->id_surat_keluar) {
                $spk = SuratKeluarModel::find($order->id_surat_keluar);
                if ($spk) {
                    $spk->update([
                        'status_proses' => 'diterima'
                    ]);
                }
            }

            if ($payout > 0) {
                KasPengepul::create([
                    'order_id' => $order->id,
                    'tipe_transaksi' => 'pengeluaran',
                    'jumlah_uang' => $payout,
                    'keterangan' => 'Pembayaran sampah ' . ($request->payment_method === 'cash' ? 'Tunai (Cash)' : 'Transfer Digital Otomatis') . ' oleh Admin ke Warga via Order ' . $order->order_no
                ]);

                // Otomatis Tambah Saldo Digital Warga
                $warga = User::find($order->user_id);
                if ($warga) {
                    $warga->increment('saldo', $payout);

                    Notifikasi::create([
                        'user_id' => $warga->id,
                        'judul' => 'Transfer Otomatis Berhasil',
                        'pesan' => 'Transfer otomatis sebesar Rp ' . number_format($payout, 0, ',', '.') . ' telah masuk ke Saldo Akun Anda dari Order ' . $order->order_no,
                        'url' => route('pengepul.warga.index')
                    ]);
                }
            }

            if ($order->biaya_jemput > 0) {
                KasPengepul::create([
                    'order_id' => $order->id,
                    'tipe_transaksi' => 'pemasukan',
                    'jumlah_uang' => $order->biaya_jemput,
                    'keterangan' => 'Pemasukan Ongkos Kirim/Jemput untuk Order ' . $order->order_no
                ]);
            }

            Notifikasi::create([
                'user_id' => $order->user_id,
                'judul' => 'Pembayaran Sampah Dikonfirmasi',
                'pesan' => 'Pembayaran sampah Anda untuk Order ' . $order->order_no . ' sebesar Rp ' . number_format($payout, 0, ',', '.') . ' telah diproses via ' . ($request->payment_method === 'cash' ? 'Tunai (Cash)' : 'Transfer Bank / E-Wallet') . '.',
                'url' => route('pengepul.warga.index')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran order ' . $order->order_no . ' (' . strtoupper($request->payment_method) . ') berhasil diproses dan kas diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }
    }

    public function cetakSuratJalan($id)
    {
        // 1. Cek PenjemputanOrder
        $order = PenjemputanOrder::with(['warga', 'driver', 'items.material'])->find($id);
        if ($order) {
            return view('halaman/pengepul/surat_jalan', [
                'order' => $order
            ]);
        }

        // 2. Cek DistribusiSupplier ID atau id_surat_keluar
        $distribusi = \App\Models\DistribusiSupplier::with(['material', 'admin', 'suratKeluar'])
            ->where('id', $id)
            ->orWhere('id_surat_keluar', $id)
            ->first();

        if ($distribusi) {
            return view('halaman/pengepul/surat_jalan_supplier', [
                'distribusi' => $distribusi
            ]);
        }

        // 3. Cek SuratKeluarModel
        $suratKeluar = SuratKeluarModel::find($id);
        if ($suratKeluar) {
            $distBySk = \App\Models\DistribusiSupplier::with(['material', 'admin', 'suratKeluar'])
                ->where('no_surat_jalan', $suratKeluar->no_surat)
                ->first();
            if ($distBySk) {
                return view('halaman/pengepul/surat_jalan_supplier', [
                    'distribusi' => $distBySk
                ]);
            }

            $orderBySk = PenjemputanOrder::with(['warga', 'driver', 'items.material'])
                ->where('id_surat_keluar', $suratKeluar->id_surat_keluar)
                ->first();
            if ($orderBySk) {
                return view('halaman/pengepul/surat_jalan', [
                    'order' => $orderBySk
                ]);
            }
        }

        abort(404, 'Dokumen Surat Jalan Tidak Ditemukan');
    }

    public function topUpSaldo(Request $request)
    {
        $request->validate([
            'jumlah_uang' => 'required|numeric|min:1000',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $keterangan = $request->keterangan ? trim($request->keterangan) : 'Top Up Modal Kas Admin';

        KasPengepul::create([
            'order_id'       => null,
            'tipe_transaksi' => 'pemasukan',
            'jumlah_uang'    => $request->jumlah_uang,
            'keterangan'     => $keterangan,
        ]);

        return redirect()->back()->with('success', 'Top up saldo kas sebesar Rp ' . number_format($request->jumlah_uang, 0, ',', '.') . ' berhasil ditambahkan!');
    }

    public function cancelOrder($id)
    {
        $order = PenjemputanOrder::findOrFail($id);

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->back()->withErrors(['msg' => 'Order ini sudah ' . $order->status . ' dan tidak dapat dibatalkan.']);
        }

        $order->update(['status' => 'cancelled']);

        if ($order->id_surat_keluar) {
            SuratKeluarModel::where('id_surat_keluar', $order->id_surat_keluar)->update([
                'status_proses' => 'ditolak',
                'keterangan' => 'Order penjemputan dibatalkan oleh Admin/Staff.'
            ]);
        }

        // Notifikasi ke Warga
        Notifikasi::create([
            'user_id' => $order->user_id,
            'judul' => 'Order Dibatalkan Admin',
            'pesan' => 'Order penjemputan No. ' . $order->order_no . ' telah dibatalkan oleh Admin.',
            'url' => route('pengepul.warga.index')
        ]);

        // Notifikasi ke Driver (jika ada)
        if ($order->driver_id) {
            Notifikasi::create([
                'user_id' => $order->driver_id,
                'judul' => 'Order Dibatalkan Admin',
                'pesan' => 'Order No. ' . $order->order_no . ' telah dibatalkan oleh Admin.',
                'url' => route('pengepul.driver.index')
            ]);
        }

        return redirect()->back()->with('success', 'Order penjemputan sampah berhasil dibatalkan.');
    }

    public function prosesDistribusiSupplier(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'material_id'   => 'required|exists:sampah_katalog,id',
            'jumlah_kg'     => 'required|numeric|min:0.1',
            'harga_jual_per_kg' => 'required|numeric|min:1',
            'keterangan'    => 'nullable|string|max:255'
        ]);

        $material = SampahKatalog::findOrFail($request->material_id);
        $totalPendapatan = $request->jumlah_kg * $request->harga_jual_per_kg;
        $noSuratJalan = 'SJ/SUPPLIER/' . date('YmdHis') . '/' . rand(100, 999);

        DB::beginTransaction();
        try {
            $spk = SuratKeluarModel::create([
                'no_agenda'     => 'DIST-' . rand(1000, 9999),
                'no_surat'      => $noSuratJalan,
                'tujuan_surat'  => $request->supplier_name,
                'tgl_surat'     => date('Y-m-d'),
                'user_id'       => Auth::id(),
                'isi_ringkas'   => 'Penjualan & Distribusi Material ' . $material->nama_material . ' (' . $request->jumlah_kg . ' kg) ke ' . $request->supplier_name,
                'status_proses' => 'diterima',
                'keterangan'    => 'Surat Jalan Pengiriman Material Sampah ke Pabrik/Supplier'
            ]);

            \App\Models\DistribusiSupplier::create([
                'no_surat_jalan'    => $noSuratJalan,
                'supplier_name'     => $request->supplier_name,
                'material_id'       => $request->material_id,
                'jumlah_kg'         => $request->jumlah_kg,
                'harga_jual_per_kg' => $request->harga_jual_per_kg,
                'total_pendapatan'  => $totalPendapatan,
                'id_surat_keluar'   => $spk->id_surat_keluar,
                'keterangan'        => $request->keterangan,
                'diproses_oleh'     => Auth::id()
            ]);

            StokGudang::create([
                'order_id'    => null,
                'material_id'  => $request->material_id,
                'tipe_stok'   => 'keluar',
                'jumlah_kg'   => $request->jumlah_kg,
                'keterangan'  => 'Pengiriman material ke Supplier ' . $request->supplier_name . ' (Ref: ' . $noSuratJalan . ')'
            ]);

            KasPengepul::create([
                'order_id'       => null,
                'tipe_transaksi' => 'pemasukan',
                'jumlah_uang'    => $totalPendapatan,
                'keterangan'     => 'Hasil Penjualan Material ' . $material->nama_material . ' (' . $request->jumlah_kg . ' kg) ke ' . $request->supplier_name
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Distribusi material ke Supplier ' . $request->supplier_name . ' sebesar Rp ' . number_format($totalPendapatan, 0, ',', '.') . ' berhasil diproses! Stok terpotong & kas bertambah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal memproses distribusi supplier: ' . $e->getMessage()]);
        }
    }
}
