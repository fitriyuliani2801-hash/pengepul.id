<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SampahKatalog;
use App\Models\PenjemputanOrder;
use App\Models\PenjemputanItem;
use App\Models\SuratKeluarModel;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\OrderChat;

class PengepulWargaController extends Controller
{
    // Gudang pengepul koordinat default (Jakarta)
    private $warehouseLat = -6.2088;
    private $warehouseLng = 106.8456;

    public function index()
    {
        $katalog = SampahKatalog::all();
        $cart = session()->get('sampah_cart', []);
        
        $myOrders = PenjemputanOrder::with(['driver', 'items.material'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(5);

        return view('halaman/pengepul/warga_dashboard', [
            'katalog' => $katalog,
            'cart' => $cart,
            'myOrders' => $myOrders,
            'warehouseLat' => $this->warehouseLat,
            'warehouseLng' => $this->warehouseLng
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:sampah_katalog,id',
            'estimasi_berat' => 'required|numeric|min:0.1'
        ]);

        $material = SampahKatalog::find($request->material_id);
        $cart = session()->get('sampah_cart', []);

        // Key by material_id
        $cart[$request->material_id] = [
            'id' => $material->id,
            'nama' => $material->nama_material,
            'harga_per_kg' => $material->harga_beli_per_kg,
            'icon' => $material->icon,
            'berat' => $request->estimasi_berat,
            'estimasi_subtotal' => $material->harga_beli_per_kg * $request->estimasi_berat
        ];

        session()->put('sampah_cart', $cart);

        return redirect()->back()->with('success', 'Sampah ditambahkan ke keranjang.');
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('sampah_cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('sampah_cart', $cart);
        }
        return redirect()->back()->with('success', 'Sampah dihapus dari keranjang.');
    }

    public function clearCart()
    {
        session()->forget('sampah_cart');
        return redirect()->back()->with('success', 'Keranjang dikosongkan.');
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('sampah_cart', []);
        if (empty($cart)) {
            return redirect()->back()->withErrors(['msg' => 'Keranjang kosong!']);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'tgl_jemput' => 'required|date|after_or_equal:today',
            'jam_jemput' => 'required|string',
        ]);

        // Hitung jarak Haversine
        $distance = $this->calculateDistance(
            $this->warehouseLat, 
            $this->warehouseLng, 
            $request->latitude, 
            $request->longitude
        );

        // Batas wilayah operasional: max 30 km
        if ($distance > 30.0) {
            return redirect()->back()->withErrors(['msg' => 'Maaf, lokasi Anda (' . round($distance, 2) . ' km) di luar batas operasional jemput (maks. 30 km).']);
        }

        // Biaya jemput: Rp2.000 per KM, free jika jarak <= 2 KM atau total estimasi berat >= 10 kg
        $totalBerat = collect($cart)->sum('berat');
        $biayaJemput = ($distance <= 2.0 || $totalBerat >= 10.0) ? 0 : round($distance * 2000);

        $totalEstimasiHarga = collect($cart)->sum('estimasi_subtotal');

        DB::beginTransaction();
        try {
            $orderNo = 'TRASH-' . date('Ymd') . '-' . strtoupper(uniqid());

            // 1. Buat Surat Perintah Kerja (SPK) di tabel surat_keluar
            $spk = SuratKeluarModel::create([
                'no_agenda' => 'SPK-' . date('ymd') . rand(10, 99),
                'no_surat' => 'SPK/JEMPUT/' . $orderNo,
                'tujuan_surat' => 'Petugas Lapangan / Driver Pengepul',
                'isi_ringkas' => 'Surat Tugas Penjemputan Sampah warga: ' . Auth::user()->name . '. Rincian estimasi berat: ' . $totalBerat . ' kg. Jadwal jemput: ' . $request->tgl_jemput . ' jam ' . $request->jam_jemput . '.',
                'tgl_surat' => date('Y-m-d'),
                'file_surat' => null, // Dynamic view / print page used for driver
                'keterangan' => 'Alamat jemput: Latitude ' . $request->latitude . ', Longitude ' . $request->longitude . '. Jarak: ' . round($distance, 2) . ' km.',
                'user_id' => Auth::id(),
                'status_proses' => 'baru'
            ]);

            // 2. Buat Order
            $order = PenjemputanOrder::create([
                'order_no' => $orderNo,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'jarak_km' => $distance,
                'biaya_jemput' => $biayaJemput,
                'tgl_jemput' => $request->tgl_jemput,
                'jam_jemput' => $request->jam_jemput,
                'total_estimasi_harga' => $totalEstimasiHarga,
                'total_final_harga' => 0,
                'driver_id' => null,
                'id_surat_keluar' => $spk->id_surat_keluar
            ]);

            // 3. Simpan items
            foreach ($cart as $item) {
                PenjemputanItem::create([
                    'order_id' => $order->id,
                    'material_id' => $item['id'],
                    'estimasi_berat' => $item['berat'],
                    'final_berat' => null,
                    'harga_beli_per_kg' => $item['harga_per_kg']
                ]);
            }

            // 4. Kirim notifikasi ke Admin & Staff
            User::whereIn('role', ['admin', 'staff'])->where('status', 'active')->each(function($admin) use ($orderNo) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Order Jemput Baru',
                    'pesan' => 'Ada permintaan penjemputan sampah baru dengan No. ' . $orderNo,
                    'url' => route('pengepul.admin.index')
                ]);
            });

