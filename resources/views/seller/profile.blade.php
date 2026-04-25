@extends('layouts.app')
@section('title', 'Toko ' . $seller->name . ' - Telcopedia')

@push('styles')
<style>
    .store-banner-container {
        background: url('https://www.transparenttextures.com/patterns/cubes.png'), linear-gradient(135deg, #9F1521 0%, #4a0910 100%);
        height: 200px;
        position: relative;
        border-radius: 0 0 20px 20px;
    }
    .store-info-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
        margin-top: -80px;
        position: relative;
        z-index: 10;
    }
    .store-avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .store-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .verified-badge {
        position: absolute;
        bottom: 5px;
        right: 0;
        background: #28a745;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        font-size: 10px;
    }
    .store-stat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #555;
    }
    .store-stat-icon {
        color: #9F1521;
        width: 20px;
        text-align: center;
    }
    
    .nav-store {
        border-bottom: 2px solid #eee;
        margin-top: 30px;
        margin-bottom: 25px;
    }
    .nav-store .nav-link {
        color: #555;
        font-weight: 600;
        padding: 12px 20px;
        border: none;
        position: relative;
    }
    .nav-store .nav-link.active {
        color: #9F1521;
        background: transparent;
    }
    .nav-store .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: #9F1521;
        border-radius: 3px 3px 0 0;
    }
    
    /* Product Card Improvements */
    .product-card-premium {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        transition: 0.3s all;
        height: 100%;
    }
    .product-card-premium:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
    .pc-img-wrapper { position: relative; height: 180px; background: #f8f8f8; overflow: hidden; }
    .pc-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .pc-badge {
        position: absolute; top: 12px; right: 12px;
        background: rgba(255,255,255,0.9); backdrop-filter: blur(5px);
        padding: 4px 10px; border-radius: 100px; font-weight: 800; font-size: 0.6rem; color: #9F1521;
    }
    .pc-body { padding: 15px; }
    .pc-title { font-size: 0.85rem; font-weight: 700; color: #222; margin-bottom: 6px; height: 2.6em; overflow: hidden; }
    .pc-price { font-size: 1.05rem; font-weight: 800; color: #9F1521; margin-bottom: 12px; }
</style>
@endpush

@section('content')
<!-- Store Banner -->
<div class="store-banner-container"></div>

<div class="container px-md-4">
    <!-- Store Info Card -->
    <div class="store-info-card">
        <div class="row align-items-center">
            <!-- Left: Avatar & Name -->
            <div class="col-md-5 d-flex align-items-center mb-4 mb-md-0 border-end-md pe-md-4">
                <div class="store-avatar-wrapper me-4">
                    <img src="{{ $seller->photo ? asset('storage/' . $seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode($seller->name).'&background=f0f0f0&color=9F1521&bold=true' }}" 
                         alt="{{ $seller->name }}" class="store-avatar">
                    @if($seller->is_verified)
                        <div class="verified-badge" title="Verified Mahasiswa">
                            <i class="fa fa-check"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ $seller->name }}</h4>
                    <p class="text-muted small mb-2"><i class="fa fa-circle text-success" style="font-size: 8px;"></i> Aktif 5 menit lalu</p>
                    <div class="d-flex gap-2 mt-2">
                        <!-- We don't have a direct chat with seller button passing seller id right now, but we can simulate or link to chat index -->
                        <a href="{{ route('chat.start_seller', $seller->id) }}" class="btn btn-outline-maroon btn-sm px-4 rounded-pill fw-bold"><i class="fa-regular fa-comment-dots me-1"></i> Chat Penjual</a>
                    </div>
                </div>
            </div>
            
            <!-- Right: Stats -->
            <div class="col-md-7 ps-md-5">
                <div class="row g-3">
                    <div class="col-6 col-sm-4">
                        <div class="store-stat-item">
                            <i class="fa-solid fa-box-open store-stat-icon"></i>
                            <div>
                                <span class="d-block small text-muted">Produk</span>
                                <span class="fw-bold text-maroon">{{ $products->total() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="store-stat-item">
                            <i class="fa-solid fa-user-check store-stat-icon"></i>
                            <div>
                                <span class="d-block small text-muted">Bergabung</span>
                                <span class="fw-bold text-maroon">{{ $seller->created_at->diffForHumans(null, true) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="store-stat-item">
                            <i class="fa-solid fa-star store-stat-icon"></i>
                            <div>
                                <span class="d-block small text-muted">Penilaian</span>
                                @php
                                    $rating = \App\Models\Review::whereHas('product', function($q) use($seller) {
                                        $q->where('seller_id', $seller->id);
                                    })->avg('rating');
                                @endphp
                                <span class="fw-bold text-maroon">{{ $rating ? number_format($rating, 1) : 'Belum ada' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Navigation -->
    <ul class="nav nav-store d-flex">
        <li class="nav-item">
            <a class="nav-link active" href="#">Semua Produk</a>
        </li>
    </ul>

    <!-- Product Grid -->
    <div class="row g-3 mb-5">
        @forelse($products as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card-premium" style="position: relative;">
                    <div class="pc-img-wrapper">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                        <span class="pc-badge shadow-sm">{{ optional($p->category)->name ?? 'Umum' }}</span>
                    </div>
                    <div class="pc-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-light text-muted" style="font-size: 0.6rem;">{{ strtoupper($p->condition) }}</span>
                            @if(optional($p->reviews)->count() > 0)
                                <div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>{{ number_format($p->reviews->avg('rating'), 1) }}</div>
                            @endif
                        </div>
                        <h6 class="pc-title">
                            <a href="{{ route('product.show', $p->id) }}" class="text-decoration-none text-dark stretched-link">{{ $p->name }}</a>
                        </h6>
                        <div class="pc-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-boxes-stacked text-muted me-1" style="font-size: 0.7rem;"></i>
                                <span class="text-muted fw-bold" style="font-size: 0.7rem;">Stok: {{ $p->stock }}</span>
                            </div>
                            <div class="d-flex gap-1" style="position: relative; z-index: 2;">
                                <a href="{{ route('product.show', $p->id) }}" class="btn btn-light btn-sm rounded-circle border p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;" title="Lihat">
                                    <i class="fa fa-eye" style="font-size: 0.6rem;"></i>
                                </a>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button class="btn btn-maroon btn-sm rounded-circle p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;" title="Tambah">
                                        <i class="fa fa-cart-plus" style="font-size: 0.6rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 my-5">
                <i class="fa-solid fa-store-slash fa-4x text-muted opacity-25 mb-4"></i>
                <h5 class="fw-bold">Toko Masih Kosong</h5>
                <p class="text-muted">Penjual ini belum menambahkan produk ke etalasenya.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mb-5">
        {{ $products->links() }}
    </div>

</div>
@endsection
