<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\KlasifikasiController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengepulWargaController;
use App\Http\Controllers\PengepulAdminController;
use App\Http\Controllers\PengepulDriverController;

// 1. Route Login & Register
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 2. Redirect /dashboard langsung ke menu utama yang lebih rapi
Route::get('/home', function () {
    $user = auth()->user();
    $stats = [];
    
    if ($user->role === 'customer') {
        $stats['saldoPendapatan'] = \App\Models\PenjemputanOrder::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_final_harga');
        
        $stats['activePickups'] = \App\Models\PenjemputanOrder::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'scheduled', 'processing'])
            ->count();
    } elseif ($user->hasRole('admin', 'staff')) {
        $stats['totalPickupsToday'] = \App\Models\PenjemputanOrder::where('tgl_jemput', date('Y-m-d'))->count();
        
        $kasPemasukan = \App\Models\KasPengepul::where('tipe_transaksi', 'pemasukan')->sum('jumlah_uang');
        $kasPengeluaran = \App\Models\KasPengepul::where('tipe_transaksi', 'pengeluaran')->sum('jumlah_uang');
        $stats['saldoKas'] = $kasPemasukan - $kasPengeluaran;
    } elseif ($user->role === 'driver') {
        $stats['activeTasksCount'] = \App\Models\PenjemputanOrder::where('driver_id', $user->id)
            ->whereIn('status', ['scheduled', 'processing'])
            ->count();
    }
    
    return view('menu', ['stats' => $stats]);
})->middleware(['auth'])->name('home');

Route::get('/menu', function () {
    return redirect()->route('home');
})->middleware(['auth'])->name('menu');

// 3. Halaman utama (Landing Page)
Route::get('/', function () {
    $katalog = \App\Models\SampahKatalog::all();
    return view('landing', ['katalog' => $katalog]);
})->name('landing');

