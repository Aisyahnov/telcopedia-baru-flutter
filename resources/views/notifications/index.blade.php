@extends('layouts.app')
@section('title', 'Notifikasi Saya - Telcopedia')

@section('hero_title', 'Pusat Notifikasi')
@section('hero_subtitle', 'Pantau semua aktivitas dan pengajuan Anda di sini.')

@section('content')
@php
    $isDashboard = Auth::check() && Auth::user()->role !== 'buyer';
@endphp

@php
    $isDashboard = Auth::check() && Auth::user()->role !== 'buyer';
@endphp

<div class="{{ $isDashboard ? '' : 'container py-5' }}">
    <div class="row {{ $isDashboard ? '' : 'justify-content-center' }}">
        <div class="{{ $isDashboard ? 'col-12' : 'col-lg-9' }}">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-900 mb-1 text-dark tracking-tighter">Notifikasi</h4>
                    <p class="text-muted small mb-0">Informasi terbaru mengenai aktivitas akun dan transaksi Anda.</p>
                </div>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <a href="{{ route('notifications.read_all') }}" class="btn btn-maroon rounded-pill px-4 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check-double me-2"></i>Tandai Semua Dibaca
                    </a>
                @endif
            </div>

            <div class="card border-0 shadow-sm rounded-24 overflow-hidden">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notif)
                        <div class="list-group-item p-0 {{ $notif->unread() ? 'bg-light' : '' }} border-bottom position-relative notification-item" style="transition: 0.3s;">
                            @if($notif->unread())
                                <div class="position-absolute top-0 start-0 h-100 bg-maroon" style="width: 4px; z-index: 2;"></div>
                            @endif
                            
                            <div class="p-4">
                                <div class="d-flex align-items-start gap-4">
                                    <div class="flex-shrink-0 d-none d-md-block">
                                        @php
                                            $icon = 'fa-info-circle';
                                            $bg = 'bg-secondary-subtle text-secondary';
                                            if(($notif->data['type'] ?? '') == 'product') { $icon = 'fa-box-open'; $bg = 'bg-warning-subtle text-warning'; }
                                            if(($notif->data['type'] ?? '') == 'order') { $icon = 'fa-shopping-cart'; $bg = 'bg-success-subtle text-success'; }
                                            if(($notif->data['type'] ?? '') == 'penarikan') { $icon = 'fa-wallet'; $bg = 'bg-primary-subtle text-primary'; }
                                        @endphp
                                        <div class="{{ $bg }} rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; border: 4px solid #fff;">
                                            <i class="fa-solid {{ $icon }} fs-4"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-800 mb-0 {{ $notif->unread() ? 'text-dark' : 'text-muted' }}" style="font-size: 1.05rem;">{{ $notif->data['title'] ?? 'Pemberitahuan' }}</h6>
                                            <span class="text-muted x-small fw-bold opacity-50">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <p class="{{ $notif->unread() ? 'text-dark' : 'text-muted' }} mb-3" style="font-size: 0.95rem; line-height: 1.6; font-weight: {{ $notif->unread() ? '500' : '400' }};">
                                            {{ $notif->data['message'] ?? '' }}
                                        </p>
                                        
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($notif->unread())
                                                <a href="{{ route('notifications.read', $notif->id) }}" class="btn btn-sm btn-maroon rounded-pill px-4 py-2 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                                    Buka Detail <i class="fa-solid fa-arrow-right ms-2 small"></i>
                                                </a>
                                            @elseif(isset($notif->data['action_url']))
                                                <a href="{{ $notif->data['action_url'] }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 py-2 fw-bold" style="font-size: 0.75rem;">
                                                    Tinjau Kembali
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-5 px-4 text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="fa-solid fa-bell-slash fs-1 text-muted opacity-25"></i>
                            </div>
                            <h5 class="fw-900 text-dark mb-2">Belum Ada Notifikasi</h5>
                            <p class="text-muted mx-auto" style="max-width: 300px;">Semua informasi mengenai akun, produk, dan transaksi Anda akan muncul di sini.</p>
                            <a href="{{ route('home') }}" class="btn btn-maroon rounded-pill px-4 mt-3 fw-bold">Kembali ke Beranda</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-24 { border-radius: 24px !important; }
    .notification-item:hover { background-color: #fafafa !important; }
    .bg-light { background-color: #fcfcfc !important; }
</style>

<style>
    .border-transparent { border-color: transparent !important; }
    .w-15px { width: 20px; }
</style>
@endsection
