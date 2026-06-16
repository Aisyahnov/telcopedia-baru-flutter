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
                    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        {{ $seller->name }}
                        @if($seller->is_verified)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill" style="font-size: 0.65rem; padding: 6px 10px;">
                                <i class="fa fa-shield-check me-1"></i> Identitas Terverifikasi
                            </span>
                        @endif
                    </h4>
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
                                <span class="fw-bold text-maroon">{{ \Carbon\Carbon::parse($seller->created_at)->locale('id')->translatedFormat('F Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="store-stat-item">
                            <i class="fa-solid fa-star store-stat-icon"></i>
                            <div>
                                <span class="d-block small text-muted">Penilaian</span>
                                <span class="fw-bold {{ $avgSellerRating ? 'text-maroon' : 'text-muted opacity-75' }}">{{ $avgSellerRating ? number_format($avgSellerRating, 1) : 'Belum ada' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Navigation -->
    <ul class="nav nav-store d-flex" id="storeTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-content" type="button" role="tab">Semua Produk</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-content" type="button" role="tab">Ulasan Seller ({{ $reviews->total() }})</button>
        </li>
    </ul>

    <div class="tab-content" id="storeTabContent">
        <!-- Product Grid Tab -->
        <div class="tab-pane fade show active" id="products-content" role="tabpanel">
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

            <!-- Pagination Products -->
            <div class="d-flex justify-content-center mb-5">
                {{ $products->appends(['r_page' => $reviews->currentPage()])->links() }}
            </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews-content" role="tabpanel">
            <div class="row mb-5">
                <div class="col-lg-8">
                    @forelse($reviews as $review)
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=f8f9fa&color=9F1521&bold=true" class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $review->user->name }}</h6>
                                        <div class="text-info small">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fa-solid fa-star {{ $i <= $review->seller_rating ? 'text-info' : 'text-muted opacity-25' }}" style="font-size: 0.7rem;"></i>
                                            @endfor
                                            <span class="ms-1 fw-bold">{{ $review->seller_rating }}.0</span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 text-dark" style="font-size: 0.9rem;">{{ $review->seller_comment ?? 'Tidak ada komentar.' }}</p>
                            @if($review->product)
                                <div class="bg-light p-2 rounded-3 d-inline-flex align-items-center mt-2 border">
                                    <img src="{{ $review->product->image_url }}" class="rounded me-2" width="25" height="25" style="object-fit: cover;">
                                    <span class="x-small text-muted">Membeli: <strong>{{ $review->product->name }}</strong></span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fa-solid fa-comment-slash fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Belum ada ulasan untuk seller ini.</p>
                        </div>
                    @endforelse

                    <!-- Pagination Reviews -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reviews->appends(['p_page' => $products->currentPage()])->links() }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-maroon text-white sticky-top" style="top: 20px;">
                        <h6 class="fw-bold mb-3">Ringkasan Seller</h6>
                        <div class="display-4 fw-bold mb-1">{{ number_format($avgSellerRating, 1) }}</div>
                        <div class="mb-4">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa-solid fa-star {{ $i <= round($avgSellerRating) ? 'text-warning' : 'text-white-50' }}"></i>
                            @endfor
                        </div>
                        <p class="small mb-0 opacity-75">Rating ini berdasarkan akumulasi dari seluruh pembeli yang memberikan ulasan layanan kepada seller ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
