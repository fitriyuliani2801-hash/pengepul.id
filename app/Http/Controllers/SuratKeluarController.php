<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratKeluarModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Notifikasi;
use App\Models\User;

class SuratKeluarController extends Controller
{
    public function suratkeluartampil()
    {
        $user = Auth::user();

        $query = SuratKeluarModel::query();

        if ($user && $user->role === 'customer') {
            $query->where('user_id', $user->id);
        }

        $datasuratkeluar = $query->with(['pengirim', 'diprosesOleh'])->orderByDesc('id_surat_keluar')->paginate(10);

        return view('halaman/view_suratkeluar', ['surat_keluar' => $datasuratkeluar]);
    }

    public function suratkeluartambah(Request $request)
    {
        $request->validate([
            'no_agenda' => 'required|string|max:100',
            'no_surat' => 'required|string|max:100',
            'tujuan_surat' => 'required|string|max:191',
            'isi_ringkas' => 'required|string',
            'tgl_surat' => 'required|date',
            'file_surat' => 'required|file|mimes:pdf|max:2048',
            'keterangan' => 'nullable|string|max:255',
            'status_proses' => 'nullable|string|in:baru,diterima,sedang diproses,ditolak',
        ]);

        $filePath = $request->file('file_surat')->store('surat_keluar', 'public');

        $statusProses = Auth::user()->hasRole('admin', 'staff')
            ? $request->input('status_proses', 'baru')
            : 'baru';
        $diprosesOleh = null;
        if (Auth::user()->hasRole('admin', 'staff') && $statusProses !== 'baru') {
            $diprosesOleh = Auth::id();
        }

        $surat = SuratKeluarModel::create([
            'no_agenda' => $request->no_agenda,
            'no_surat' => $request->no_surat,
            'tujuan_surat' => $request->tujuan_surat,
            'isi_ringkas' => $request->isi_ringkas,
            'tgl_surat' => $request->tgl_surat,
            'file_surat' => $filePath,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
            'status_proses' => $statusProses,
            'diproses_oleh' => $diprosesOleh,
        ]);

        $this->notifyPetugas($surat);

        return redirect('/suratkeluar')->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function suratkeluaredit(Request $request, $id)
    {
        if (Auth::user()->hasRole('customer')) {
            abort(403, 'Warga tidak diizinkan mengubah surat keluar.');
        }

        $request->validate([
            'no_agenda' => 'required|string|max:100',
            'no_surat' => 'required|string|max:100',
            'tujuan_surat' => 'required|string|max:191',
            'isi_ringkas' => 'required|string',
            'tgl_surat' => 'required|date',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
            'keterangan' => 'nullable|string|max:255',
            'status_proses' => 'nullable|string|in:baru,diterima,sedang diproses,ditolak',
        ]);

        $surat = SuratKeluarModel::find($id);
        if (!$surat) {
            return redirect('/suratkeluar')->withErrors(['msg' => 'Data surat keluar tidak ditemukan.']);
        }
        $this->authorizeOwnerOrStaff($surat);
        $statusSebelumnya = $surat->status_proses ?? 'baru';

        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                Storage::disk('public')->delete($surat->file_surat);
            }
            $surat->file_surat = $request->file('file_surat')->store('surat_keluar', 'public');
        }

        $surat->no_agenda = $request->no_agenda;
        $surat->no_surat = $request->no_surat;
        $surat->tujuan_surat = $request->tujuan_surat;
        $surat->isi_ringkas = $request->isi_ringkas;
        $surat->tgl_surat = $request->tgl_surat;
        $surat->keterangan = $request->keterangan;
        $statusProses = $request->input('status_proses', $surat->status_proses ?? 'baru');
        if (Auth::user()->hasRole('admin', 'staff') && $request->has('status_proses')) {
            $surat->status_proses = $statusProses;
            if ($statusProses !== 'baru') {
                $surat->diproses_oleh = Auth::id();
            } elseif ($statusProses === 'baru') {
                $surat->diproses_oleh = null;
            }
        }
        $surat->save();

        if ($statusSebelumnya !== $surat->status_proses) {
            $this->notifyPemilikOrReplyChanged($surat, false);
        }

        return redirect('/suratkeluar')->with('success', 'Surat keluar berhasil diperbarui.');
    }

    public function suratkeluarhapus($id_surat_keluar)
    {
        if (Auth::user()->hasRole('customer')) {
            abort(403, 'Warga tidak diizinkan menghapus surat keluar.');
        }

        $datasuratkeluar = SuratKeluarModel::find($id_surat_keluar);
        if (!$datasuratkeluar) {
            return redirect('/suratkeluar')->withErrors(['msg' => 'Data surat keluar tidak ditemukan.']);
        }
        $this->authorizeOwnerOrStaff($datasuratkeluar);

        if ($datasuratkeluar->file_surat && Storage::disk('public')->exists($datasuratkeluar->file_surat)) {
            Storage::disk('public')->delete($datasuratkeluar->file_surat);
        }

        $datasuratkeluar->delete();

        return redirect()->back()->with('success', 'Surat keluar berhasil dihapus.');
    }

    private function authorizeOwnerOrStaff(SuratKeluarModel $surat): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        if ($user->hasRole('admin', 'staff')) {
            return;
        }

        abort_unless($surat->user_id === $user->id, 403, 'Anda tidak memiliki akses ke surat ini.');
    }

    private function notifyPetugas(SuratKeluarModel $surat): void
    {
        if (Auth::user()->hasRole('admin', 'staff')) {
            return;
        }

        User::whereIn('role', ['admin', 'staff'])->where('status', 'active')->each(function (User $user) use ($surat) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Surat keluar baru',
                'pesan' => 'Surat '.$surat->no_surat.' dari '.Auth::user()->name.' menunggu diproses.',
                'url' => route('suratkeluar.index'),
            ]);
        });
    }

    private function notifyPemilikOrReplyChanged(SuratKeluarModel $surat, bool $hasNewReply): void
    {
        if (!$surat->user_id) {
            return;
        }

        $statusText = ucfirst($surat->status_proses ?? 'dikirim');
        $pesan = 'Status surat keluar Anda (#'.$surat->no_surat.') diperbarui menjadi '.$statusText.'.';

        Notifikasi::create([
            'user_id' => $surat->user_id,
            'judul' => 'Update status surat keluar',
            'pesan' => $pesan,
            'url' => route('suratkeluar.index'),
        ]);
    }
}
