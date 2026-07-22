<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratMasukModel;
use App\Models\Notifikasi;
use App\Models\User;

class SuratMasukController extends Controller
{
    public function suratmasuktampil()
    {
        $user = Auth::user();
        $query = SuratMasukModel::query();

        if ($user && $user->role === 'customer') {
            $query->where('user_id', $user->id);
        }

        $datasuratmasuk = $query->with(['pengirim', 'diprosesOleh'])->orderByDesc('id_surat_masuk')->paginate(10);

        return view('halaman/view_suratmasuk', ['surat_masuk' => $datasuratmasuk]);
    }

    public function suratmasuktambah(Request $request)
    {
        $request->validate([
            'no_agenda' => 'required|string|max:100',
            'no_surat' => 'required|string|max:100',
            'asal_surat' => 'required|string|max:191',
            'isi_ringkas' => 'required|string',
            'tgl_surat' => 'required|date',
            'tgl_diterima' => 'required|date',
            'file_surat' => 'required|file|mimes:pdf|max:2048',
            'keterangan' => 'nullable|string|max:255',
            'status_proses' => 'nullable|string|in:baru,diterima,sedang diproses,ditolak',
        ]);

        $filePath = $request->file('file_surat')->store('surat_masuk', 'public');

        $statusProses = Auth::user()->isAdmin()
            ? $request->input('status_proses', 'baru')
            : 'baru';
        $diprosesOleh = null;
        if (Auth::user()->isAdmin() && $statusProses !== 'baru') {
            $diprosesOleh = Auth::id();
        }

        $surat = SuratMasukModel::create([
            'no_agenda' => $request->no_agenda,
            'no_surat' => $request->no_surat,
            'asal_surat' => $request->asal_surat,
            'isi_ringkas' => $request->isi_ringkas,
            'tgl_surat' => $request->tgl_surat,
            'tgl_diterima' => $request->tgl_diterima,
            'file_surat' => $filePath,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
            'status_proses' => $statusProses,
            'diproses_oleh' => $diprosesOleh,
        ]);

        $this->notifyPetugas($surat);

        return redirect('/suratmasuk')->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function suratmasukhapus($id_surat_masuk)
    {
        $datasuratmasuk = SuratMasukModel::find($id_surat_masuk);
        abort_unless($datasuratmasuk, 404);
        $this->authorizeOwnerOrStaff($datasuratmasuk);
        $statusSebelumnya = $datasuratmasuk->status_proses ?? 'baru';

        if ($datasuratmasuk->file_surat && Storage::disk('public')->exists($datasuratmasuk->file_surat)) {
            Storage::disk('public')->delete($datasuratmasuk->file_surat);
        }

        $datasuratmasuk->delete();

        return redirect()->back()->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function suratmasukedit($id_surat_masuk, Request $request)
    {
        $request->validate([
            'no_agenda' => 'required|string|max:100',
            'no_surat' => 'required|string|max:100',
            'asal_surat' => 'required|string|max:191',
            'isi_ringkas' => 'required|string',
            'tgl_surat' => 'required|date',
            'tgl_diterima' => 'required|date',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
            'file_balasan' => 'nullable|file|mimes:pdf|max:2048',
            'keterangan' => 'nullable|string|max:255',
            'status_proses' => 'nullable|string|in:baru,diterima,sedang diproses,ditolak',
        ]);

        $datasuratmasuk = SuratMasukModel::find($id_surat_masuk);
        abort_unless($datasuratmasuk, 404);
        $this->authorizeOwnerOrStaff($datasuratmasuk);
        $statusSebelumnya = $datasuratmasuk->status_proses ?? 'baru';

        $datasuratmasuk->no_agenda = $request->no_agenda;
        $datasuratmasuk->no_surat = $request->no_surat;
        $datasuratmasuk->asal_surat = $request->asal_surat;
        $datasuratmasuk->isi_ringkas = $request->isi_ringkas;
        $datasuratmasuk->tgl_surat = $request->tgl_surat;
        $datasuratmasuk->tgl_diterima = $request->tgl_diterima;
        $datasuratmasuk->keterangan = $request->keterangan;
        $statusProses = $request->input('status_proses', $datasuratmasuk->status_proses ?? 'baru');
        // Status proses hanya boleh diubah oleh admin. Nilai dari request staff
        // atau customer sengaja diabaikan agar aturan ini tidak dapat dibypass.
        if (Auth::user()->isAdmin() && $request->has('status_proses')) {
            $datasuratmasuk->status_proses = $statusProses;
            if ($statusProses !== 'baru') {
                $datasuratmasuk->diproses_oleh = Auth::id();
            } elseif ($statusProses === 'baru') {
                $datasuratmasuk->diproses_oleh = null;
            }
        }

        if ($request->hasFile('file_surat')) {
            if ($datasuratmasuk->file_surat && Storage::disk('public')->exists($datasuratmasuk->file_surat)) {
                Storage::disk('public')->delete($datasuratmasuk->file_surat);
            }
            $datasuratmasuk->file_surat = $request->file('file_surat')->store('surat_masuk', 'public');
        }

        $hasNewReply = false;
        if (Auth::user()->hasRole('admin', 'staff') && $request->hasFile('file_balasan')) {
            if ($datasuratmasuk->file_balasan && Storage::disk('public')->exists($datasuratmasuk->file_balasan)) {
                Storage::disk('public')->delete($datasuratmasuk->file_balasan);
            }
            $datasuratmasuk->file_balasan = $request->file('file_balasan')->store('surat_masuk_balasan', 'public');
            $hasNewReply = true;
        }

        $datasuratmasuk->save();

        if ($statusSebelumnya !== $datasuratmasuk->status_proses || $hasNewReply) {
            $this->notifyStatusOrReplyChanged($datasuratmasuk, $hasNewReply);
        }

        return redirect()->back()->with('success', 'Surat masuk berhasil diperbarui.');
    }

    private function authorizeOwnerOrStaff(SuratMasukModel $surat): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if ($user->hasRole('admin', 'staff')) {
            return;
        }

        abort_unless($surat->user_id === $user->id, 403, 'Anda tidak memiliki akses ke surat ini.');
    }

    private function notifyPetugas(SuratMasukModel $surat): void
    {
        if (Auth::user()->hasRole('admin', 'staff')) {
            return;
        }

        User::whereIn('role', ['admin', 'staff'])->where('status', 'active')->each(function (User $user) use ($surat) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Surat masuk baru',
                'pesan' => 'Surat '.$surat->no_surat.' dari '.Auth::user()->name.' menunggu diproses.',
                'url' => route('suratmasuk.index'),
            ]);
        });
    }

    private function notifyStatusOrReplyChanged(SuratMasukModel $surat, bool $hasNewReply): void
    {
        // Staff yang aktif dan pemilik surat (customer) menerima pembaruan.
        // Admin tidak dibuatkan notifikasi atas perubahan yang dilakukannya sendiri.
        $penerima = User::where('role', 'staff')
            ->where('status', 'active')
            ->pluck('id');

        if ($surat->user_id) {
            $penerima->push($surat->user_id);
        }

        $penerima->unique()->each(function ($userId) use ($surat, $hasNewReply) {
            $judul = $hasNewReply ? 'Surat balasan masuk dikirim' : 'Status surat masuk diperbarui';
            $pesan = $hasNewReply 
                ? 'Admin telah mengirimkan surat balasan untuk surat '.$surat->no_surat.'.' 
                : 'Status surat '.$surat->no_surat.' sekarang: '.ucwords($surat->status_proses).'.';

            Notifikasi::create([
                'user_id' => $userId,
                'judul' => $judul,
                'pesan' => $pesan,
                'url' => route('suratmasuk.index'),
            ]);
        });
    }
}
