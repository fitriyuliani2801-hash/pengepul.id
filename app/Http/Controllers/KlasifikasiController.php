<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KlasifikasiModel;

class KlasifikasiController extends Controller
{
    public function klasifikasitampil()
    {
        $dataklasifikasi = KlasifikasiModel::orderBy('id_klasifikasi', 'ASC')->paginate(10);
        return view('halaman/view_klasifikasi', ['klasifikasi' => $dataklasifikasi]);
    }

    public function klasifikasitambah(Request $request)
    {
        $request->validate([
            'kode_klasifikasi' => 'required|string|max:50',
            'nama_klasifikasi' => 'required|string|max:191',
        ]);

        KlasifikasiModel::create([
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'nama_klasifikasi' => $request->nama_klasifikasi,
        ]);

        return redirect('/klasifikasi')->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function klasifikasihapus($id_klasifikasi)
    {
        $dataklasifikasi = KlasifikasiModel::find($id_klasifikasi);
        abort_unless($dataklasifikasi, 404);
        $dataklasifikasi->delete();

        return redirect()->back()->with('success', 'Klasifikasi berhasil dihapus.');
    }

    public function klasifikasiedit($id_klasifikasi, Request $request)
    {
        $request->validate([
            'kode_klasifikasi' => 'required|string|max:50',
            'nama_klasifikasi' => 'required|string|max:191',
        ]);

        $dataklasifikasi = KlasifikasiModel::find($id_klasifikasi);
        abort_unless($dataklasifikasi, 404);

        $dataklasifikasi->kode_klasifikasi = $request->kode_klasifikasi;
        $dataklasifikasi->nama_klasifikasi = $request->nama_klasifikasi;
        $dataklasifikasi->save();

        return redirect()->back()->with('success', 'Klasifikasi berhasil diperbarui.');
    }
}