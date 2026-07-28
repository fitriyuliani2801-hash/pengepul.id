@extends('index')
@section('title', 'Notifikasi')
@section('isihalaman')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-bell text-warning"></i> Pusat Notifikasi
        </h1>
        <p class="text-muted mb-0">Pembaruan status persuratan, tugas penjemputan, dan transaksi baru.</p>
    </div>
    @if($notifikasi->whereNull('read_at')->count())
        <form method="POST" action="{{ route('notifikasi.read-all') }}">
            @csrf
            <button class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-check-double me-1"></i> Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

<div class="page-card overflow-hidden">
    @forelse($notifikasi as $item)
        <form method="POST" action="{{ route('notifikasi.read', $item) }}" class="border-bottom {{ $item->read_at ? '' : 'bg-light' }}">
            @csrf
            <button class="btn w-100 text-start p-3 rounded-0 transition-all hover-bg-slate">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="stat-icon-wrapper {{ $item->read_at ? 'stat-icon-primary' : 'stat-icon-warning' }}" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                            <i class="fa-solid {{ $item->read_at ? 'fa-bell-slash' : 'fa-bell' }}"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                {{ $item->judul }}
                                @if(!$item->read_at)
                                    <span class="badge bg-danger rounded-circle" style="width: 8px; height: 8px; padding: 0;"></span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1">{{ $item->pesan }}</div>
                        </div>
                    </div>
                    <small class="text-muted text-nowrap" style="font-size: 0.76rem;">
                        <i class="fa-regular fa-clock me-1"></i> {{ $item->created_at->diffForHumans() }}
                    </small>
                </div>
            </button>
        </form>
    @empty
        <div class="empty-state py-5">
            <i class="fa-regular fa-bell-slash fs-1 mb-2 text-muted d-block"></i>
            Belum ada notifikasi baru.
        </div>
    @endforelse
</div>
<div class="mt-3">{{ $notifikasi->links() }}</div>
@endsection
