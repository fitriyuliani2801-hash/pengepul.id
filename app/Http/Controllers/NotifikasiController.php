<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('halaman.notifikasi', compact('notifikasi'));
    }

    public function read(Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === Auth::id(), 403);
        $notifikasi->update(['read_at' => now()]);

        return redirect($notifikasi->url ?: route('home'));
    }

    public function readAll(): RedirectResponse
    {
        Notifikasi::where('user_id', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
