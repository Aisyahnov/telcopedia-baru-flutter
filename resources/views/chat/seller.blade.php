@extends('layouts.app')

@section('title', 'Chat Seller - Telcopedia')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Sidebar: Product List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-20 overflow-hidden">
                <div class="card-header bg-maroon text-white p-3 border-0">
                    <h6 class="mb-0 fw-bold"><i class="fa fa-boxes me-2"></i> Chat per Produk</h6>
                </div>
                <div class="list-group list-group-flush" id="product-list" style="max-height: 70vh; overflow-y: auto;">
                    @forelse($groups as $group)
                        <div class="list-group-item p-3 border-bottom border-light product-group-item" data-product-id="{{ $group['product']->id }}">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('storage/' . $group['product']->image) }}" class="rounded-10 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">{{ $group['product']->name }}</div>
                                    <div class="x-small text-muted">{{ count($group['chats']) }} Calon Pembeli</div>
                                </div>
                                <i class="fa fa-chevron-down text-muted small"></i>
                            </div>
                            
                            <!-- Buyer List for this product -->
                            <div class="buyer-list mt-2" id="buyer-list-{{ $group['product']->id }}" style="display: none;">
                                @foreach($group['chats'] as $chat)
                                    <a href="{{ route('chat.room', $chat['id']) }}" class="d-flex align-items-center p-2 rounded-15 bg-light mb-2 text-decoration-none hover-shadow transition-all">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($chat['other_user']->name) }}&background=random" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="small fw-bold text-dark text-truncate">{{ $chat['other_user']->name }}</div>
                                            <div class="x-small text-muted text-truncate">{{ $chat['last_message'] ? $chat['last_message']->message : 'Belum ada pesan' }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fa fa-comments fa-3x mb-3 opacity-25"></i>
                            <p class="small">Belum ada chat masuk untuk produk Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chat Room Placeholder -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-20 bg-white d-flex align-items-center justify-content-center" style="min-height: 70vh;">
                <div class="text-center p-5">
                    <div class="mb-4 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fa fa-comment-dots fa-3x text-maroon opacity-50"></i>
                    </div>
                    <h5 class="fw-bold">Pilih Pembeli</h5>
                    <p class="text-muted small mx-auto" style="max-width: 300px;">
                        Pilih produk di samping, lalu pilih pembeli yang ingin Anda balas pesannya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-maroon { background-color: #9F1521; }
    .text-maroon { color: #9F1521; }
    .rounded-20 { border-radius: 20px; }
    .rounded-15 { border-radius: 15px; }
    .rounded-10 { border-radius: 10px; }
    .x-small { font-size: 0.75rem; }
    .product-group-item { cursor: pointer; transition: background 0.2s; }
    .product-group-item:hover { background-color: #f8f9fa; }
    .transition-all { transition: all 0.2s ease; }
    .hover-shadow:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
</style>

@push('scripts')
<script>
    document.querySelectorAll('.product-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // If clicking an <a> (buyer), don't toggle the list
            if (e.target.closest('a')) return;

            const productId = this.getAttribute('data-product-id');
            const buyerList = document.getElementById('buyer-list-' + productId);
            const icon = this.querySelector('.fa-chevron-down');
            
            if (buyerList.style.display === 'none') {
                buyerList.style.display = 'block';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                buyerList.style.display = 'none';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    });
</script>
@endpush
@endsection
