@extends('layouts.app')
@section('title', 'Pesan Masuk - Telcopedia')

@if(Auth::user()->role !== 'buyer')
    @section('hero_title', 'Kotak Masuk Chat')
    @section('hero_subtitle', 'Komunikasi langsung dengan calon pembeli barang Anda.')
    @section('hero_emoji', '')
@endif

@section('content')
    <div class="{{ Auth::user()->role !== 'buyer' ? '' : 'container py-5' }}" style="{{ Auth::user()->role === 'buyer' ? 'max-width: 800px;' : '' }}">
        @if(Auth::user()->role === 'buyer')
            <div class="d-flex align-items-center mb-4">
                <h4 class="fw-bold mb-0">Kotak Masuk (Chat)</h4>
                <span class="badge bg-danger ms-3 rounded-pill px-3">{{ $chats->count() }} Percakapan</span>
            </div>
        @endif

        @if($chats->isEmpty())
            <div class="card border-0 shadow-sm p-5 text-center rounded-4">
                <div class="card-body py-5">
                    <i class="fa fa-comments fa-4x text-muted opacity-25 mb-4"></i>
                    <h5 class="fw-bold mb-3">Belum ada obrolan.</h5>
                    <p class="text-muted mb-4">Tanyakan kondisi barang langsung ke penjual dengan fitur chat.</p>
                    <a href="{{ route('home') }}" class="btn btn-danger px-5 rounded-pill shadow-sm">Mulai Belanja</a>
                </div>
            </div>
        @else
            <div class="card card-management shadow-sm border-0">
                <div class="list-group list-group-flush">
                    @foreach($chats as $chat)
                    @php 
                        $partner = $chat->user1_id == auth()->id() ? $chat->user2 : $chat->user1; 
                        $lastMessage = $chat->messages->last();
                    @endphp
                    <a href="{{ route('chat.room', $chat->id) }}" class="list-group-item list-group-item-action p-4 border-bottom bg-white d-flex align-items-center transition-all hover-translate-x">
                        
                        <!-- Avatar Area -->
                        <div class="flex-shrink-0">
                            <div class="bg-maroon-soft rounded-circle d-flex align-items-center justify-content-center text-maroon shadow-sm border" style="width: 60px; height: 60px;">
                                <i class="fa fa-user fa-2x opacity-50"></i>
                            </div>
                        </div>
                        
                        <!-- Chat Preview -->
                        <div class="flex-grow-1 ms-4 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">{{ $partner->name ?? 'Pengguna Telcopedia' }}</h6>
                                <span class="x-small text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <p class="text-muted small mb-0 text-truncate flex-grow-1">
                                    @if($lastMessage)
                                        @if($lastMessage->sender_id == auth()->id())
                                            <i class="fa-solid fa-check-double text-primary me-1" style="font-size: 0.7rem;"></i>
                                        @endif
                                        {{ $lastMessage->message }}
                                    @else
                                        Mulai obrolan sekarang...
                                    @endif
                                </p>
                                @if($lastMessage && !$lastMessage->is_read && $lastMessage->sender_id != auth()->id())
                                    <span class="badge bg-danger rounded-pill ms-2" style="font-size: 0.6rem; padding: 5px 8px;">BARU</span>
                                @endif
                            </div>
                        </div>

                        <div class="ms-3 text-muted opacity-25 d-none d-md-block">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
