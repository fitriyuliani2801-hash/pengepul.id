@extends('index')
@section('title', 'Dashboard')
@section('isihalaman')
<div class="page-card p-4 mb-4"><span class="badge bg-primary role-badge text-uppercase mb-2">{{ auth()->user()->role }}</span><h1 class="h3 mb-2">Selamat datang, {{ auth()->user()->name }}</h1><p class="text-muted mb-0">Kelola dan pantau surat Anda dari satu tempat yang rapi dan aman.</p></div>
<div class="row g-3">
    <div class="col-sm-6 col-xl-4"><a class="text-decoration-none" href="{{ route('suratmasuk.index') }}"><div class="page-card p-4 h-100"><div class="text-primary small fw-semibold text-uppercase">Arsip</div><h2 class="h5 text-dark mt-2 mb-1">Surat Masuk</h2><p class="text-muted small mb-0">Lihat dan kelola surat masuk.</p></div></a></div>
    <div class="col-sm-6 col-xl-4"><a class="text-decoration-none" href="{{ route('suratkeluar.index') }}"><div class="page-card p-4 h-100"><div class="text-success small fw-semibold text-uppercase">Arsip</div><h2 class="h5 text-dark mt-2 mb-1">Surat Keluar</h2><p class="text-muted small mb-0">Lihat dan kelola surat keluar.</p></div></a></div>
    @if(auth()->user()->hasRole('admin', 'staff'))<div class="col-sm-6 col-xl-4"><a class="text-decoration-none" href="{{ route('disposisi.index') }}"><div class="page-card p-4 h-100"><div class="text-warning small fw-semibold text-uppercase">Tindak lanjut</div><h2 class="h5 text-dark mt-2 mb-1">Disposisi</h2><p class="text-muted small mb-0">Atur distribusi dan batas waktu surat.</p></div></a></div>@endif
</div>
@endsection
