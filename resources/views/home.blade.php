@extends('layouts.app')
@section('title', 'Telcopedia - Platform Jual Beli Mahasiswa Telkom')

@push('styles')
<style>
    /* PREMIUM HOME STYLES */
    body { background-color: #fcfcfc; }

    /* Hero Section - Compact & Integrated */
    .hero-section { position: relative; padding: 20px 0 10px; }
    .hero-card-custom {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .hero-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--telco-maroon-soft);
        color: var(--telco-maroon);
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.6rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 15px;
    }

    /* USP Style - Plain & Clean (Matching User Image) */
    .usp-icon {
        color: var(--telco-maroon);
        font-size: 2.2rem;
        margin-bottom: 20px;
        display: block;
    }
    .usp-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 12px;
    }
    .usp-desc {
        font-size: 0.85rem;
        color: #888;
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Product Card Improvements */
    .product-card-premium {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        transition: 0.3s all;
        height: 100%;
        position: relative; /* Penting untuk stretched-link */
    }
    .product-card-premium:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
    .pc-img-wrapper { position: relative; height: 200px; background: #f8f8f8; overflow: hidden; }
    .pc-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .pc-badge {
        position: absolute; top: 12px; right: 12px;
        background: rgba(255,255,255,0.9); backdrop-filter: blur(5px);
        padding: 4px 10px; border-radius: 100px; font-weight: 800; font-size: 0.6rem; color: var(--telco-maroon);
    }
    .pc-body { padding: 15px; }
    .pc-title { font-size: 0.9rem; font-weight: 700; color: #222; margin-bottom: 6px; height: 2.6em; overflow: hidden; }
    .pc-price { font-size: 1.1rem; font-weight: 800; color: var(--telco-maroon); margin-bottom: 12px; }
    
    /* Testimonials Premium */
    .testimonial-card {
        background: white;
        border-radius: 24px;
        padding: 40px 30px 30px;
        border: 1px solid #f0f0f0;
        transition: 0.3s all;
        height: 100%;
        margin-top: 40px;
    }
    .testimonial-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); border-color: var(--telco-maroon-soft); }
    .testi-img {
        width: 80px; height: 80px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: absolute;
        top: -40px;
        left: 50%;
        transform: translateX(-50%);
        object-fit: cover;
    }

    /* Animations */
    .fade-up { animation: fadeUp 0.6s ease backwards; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    #heroCarousel .carousel-control-prev, #heroCarousel .carousel-control-next {
        width: 40px; height: 40px; background: white; border-radius: 50%; top: 50%; transform: translateY(-50%);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); opacity: 0; transition: 0.3s all; border: none;
    }
    #heroCarousel:hover .carousel-control-prev { opacity: 1; left: 10px; }
    #heroCarousel:hover .carousel-control-next { opacity: 1; right: 10px; }
</style>
@endpush

@section('content')

<!-- HERO SECTION -->
<div class="hero-section">
    <div class="container">
        <div id="heroCarousel" class="carousel slide hero-card-custom bg-white" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row align-items-center g-0">
                        <div class="col-lg-6 p-4 ps-lg-5">
                            <div class="fade-up">
                                <span class="hero-badge">Telkom Marketplace</span>
                                <h2 class="fw-900 text-dark mb-2">Cari Barang Bekas <br><span class="text-maroon">Harga Teman.</span></h2>
                                <p class="text-muted small mb-4">Belanja aman dari kawan sendiri di ekosistem Telkom University.</p>
                                <div class="d-flex gap-2">
                                    <a href="#products" class="btn btn-maroon btn-sm px-4 shadow">Belanja</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img src="{{ asset('images/hero1.jpg') }}" class="w-100 object-fit-cover" style="height: 280px;" alt="Hero">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- USP SECTION (Exact Match from Image) -->
<div class="container py-5">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <i class="fa-solid fa-shield-halved usp-icon"></i>
            <h6 class="usp-title">Aman & Terpercaya</h6>
            <p class="usp-desc">Seluruh user terverifikasi dengan NIM Mahasiswa Telkom University.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-tag usp-icon"></i>
            <h6 class="usp-title">Harga Mahasiswa</h6>
            <p class="usp-desc">Cari barang preloved berkualitas dengan harga yang ramah di kantong.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-handshake usp-icon"></i>
            <h6 class="usp-title">Mudah & Cepat</h6>
            <p class="usp-desc">Transaksi bisa langsung COD di area kampus atau sekitaran Dayeuhkolot.</p>
        </div>
    </div>
</div>