// Route untuk Data (Semua di dalam middleware auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'readAll'])->name('notifikasi.read-all');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'read'])->name('notifikasi.read');
    
    // Route Data Disposisi
    Route::get('/disposisi', [DisposisiController::class, 'disposisitampil'])->middleware('role:admin,staff')->name('disposisi.index');
    Route::post('/disposisi/tambah', [DisposisiController::class, 'disposisitambah'])->middleware('role:admin,staff')->name('disposisi.store');
    Route::delete('/disposisi/{id_disposisi}', [DisposisiController::class, 'disposisihapus'])->middleware('role:admin,staff')->name('disposisi.destroy');
    Route::put('/disposisi/edit/{id_disposisi}', [DisposisiController::class, 'disposisiedit'])->middleware('role:admin,staff')->name('disposisi.update');

    // Route Data Klasifikasi
    Route::get('/klasifikasi', [KlasifikasiController::class, 'klasifikasitampil'])->middleware('role:admin,staff')->name('klasifikasi.index');
    Route::post('/klasifikasi/tambah', [KlasifikasiController::class, 'klasifikasitambah'])->middleware('role:admin,staff')->name('klasifikasi.store');
    Route::delete('/klasifikasi/{id_klasifikasi}', [KlasifikasiController::class, 'klasifikasihapus'])->middleware('role:admin,staff')->name('klasifikasi.destroy');
    Route::put('/klasifikasi/edit/{id_klasifikasi}', [KlasifikasiController::class, 'klasifikasiedit'])->middleware('role:admin,staff')->name('klasifikasi.update');

    // Route Data Surat Masuk
    Route::get('/suratmasuk', [SuratMasukController::class, 'suratmasuktampil'])->middleware('role:admin,staff,customer')->name('suratmasuk.index');
    Route::post('/suratmasuk/tambah', [SuratMasukController::class, 'suratmasuktambah'])->middleware('role:admin,staff,customer')->name('suratmasuk.store');
    Route::delete('/suratmasuk/{id_surat_masuk}', [SuratMasukController::class, 'suratmasukhapus'])->middleware('role:admin,staff,customer')->name('suratmasuk.destroy');
    Route::put('/suratmasuk/edit/{id_surat_masuk}', [SuratMasukController::class, 'suratmasukedit'])->middleware('role:admin,staff,customer')->name('suratmasuk.update');

    // Route Data Surat Keluar (Tidak Ada Balasan)
    Route::get('/suratkeluar', [SuratKeluarController::class, 'suratkeluartampil'])->middleware('role:admin,staff,customer')->name('suratkeluar.index');
    Route::post('/suratkeluar/tambah', [SuratKeluarController::class, 'suratkeluartambah'])->middleware('role:admin,staff,customer')->name('suratkeluar.store');
    Route::delete('/suratkeluar/{id_surat_keluar}', [SuratKeluarController::class, 'suratkeluarhapus'])->middleware('role:admin,staff,customer')->name('suratkeluar.destroy');
    Route::put('/suratkeluar/edit/{id_surat_keluar}', [SuratKeluarController::class, 'suratkeluaredit'])->middleware('role:admin,staff,customer')->name('suratkeluar.update');

    // 5. Route Modul Pengepul Sampah
    // A. Warga (Customer) Routes
    Route::get('/pengepul/warga', [PengepulWargaController::class, 'index'])->middleware('role:admin,staff,customer')->name('pengepul.warga.index');
    Route::post('/pengepul/warga/cart/add', [PengepulWargaController::class, 'addToCart'])->middleware('role:admin,staff,customer')->name('pengepul.warga.cart.add');
    Route::delete('/pengepul/warga/cart/remove/{id}', [PengepulWargaController::class, 'removeFromCart'])->middleware('role:admin,staff,customer')->name('pengepul.warga.cart.remove');
    Route::post('/pengepul/warga/cart/clear', [PengepulWargaController::class, 'clearCart'])->middleware('role:admin,staff,customer')->name('pengepul.warga.cart.clear');
    Route::post('/pengepul/warga/checkout', [PengepulWargaController::class, 'checkout'])->middleware('role:admin,staff,customer')->name('pengepul.warga.checkout');

    // B. Admin & Staff Routes
    Route::get('/pengepul/admin', [PengepulAdminController::class, 'index'])->middleware('role:admin,staff')->name('pengepul.admin.index');
    Route::put('/pengepul/admin/katalog/price/{id}', [PengepulAdminController::class, 'updatePrice'])->middleware('role:admin,staff')->name('pengepul.admin.katalog.update-price');
    Route::post('/pengepul/admin/assign/{id}', [PengepulAdminController::class, 'assignDriver'])->middleware('role:admin,staff')->name('pengepul.admin.assign-driver');
    Route::post('/pengepul/admin/pembayaran/proses/{id}', [PengepulAdminController::class, 'prosesPembayaran'])->middleware('role:admin,staff')->name('pengepul.admin.pembayaran.proses');

    // C. Driver Routes
    Route::get('/pengepul/driver', [PengepulDriverController::class, 'index'])->middleware('role:driver')->name('pengepul.driver.index');
    Route::post('/pengepul/driver/pickup/start/{id}', [PengepulDriverController::class, 'startPickup'])->middleware('role:driver')->name('pengepul.driver.pickup.start');
    Route::post('/pengepul/driver/pickup/complete/{id}', [PengepulDriverController::class, 'completePickup'])->middleware('role:driver')->name('pengepul.driver.pickup.complete');
    Route::post('/pengepul/driver/location/update/{id}', [PengepulDriverController::class, 'updateLocation'])->middleware('role:driver')->name('pengepul.driver.location.update');

    // D. Shared Chat, Tracking & Surat Jalan Routes
    Route::get('/pengepul/surat-jalan/{id}', [PengepulAdminController::class, 'cetakSuratJalan'])->name('pengepul.surat-jalan');
    Route::get('/pengepul/chat/fetch/{order_id}', [PengepulWargaController::class, 'fetchChatMessages'])->name('pengepul.chat.fetch');
    Route::post('/pengepul/chat/send', [PengepulWargaController::class, 'sendChatMessage'])->name('pengepul.chat.send');
    Route::get('/pengepul/tracking/fetch/{order_id}', [PengepulWargaController::class, 'fetchDriverTracking'])->name('pengepul.tracking.fetch');

    // E. Profile Routes (All Authenticated Roles)
    Route::get('/profil', [PengepulWargaController::class, 'showProfile'])->name('profil.index');
    Route::post('/profil/update', [PengepulWargaController::class, 'updateProfile'])->name('profil.update');
});