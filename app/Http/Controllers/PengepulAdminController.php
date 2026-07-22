<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->select('sampah_katalog.nama_material', 'sampah_katalog.icon',
                DB::raw("SUM(CASE WHEN tipe_stok = 'masuk' THEN jumlah_kg ELSE -jumlah_kg END) as total_berat"))
            ->groupBy('sampah_katalog.nama_material', 'sampah_katalog.icon')
            ->get();

        return view('halaman/pengepul/admin_dashboard', [
            'orders' => $orders,
            'drivers' => $drivers,
            'katalog' => $katalog,
            'kas' => $kas,
            'saldoKas' => $saldoKas,
            'stok' => $stok,
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
                    'keterangan' => 'Pembayaran sampah ' . ($request->payment_method === 'cash' ? 'Tunai (Cash)' : 'Transfer Bank/E-Wallet') . ' oleh Admin ke Warga via Order ' . $order->order_no
                ]);
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
        $order = PenjemputanOrder::with(['warga', 'driver', 'items.material'])->findOrFail($id);

        return view('halaman/pengepul/surat_jalan', [
            'order' => $order
        ]);
    }
}
