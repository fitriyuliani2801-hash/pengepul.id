<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Notifikasi;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Halaman Utama Kamera Scan Wajah Absensi (Masuk / Pulang)
     */
    public function scanIndex()
    {
        $user = Auth::user();

        if (!$user->face_photo) {
            return redirect()->route('absensi.enrollment')
                ->with('warning', 'Wajah Anda belum terdaftar. Silakan selesaikan Registrasi & Tes Gerakan Wajah terlebih dahulu.');
        }

        $today = Carbon::today()->format('Y-m-d');

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->where('tgl_absensi', $today)
            ->first();

        return view('halaman/absensi/scan', [
            'user' => $user,
            'absensiHariIni' => $absensiHariIni,
            'todayDate' => Carbon::now()->isoFormat('D MMMM YYYY')
        ]);
    }

    /**
     * Halaman Enrollment / Registrasi Wajah Karyawan
     */
    public function enrollmentIndex()
    {
        $user = Auth::user();
        return view('halaman/absensi/enrollment', [
            'user' => $user
        ]);
    }

    /**
     * Simpan Data Acuan Wajah Karyawan (Enrollment)
     */
    public function simpanEnrollment(Request $request)
    {
        $request->validate([
            'image_base64' => 'required|string',
            'face_descriptor' => 'nullable|string'
        ]);

        $user = Auth::user();

        try {
            $imageParts = explode(";base64,", $request->image_base64);
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = isset($imageTypeAux[1]) ? $imageTypeAux[1] : 'jpg';
            $imageBase64 = base64_decode($imageParts[1]);

            $folderPath = public_path('uploads/absensi_faces');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $fileName = 'face_user_' . $user->id . '_' . time() . '.' . $imageType;
            $filePath = $folderPath . '/' . $fileName;

            File::put($filePath, $imageBase64);

            $user->update([
                'face_photo' => 'uploads/absensi_faces/' . $fileName,
                'face_descriptor' => $request->face_descriptor ?: $user->face_descriptor
            ]);

            return redirect()->route('absensi.scan')
                ->with('success', 'Wajah Anda berhasil didaftarkan! Sekarang Anda dapat melakukan Absen Scan Wajah.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Gagal menyalin sampel wajah: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses Absen Masuk / Pulang dengan Scan Wajah & Kamera
     */
    public function prosesScan(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:masuk,pulang',
            'image_base64' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'skor_kemiripan' => 'nullable|numeric'
        ]);

        $user = Auth::user();

        if (!$user->face_photo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wajah Anda belum terdaftar di sistem. Silakan lakukan pendaftaran wajah terlebih dahulu.'
            ], 400);
        }

        $now = Carbon::now();
        $todayDate = $now->format('Y-m-d');
        $currentTimeStr = $now->format('H:i:s');

        try {
            // Simpan foto bukti scan
            $imageParts = explode(";base64,", $request->image_base64);
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = isset($imageTypeAux[1]) ? $imageTypeAux[1] : 'jpg';
            $imageBase64 = base64_decode($imageParts[1]);

            $folderPath = public_path('uploads/absensi_scans');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $fileName = 'scan_' . $request->tipe . '_user_' . $user->id . '_' . date('Ymd_His') . '.' . $imageType;
            $filePath = $folderPath . '/' . $fileName;
            File::put($filePath, $imageBase64);

            $relativePhotoPath = 'uploads/absensi_scans/' . $fileName;

            // Cari / Buat Record Absensi Hari Ini
            $absensi = Absensi::firstOrNew([
                'user_id' => $user->id,
                'tgl_absensi' => $todayDate
            ]);

            $skor = $request->skor_kemiripan ?: 98.5;

            if ($request->tipe === 'masuk') {
                if ($absensi->jam_masuk) {
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Anda sudah melakukan Absen Masuk hari ini pada jam ' . $absensi->jam_masuk . '.'
                    ], 400);
                }

                // Jam masuk baku misal 08:00
                $statusMasuk = ($currentTimeStr > '08:00:00') ? 'terlambat' : 'tepat_waktu';

                $absensi->jam_masuk = $currentTimeStr;
                $absensi->foto_masuk = $relativePhotoPath;
                $absensi->status_masuk = $statusMasuk;
                $absensi->skor_kemiripan = $skor;
                $absensi->latitude_masuk = $request->latitude;
                $absensi->longitude_masuk = $request->longitude;
                $absensi->save();

                Notifikasi::create([
                    'user_id' => $user->id,
                    'judul' => 'Absensi Masuk Berhasil',
                    'pesan' => 'Absen Masuk berhasil dicatat via Scan Wajah (' . strtoupper(str_replace('_', ' ', $statusMasuk)) . ') pada jam ' . $currentTimeStr,
                    'url' => route('absensi.riwayat')
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absen Masuk Berhasil! Status: ' . ($statusMasuk === 'terlambat' ? 'Terlambat' : 'Tepat Waktu'),
                    'redirect' => route('absensi.scan')
                ]);
            } else {
                if (!$absensi->jam_masuk) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda belum melakukan Absen Masuk hari ini. Silakan Absen Masuk terlebih dahulu.'
                    ], 400);
                }

                if ($absensi->jam_pulang) {
                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Anda sudah melakukan Absen Pulang hari ini pada jam ' . $absensi->jam_pulang . '.'
                    ], 400);
                }

                // Jam pulang baku misal 17:00
                $statusPulang = ($currentTimeStr < '17:00:00') ? 'pulang_cepat' : 'tepat_waktu';

                $absensi->jam_pulang = $currentTimeStr;
                $absensi->foto_pulang = $relativePhotoPath;
                $absensi->status_pulang = $statusPulang;
                $absensi->latitude_pulang = $request->latitude;
                $absensi->longitude_pulang = $request->longitude;
                $absensi->save();

                Notifikasi::create([
                    'user_id' => $user->id,
                    'judul' => 'Absensi Pulang Berhasil',
                    'pesan' => 'Absen Pulang berhasil dicatat via Scan Wajah pada jam ' . $currentTimeStr,
                    'url' => route('absensi.riwayat')
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absen Pulang Berhasil! Terima kasih atas kerja keras Anda hari ini.',
                    'redirect' => route('absensi.scan')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat memproses scan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Riwayat Absensi Karyawan Bersangkutan
     */
    public function riwayat()
    {
        $user = Auth::user();
        $riwayat = Absensi::where('user_id', $user->id)
            ->orderByDesc('tgl_absensi')
            ->paginate(15);

        return view('halaman/absensi/riwayat', [
            'user' => $user,
            'riwayat' => $riwayat
        ]);
    }

    /**
     * Rekapitulasi Absensi Seluruh Karyawan untuk Admin & Staff
     */
    public function rekapAdmin(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin', 'staff')) {
            abort(403, 'Akses Ditolak');
        }

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $role = $request->input('role');

        $query = Absensi::with('user')
            ->where('tgl_absensi', $tanggal);

        if ($role) {
            $query->whereHas('user', function($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $absensiList = $query->orderByDesc('id')->get();

        // Karyawan aktif yang belum absen hari ini
        $karyawanQuery = User::whereIn('role', ['staff', 'driver', 'admin'])->where('status', 'active');
        if ($role) {
            $karyawanQuery->where('role', $role);
        }
        $semuaKaryawan = $karyawanQuery->get();

        $absentUserIds = $absensiList->pluck('user_id')->toArray();
        $belumAbsen = $semuaKaryawan->whereNotIn('id', $absentUserIds);

        $totalHadir = $absensiList->count();
        $totalTerlambat = $absensiList->where('status_masuk', 'terlambat')->count();
        $totalTepatWaktu = $absensiList->where('status_masuk', 'tepat_waktu')->count();

        return view('halaman/absensi/admin_rekap', [
            'absensiList' => $absensiList,
            'belumAbsen' => $belumAbsen,
            'tanggal' => $tanggal,
            'roleFilter' => $role,
            'totalHadir' => $totalHadir,
            'totalTerlambat' => $totalTerlambat,
            'totalTepatWaktu' => $totalTepatWaktu,
            'totalBelumAbsen' => $belumAbsen->count()
        ]);
    }
}
