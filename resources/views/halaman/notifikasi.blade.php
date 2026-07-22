@extends('index')
@section('title', 'Notifikasi')
@section('isihalaman')
<div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div><h1 class="h3 mb-1">Notifikasi</h1><p class="text-muted mb-0">Pembaruan status dan surat baru ditampilkan di sini.</p></div>
    @if($notifikasi->whereNull('read_at')->count())
        <form method="POST" action="{{ route('notifikasi.read-all') }}">@csrf<button class="btn btn-outline-primary">Tandai semua dibaca</button></form>
    @endif
</div>
<div class="page-card overflow-hidden">
    @forelse($notifikasi as $item)
        <form method="POST" action="{{ route('notifikasi.read', $item) }}" class="border-bottom {{ $item->read_at ? '' : 'bg-light' }}">@csrf
            <button class="btn w-100 text-start p-3 rounded-0">
                <div class="d-flex justify-content-between gap-3"><div><div class="fw-semibold">{{ $item->judul }}</div><div class="text-muted small mt-1">{{ $item->pesan }}</div></div><small class="text-muted text-nowrap">{{ $item->created_at->diffForHumans() }}</small></div>
            </button>
        </form>
    @empty
        <div class="empty-state">Belum ada notifikasi.</div>
    @endforelse
</div>
<div class="mt-3">{{ $notifikasi->links() }}</div>
@endsection
