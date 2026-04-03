@extends('layouts.app')
@section('title', 'Pesan Masuk - Telcopedia')

@section('content')
<div class="container my-5" style="max-width: 800px;">
    
    <div class="d-flex align-items-center mb-4">
        <h4 class="fw-bold mb-0">Kotak Masuk (Chat)</h4>
        <span class="badge bg-danger ms-3 rounded-pill px-3">{{ $chats->count() }} Percakapan</span>
    </div>

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
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="list-group list-group-flush">
                @foreach($chats as $chat)
                @php 
                    $partner = $chat->user1_id == auth()->id() ? $chat->user2 : $chat->user1; 
                    $lastMessage = $chat->messages->last();
                @endphp
                <a href="{{ route('chat.room', $chat->id) }}" class="list-group-item list-group-item-action p-4 border-bottom bg-white d-flex align-items-center">
                    
                    <!-- Avatar Area -->
                    <div class="flex-shrink-0">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-muted shadow-sm" style="width: 60px; height: 60px;">
                            <i class="fa fa-user fa-2x opacity-50"></i>
                        </div>
                    </div>
                    
                    <!-- Chat Preview -->
                    <div class="flex-grow-1 ms-4 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $partner->name ?? 'Pengguna Telcopedia' }}</h6>
                            <span class="small text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <p class="text-muted small mb-0 text-truncate" style="max-width: 90%;">
                            {{ $lastMessage ? ($lastMessage->sender_id == auth()->id() ? 'Anda: ' : '') . $lastMessage->message : 'Mulai obrolan sekarang...' }}
                        </p>
                    </div>

                    <!-- Indikator Belum Dibaca -->
                    @if($lastMessage && !$lastMessage->is_read && $lastMessage->sender_id != auth()->id())
                        <div class="ms-3">
                            <span class="badge bg-danger rounded-circle p-2 border border-light border-2" style="width: 15px; height: 15px;"></span>
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