<!-- MAIN CONTAINER -->
<div class="container py-4">
    @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
    <!-- REKOMENDASI SECTION -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h5 class="fw-800 text-dark mb-0">
                Rekomendasi Spesial <span class="text-maroon">Untukmu</span>
            </h5>
            <p class="text-muted small mb-0 mt-1">Berdasarkan aktivitas dan produk populer.</p>
        </div>
    </div>
    
    <div class="row g-3 mb-5">
        @foreach($recommendedProducts as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card-premium" style="border: 1px solid var(--telco-maroon-soft);">
                    <div class="pc-img-wrapper">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                        <span class="pc-badge"><i class="fa-solid fa-sparkles text-warning me-1"></i> {{ optional($p->category)->name }}</span>
                    </div>
                    <div class="pc-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-light text-muted" style="font-size: 0.6rem;">{{ strtoupper($p->condition) }}</span>
                            @if($p->reviews->count() > 0)
                                <div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>{{ number_format($p->reviews->avg('rating'), 1) }}</div>
                            @endif
                        </div>
                        <h6 class="pc-title">
                            <a href="{{ route('product.show', $p->id) }}" class="text-decoration-none text-dark stretched-link">{{ $p->name }}</a>
                        </h6>
                        <div class="pc-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($p->seller)->name ?? 'S') }}&background=F8F9FA&color=9F1521" class="rounded-circle me-1" width="20" height="20">
                                <span class="text-dark fw-bold" style="font-size: 0.7rem;">{{ explode(' ', optional($p->seller)->name ?? 'Seller')[0] }}</span>
                            </div>
                            <div class="d-flex gap-1" style="position: relative; z-index: 2;">
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button class="btn btn-maroon btn-sm rounded-circle p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-cart-plus" style="font-size: 0.6rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- PRODUCTS GRID -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h5 class="fw-800 text-dark mb-0">
                @if(request('keyword')) Hasil: <span class="text-maroon">"{{ request('keyword') }}"</span> @else Produk <span class="text-maroon">Terbaru</span> @endif
            </h5>
        </div>
    </div>

    <div class="row g-3">
        @forelse($products as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card-premium">
                    <div class="pc-img-wrapper">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                        <span class="pc-badge">{{ optional($p->category)->name }}</span>
                    </div>
                    <div class="pc-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-light text-muted" style="font-size: 0.6rem;">{{ strtoupper($p->condition) }}</span>
                            @if($p->reviews->count() > 0)
                                <div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>{{ number_format($p->reviews->avg('rating'), 1) }}</div>
                            @endif
                        </div>
                        <h6 class="pc-title">
                            <a href="{{ route('product.show', $p->id) }}" class="text-decoration-none text-dark stretched-link">{{ $p->name }}</a>
                        </h6>
                        <div class="pc-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($p->seller)->name ?? 'S') }}&background=F8F9FA&color=9F1521" class="rounded-circle me-1" width="20" height="20">
                                <span class="text-dark fw-bold" style="font-size: 0.7rem;">{{ explode(' ', optional($p->seller)->name ?? 'Seller')[0] }}</span>
                            </div>
                            <div class="d-flex gap-1" style="position: relative; z-index: 2;">
                                <a href="{{ route('product.show', $p->id) }}" class="btn btn-light btn-sm rounded-circle border p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-eye" style="font-size: 0.6rem;"></i>
                                </a>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button class="btn btn-maroon btn-sm rounded-circle p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-cart-plus" style="font-size: 0.6rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Produk tidak ditemukan.</p>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
</div>

<!-- TESTIMONIALS -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h4 class="fw-800 text-dark mb-1">Apa Kata <span class="text-maroon">Mereka?</span></h4>
        <p class="text-muted small">Cerita sukses transaksi di Telcopedia.</p>
    </div>
    <div class="row g-5">
        <div class="col-md-4">
            <div class="testimonial-card text-center position-relative">
                <img src="{{ asset('images/testi1.jpg') }}" class="testi-img" alt="User">
                <p class="text-muted fst-italic mb-4 mt-2">"Awalnya ragu beli barang bekas online, tapi di Telcopedia aman banget karena semua sellernya satu kampus. Barangnya juga masih mulus!"</p>
                <h6 class="fw-800 mb-1">Aisyah Noviani</h6>
                <span class="text-maroon x-small fw-bold">Mahasiswa FIK</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="testimonial-card text-center position-relative">
                <img src="{{ asset('images/testi3.jpg') }}" class="testi-img" alt="User">
                <p class="text-muted fst-italic mb-4 mt-2">"Cari buku referensi kuliah jadi lebih gampang dan murah. Fitur chatnya juga responsif buat janjian COD di GKU."</p>
                <h6 class="fw-800 mb-1">Siti Amany</h6>
                <span class="text-maroon x-small fw-bold">Mahasiswa FRI</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="testimonial-card text-center position-relative">
                <img src="{{ asset('images/testi2.jpg') }}" class="testi-img" alt="User">
                <p class="text-muted fst-italic mb-4 mt-2">"Jual meja lipat bekas kosan cuma butuh 2 hari langsung laku. Gak perlu ribet packing karena bisa langsung ketemuan."</p>
                <h6 class="fw-800 mb-1">Andi Bayu</h6>
                <span class="text-maroon x-small fw-bold">Mahasiswa FIF</span>
            </div>
        </div>
    </div>
</div>

<!-- SELLER CTA -->
<div class="container py-4">
    <div class="p-4 rounded-4 shadow-sm position-relative overflow-hidden" style="background: #1a1a1a;">
        <div class="row align-items-center position-relative">
            <div class="col-lg-9 text-center text-lg-start">
                <h5 class="fw-800 text-white mb-1">Punya barang yang tidak terpakai?</h5>
                <p class="text-white opacity-50 small mb-0">Ubah barang lama kamu menjadi uang tambahan sekarang!</p>
            </div>
            <div class="col-lg-3 text-center text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('register.form') }}" class="btn btn-maroon btn-sm px-4 rounded-pill py-2">Jual Sekarang</a>
            </div>
        </div>
    </div>
</div>

@endsection
