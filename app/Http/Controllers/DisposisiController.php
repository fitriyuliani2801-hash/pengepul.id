<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposisiModel;

class DisposisiController extends Controller
{
    public function disposisitampil()
    {
        $datadisposisi = DisposisiModel::orderBy('id_disposisi', 'ASC')->paginate(10);
        return view('halaman/view_disposisi', ['disposisi' => $datadisposisi]);
    }

    public function disposisitambah(Request $request)
    {
        $request->validate([
            'id_surat_masuk' => 'required|integer',
            'tujuan_disposisi' => 'required|string|max:191',
            'isi_disposisi' => 'required|string',
            'sifat_disposisi' => 'required|string',
            'batas_waktu' => 'required|date',
        ]);

        DisposisiModel::create([
            'id_surat_masuk' => $request->id_surat_masuk,
            'tujuan_disposisi' => $request->tujuan_disposisi,
            'isi_disposisi' => $request->isi_disposisi,
            'sifat_disposisi' => $request->sifat_disposisi,
            'batas_waktu' => $request->batas_waktu,
        ]);

        return redirect('/disposisi')->with('success', 'Disposisi berhasil ditambahkan.');
    }

    public function disposisihapus($id_disposisi)
    {
        $datadisposisi = DisposisiModel::find($id_disposisi);
        abort_unless($datadisposisi, 404);
        $datadisposisi->delete();

        return redirect()->back()->with('success', 'Disposisi berhasil dihapus.');
    }

    public function disposisiedit($id_disposisi, Request $request)
    {
        $request->validate([
            'id_surat_masuk' => 'required|integer',
            'tujuan_disposisi' => 'required|string|max:191',
            'isi_disposisi' => 'required|string',
            'sifat_disposisi' => 'required|string',
            'batas_waktu' => 'required|date',
        ]);

        $datadisposisi = DisposisiModel::find($id_disposisi);
        abort_unless($datadisposisi, 404);

        $datadisposisi->id_surat_masuk = $request->id_surat_masuk;
        $datadisposisi->tujuan_disposisi = $request->tujuan_disposisi;
        $datadisposisi->isi_disposisi = $request->isi_disposisi;
        $datadisposisi->sifat_disposisi = $request->sifat_disposisi;
        $datadisposisi->batas_waktu = $request->batas_waktu;
        $datadisposisi->save();

        return redirect()->back()->with('success', 'Disposisi berhasil diperbarui.');
    }
}