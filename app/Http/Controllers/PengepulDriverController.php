<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PenjemputanOrder;
use App\Models\PenjemputanItem;
use App\Models\KasPengepul;
use App\Models\StokGudang;
use App\Models\SuratKeluarModel;
use App\Models\Notifikasi;

class PengepulDriverController extends Controller
{
    public function index()
    {
        $myTasks = PenjemputanOrder::with(['warga', 'items.material'])
            ->where('driver_id', Auth::id())
            ->orderByRaw("FIELD(status, 'processing', 'scheduled', 'completed', 'cancelled')")
            ->orderByDesc('id')
            ->paginate(5);

        return view('halaman/pengepul/driver_dashboard', [
            'myTasks' => $myTasks
        ]);
    }

    public function startPickup($id)
    {
        $order = PenjemputanOrder::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();
        
        DB::beginTransaction();
        try {
            $order->update(['status' => 'processing']);

            // Update SPK (SuratKeluar)
            if ($order->id_surat_keluar) {
                $spk = SuratKeluarModel::find($order->id_surat_keluar);
                if ($spk) {
                    $spk->update(['status_proses' => 'sedang diproses']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Penjemputan dimulai. Silakan menuju ke koordinat warga.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }

    public function completePickup(Request $request, $id)
    {
        $order = PenjemputanOrder::with('items')->where('id', $id)->where('driver_id', Auth::id())->firstOrFail();

        $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,transfer',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'catatan_pembayaran' => 'nullable|string|max:500'
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'tf_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_transfer'), $filename);
            $buktiPath = 'uploads/bukti_transfer/' . $filename;
        }

        DB::beginTransaction();
        try {
            $totalFinalHarga = 0;

            // 1. Simpan Berat Final per Item & Hitung Total
            foreach ($request->weights as $itemId => $weight) {
                $item = PenjemputanItem::findOrFail($itemId);
                $item->update([
                    'final_berat' => $weight
                ]);

                $totalFinalHarga += $weight * $item->harga_beli_per_kg;

                // 2. Tambah ke Stok Gudang
                if ($weight > 0) {
                    StokGudang::create([
                        'material_id' => $item->material_id,
                        'jumlah_kg' => $weight,
                        'tipe_stok' => 'masuk',
                        'keterangan' => 'Pembelian dari Warga via Order No. ' . $order->order_no
                    ]);
                }
            }

            // Bersih Pembayaran ke Warga (estimasi harga total - biaya jemput)
            $payout = $totalFinalHarga - $order->biaya_jemput;
            if ($payout < 0) {
                $payout = 0;
            }

            // 3. Update Order
            $order->update([
                'status' => 'completed',
                'total_final_harga' => $payout,
                'metode_pembayaran' => $request->payment_method,
                'status_pembayaran' => 'paid',
                'bukti_transfer' => $buktiPath ?? $order->bukti_transfer,
                'catatan_pembayaran' => $request->catatan_pembayaran
            ]);

            // 4. Update SPK status
            if ($order->id_surat_keluar) {
                $spk = SuratKeluarModel::find($order->id_surat_keluar);
                if ($spk) {
                    $spk->update(['status_proses' => 'diterima']);
                }
            }

            // 5. Catat di Kas Pengepul (Kas Pengeluaran)
            if ($payout > 0) {
                KasPengepul::create([
                    'order_id' => $order->id,
                    'tipe_transaksi' => 'pengeluaran',
                    'jumlah_uang' => $payout,
                    'keterangan' => 'Pembayaran sampah ' . ($request->payment_method === 'cash' ? 'Tunai (Cash)' : 'Transfer Bank/E-Wallet') . ' ke Warga via Order ' . $order->order_no
                ]);
            }

            // Jika ada biaya jemput, catat sebagai kas masuk (pemasukan/ongkir operasional)
            if ($order->biaya_jemput > 0) {
                KasPengepul::create([
                    'order_id' => $order->id,
                    'tipe_transaksi' => 'pemasukan',
                    'jumlah_uang' => $order->biaya_jemput,
                    'keterangan' => 'Pemasukan Ongkos Kirim/Jemput untuk Order ' . $order->order_no
                ]);
            }

            // 6. Kirim notifikasi ke Warga / customer
            Notifikasi::create([
                'user_id' => $order->user_id,
                'judul' => 'Penjemputan Sampah Selesai',
                'pesan' => 'Sampah Anda telah ditimbang. Pembayaran sebesar Rp ' . number_format($payout, 0, ',', '.') . ' dikirim via ' . ($request->payment_method === 'cash' ? 'Tunai (Cash)' : 'Transfer Bank / E-Wallet') . '.',
                'url' => route('pengepul.warga.index')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Penjemputan berhasil diselesaikan! Timbangan dan pembayaran (' . strtoupper($request->payment_method) . ') telah dicatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal menyelesaikan penjemputan: ' . $e->getMessage()]);
        }
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $order = PenjemputanOrder::where('id', $id)->where('driver_id', Auth::id())->firstOrFail();

        $order->update([
            'driver_latitude' => $request->latitude,
            'driver_longitude' => $request->longitude
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Posisi lokasi petugas berhasil diperbarui.'
        ]);
    }
}