            DB::commit();
            session()->forget('sampah_cart');

            return redirect()->back()->with('success', 'Order penjemputan sampah berhasil dibuat! SPK untuk Driver telah diterbitkan otomatis.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Gagal membuat order: ' . $e->getMessage()]);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c; // km
    }

    public function showProfile()
    {
        return view('halaman/pengepul/profil', [
            'user' => Auth::user(),
            'warehouseLat' => $this->warehouseLat,
            'warehouseLng' => $this->warehouseLng
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bank_nama' => 'nullable|string|max:50',
            'bank_nomor' => 'nullable|string|max:50',
            'ewallet_nama' => 'nullable|string|max:50',
            'ewallet_nomor' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        $data = $request->only([
            'name', 'email', 'no_hp', 'alamat', 'latitude', 'longitude',
            'bank_nama', 'bank_nomor', 'ewallet_nama', 'ewallet_nomor'
        ]);

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        // Use mass assignment on User
        \App\Models\User::where('id', $user->id)->update($data);

        return redirect()->back()->with('success', 'Profil dan informasi pembayaran berhasil diperbarui.');
    }

    public function fetchChatMessages($order_id)
    {
        $order = PenjemputanOrder::with(['warga', 'driver'])->findOrFail($order_id);

        $chats = OrderChat::with('sender')
            ->where('order_id', $order_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'sender_id' => $chat->sender_id,
                    'sender_name' => $chat->sender ? $chat->sender->name : 'Sistem',
                    'sender_role' => $chat->sender ? $chat->sender->role : 'system',
                    'message' => $chat->message,
                    'created_at' => $chat->created_at->format('H:i, d M Y'),
                    'is_me' => $chat->sender_id == Auth::id()
                ];
            });

        return response()->json([
            'success' => true,
            'chats' => $chats,
            'order' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'warga_name' => $order->warga ? $order->warga->name : '-',
                'warga_hp' => $order->warga ? $order->warga->no_hp : '',
                'driver_name' => $order->driver ? $order->driver->name : '-',
                'driver_hp' => $order->driver ? $order->driver->no_hp : ''
            ]
        ]);
    }

    public function sendChatMessage(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:penjemputan_orders,id',
            'message' => 'required|string|max:1000'
        ]);

        $order = PenjemputanOrder::findOrFail($request->order_id);

        // Determine recipient
        $receiverId = null;
        if (Auth::id() == $order->user_id) {
            $receiverId = $order->driver_id;
        } else {
            $receiverId = $order->user_id;
        }

        $chat = OrderChat::create([
            'order_id' => $order->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'is_read' => false
        ]);

        // Kirim Notifikasi jika recipient ada
        if ($receiverId) {
            Notifikasi::create([
                'user_id' => $receiverId,
                'judul' => 'Pesan Chat Baru - Order ' . $order->order_no,
                'pesan' => Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->message, 50),
                'url' => route('pengepul.warga.index')
            ]);
        }

        return response()->json([
            'success' => true,
            'chat' => [
                'id' => $chat->id,
                'sender_id' => $chat->sender_id,
                'sender_name' => Auth::user()->name,
                'sender_role' => Auth::user()->role,
                'message' => $chat->message,
                'created_at' => $chat->created_at->format('H:i, d M Y'),
                'is_me' => true
            ]
        ]);
    }

    public function fetchDriverTracking($order_id)
    {
        $order = PenjemputanOrder::with(['driver', 'warga'])->findOrFail($order_id);

        $driverLat = $order->driver_latitude ? (float)$order->driver_latitude : ($this->warehouseLat);
        $driverLng = $order->driver_longitude ? (float)$order->driver_longitude : ($this->warehouseLng);

        return response()->json([
            'success' => true,
            'order_no' => $order->order_no,
            'status' => $order->status,
            'warga_lat' => (float)$order->latitude,
            'warga_lng' => (float)$order->longitude,
            'driver_lat' => $driverLat,
            'driver_lng' => $driverLng,
            'driver_name' => $order->driver ? $order->driver->name : 'Belum Ditugaskan',
            'driver_hp' => $order->driver ? $order->driver->no_hp : '',
            'updated_at' => $order->updated_at->format('H:i, d M Y')
        ]);
    }
}
