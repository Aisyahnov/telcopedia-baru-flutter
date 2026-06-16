@extends('layouts.app')
@section('title', $product->name . ' - Telcopedia')

@push('styles')
<style>
    body { background-color: #fcfcfc; }
    
    /* Breadcrumb Style */
    .breadcrumb-custom { font-size: 0.8rem; color: #888; margin-bottom: 25px; }
    .breadcrumb-custom a { color: #888; text-decoration: none; transition: 0.2s; }
    .breadcrumb-custom a:hover { color: var(--telco-maroon); }
    .breadcrumb-custom .active { color: var(--telco-maroon); font-weight: 700; }

    /* Product Detail Premium */
    .product-main-card { background: white; border-radius: 24px; border: 1px solid #f0f0f0; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
    .main-image-container { border-radius: 20px; overflow: hidden; background: #fff; border: 1px solid #f5f5f5; height: 450px; display: flex; align-items: center; justify-content: center; }
    
    .thumb-item { width: 70px; height: 70px; border-radius: 12px; border: 2px solid #f0f0f0; cursor: pointer; transition: 0.2s; overflow: hidden; }
    .thumb-item:hover, .thumb-item.active { border-color: var(--telco-maroon); transform: scale(1.05); }

    .price-tag { font-size: 2rem; font-weight: 900; color: var(--telco-maroon); margin: 15px 0; letter-spacing: -1px; }
    
    .badge-condition { padding: 6px 16px; font-size: 0.7rem; font-weight: 800; border-radius: 100px; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-new { background: #e3f2fd; color: #1976d2; }
    .badge-used { background: #fff3e0; color: #e65100; }

    .seller-profile-card { background: #fafafa; border-radius: 20px; padding: 20px; border: 1px solid #f0f0f0; transition: 0.3s all; text-decoration: none !important; display: block; }
    .seller-profile-card:hover { background: #fff; border-color: var(--telco-maroon-soft); box-shadow: 0 10px 20px rgba(0,0,0,0.03); }

    /* Buy Box */
    .buy-box-premium { background: white; border-radius: 24px; border: 1px solid #eee; padding: 25px; position: sticky; top: 100px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
    
    /* Product Card (for recommendations) */
    .product-card-premium {
        background: white; border-radius: 20px; border: 1px solid #f0f0f0; overflow: hidden; transition: 0.3s all; height: 100%;
    }
    .product-card-premium:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
    .pc-img-wrapper { position: relative; height: 180px; background: #f8f8f8; overflow: hidden; }
    .pc-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .pc-body { padding: 15px; }

    /* Read More Description */
    .product-desc-container {
        position: relative;
        max-height: 120px;
        overflow: hidden;
        transition: max-height 0.4s ease;
    }
    .product-desc-container.expanded {
        max-height: 2000px;
    }
    .product-desc-gradient {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background: linear-gradient(to bottom, rgba(252, 252, 252, 0) 0%, rgba(252, 252, 252, 1) 100%);
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .product-desc-container.expanded .product-desc-gradient {
        opacity: 0;
    }
    .btn-read-more {
        font-weight: 800;
        color: var(--telco-maroon);
        cursor: pointer;
        font-size: 0.9rem;
        display: inline-block;
        margin-top: 5px;
    }
    .btn-read-more:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')

<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav class="breadcrumb-custom">
        <a href="{{ route('home') }}">Home</a> <i class="fa fa-chevron-right mx-2 small opacity-50"></i>
        <a href="{{ route('category.index') }}">Semua Kategori</a> <i class="fa fa-chevron-right mx-2 small opacity-50"></i>
        <span class="active">{{ $product->name }}</span>
    </nav>

    <div class="row g-4">
        <!-- LEFT: IMAGES -->
        <div class="col-lg-5">
            <div class="product-main-card">
                <div class="main-image-container mb-4">
                    <img src="{{ $product->image_url }}" class="w-100 h-100 object-fit-contain p-3" id="mainImage">
                </div>
                
                @if($product->images->count() > 0)
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="thumb-item active" onclick="changeImage('{{ $product->image_url }}', this)">
                            <img src="{{ $product->image_url }}" class="w-100 h-100 object-fit-cover">
                        </div>
                        @foreach($product->images as $img)
                            <div class="thumb-item" onclick="changeImage('{{ asset('storage/' . $img->image_url) }}', this)">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="w-100 h-100 object-fit-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- CENTER: DETAILS -->
        <div class="col-lg-4">
            <div class="mb-2">
                <span class="badge-condition {{ strtolower($product->condition) == 'new' ? 'badge-new' : 'badge-used' }}">
                    {{ $product->condition }}
                </span>
                <span class="ms-2 text-muted small fw-bold"><i class="fa fa-layer-group me-1"></i>{{ $product->category->name }}</span>
            </div>
            
            <h1 class="fw-900 text-dark mb-2" style="font-size: 1.8rem; letter-spacing: -0.5px;">{{ $product->name }}</h1>
            
            @php
                $avgRating = round($product->reviews()->avg('rating'), 1);
                $reviewCount = $product->reviews()->count();
            @endphp
            
            <div class="d-flex align-items-center gap-2 mb-4">
                @if($reviewCount > 0)
                    <div class="text-warning fw-bold"><i class="fa fa-star me-1"></i>{{ $avgRating }}</div>
                    <div class="text-muted small">({{ $reviewCount }} Ulasan)</div>
                @else
                    <div class="text-muted small italic">Belum ada ulasan</div>
                @endif
                <div class="bg-light px-2 py-1 rounded small fw-bold text-dark ms-2">Preloved</div>
            </div>

            <div class="price-tag">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

            <hr class="opacity-10 my-4">

            <h6 class="fw-800 text-dark mb-2">Deskripsi Produk</h6>
            <div class="product-desc-wrapper mb-2">
                <div class="product-desc-container" id="descContainer">
                    <p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">
                        {{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}
                    </p>
                    <div class="product-desc-gradient" id="descGradient"></div>
                </div>
                <div class="btn-read-more" id="readMoreBtn" onclick="toggleDescription()">Baca Selengkapnya</div>
            </div>

            <hr class="opacity-10 my-4">

            <!-- Seller Profile Section -->
            <a href="{{ route('seller.profile', $product->seller_id) }}" class="seller-profile-card">
                <div class="d-flex align-items-center">
                    <img src="{{ optional($product->seller)->photo ? asset('storage/' . $product->seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode($product->seller->name ?? 'S').'&background=9F1521&color=fff&bold=true' }}" class="rounded-circle me-3" style="object-fit: cover;" width="55" height="55">
                    <div class="flex-grow-1">
                        <div class="fw-800 text-dark mb-0">
                            {{ $product->seller->name ?? 'Penjual' }}
                            @if(optional($product->seller)->is_verified)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill align-middle ms-1" style="font-size: 0.6rem; padding: 4px 8px;">
                                    <i class="fa fa-shield-check me-1"></i> Identitas Terverifikasi
                                </span>
                            @endif
                        </div>
                        <div class="text-muted x-small fw-bold">Online • Bergabung {{ $product->seller->created_at->format('M Y') }}</div>
                    </div>
                    <i class="fa fa-chevron-right text-muted small"></i>
                </div>
            </a>
        </div>

        <!-- RIGHT: ACTION BOX -->
        <div class="col-lg-3">
            <div class="buy-box-premium">
                <h6 class="fw-800 text-dark mb-4">Ringkasan Belanja</h6>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small fw-bold">Stok Produk</span>
                    <span class="text-dark fw-800">{{ $product->stock }} Item</span>
                </div>
                
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small fw-bold">Subtotal</span>
                        <span class="text-maroon fw-900" id="subtotalDisplay">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="mb-3">
                            <label class="x-small fw-800 text-muted text-uppercase mb-2 d-block">Atur Jumlah</label>
                            <div class="input-group input-group-sm mb-3">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="decrementQty()">-</button>
                                <input type="number" id="quantityInput" name="quantity" class="form-control text-center fw-bold border-x-0" value="1" min="1" max="{{ $product->stock }}" onchange="updateSubtotal()">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="incrementQty()">+</button>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-maroon py-2 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                                <i class="fa fa-cart-plus me-2"></i> Keranjang
                            </button>
                            <button type="button" class="btn btn-outline-maroon py-2 fw-bold rounded-3" onclick="buyNow()">
                                Beli Sekarang
                            </button>
                        </div>
                    </form>
                @else
                    <button class="btn btn-secondary w-100 py-3 fw-bold rounded-3 mb-2" disabled>Stok Habis</button>
                @endif

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('chat.start', $product->id) }}" class="btn btn-light btn-sm flex-grow-1 fw-bold border d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-comment me-1"></i> Chat
                    </a>
                    <form action="{{ route('favorite.toggle') }}" method="POST" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-sm w-100 fw-bold border d-flex align-items-center justify-content-center" style="{{ $isFavorited ? 'background: #fff5f5; border-color: #9F1521; color: #9F1521;' : 'background: #fff; color: #666;' }}">
                            <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-heart text-maroon me-1"></i> Wishlist
                        </button>
                    </form>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <p class="x-small fw-800 text-muted text-uppercase mb-2">Bagikan Produk</p>
                    <div class="d-flex gap-2">
                        <a href="https://wa.me/?text={{ urlencode('Cek barang keren ini di Telcopedia: ' . $product->name . ' - ' . url()->current()) }}" target="_blank" class="btn btn-light btn-sm border flex-grow-1 text-success">
                            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                        </a>
                        <button onclick="copyLink()" class="btn btn-light btn-sm border flex-grow-1 text-dark">
                            <i class="fa fa-link me-1"></i> Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REVIEWS SECTION -->
    <div class="row mt-5 pt-4">
        <div class="col-lg-9">
            <div class="d-flex align-items-center gap-3 mb-4">
                <h4 class="fw-800 text-dark mb-0">Ulasan Pembeli</h4>
                <div class="bg-maroon-soft text-maroon px-3 py-1 rounded-pill x-small fw-bold">{{ $reviewCount }} Review</div>
            </div>

            @php $reviews = $product->reviews()->with('user')->latest()->take(5)->get(); @endphp

            @forelse($reviews as $review)
                <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=F8F9FA&color=9F1521&bold=true" class="rounded-circle" width="45" height="45">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-800 text-dark mb-0">{{ $review->user->name }}</div>
                                <div class="text-warning x-small">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'opacity-20' }}"></i>
                                    @endfor
                                    <span class="ms-2 text-muted fw-normal">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">{{ $review->comment ?? 'Penjual tidak memberikan komentar teks.' }}</p>
                    </div>
                </div>
            @empty
                <div class="py-5 text-center bg-white rounded-4 border border-dashed">
                    <img src="https://illustrations.popsy.co/gray/creative-work.svg" width="120" class="mb-3 opacity-50">
                    <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- RECOMMENDATIONS SECTION -->
    @if($relatedProducts->count() > 0)
    <div class="mt-5 pt-5">
    <div class="mt-5 pt-4 border-top">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-900 text-dark mb-0">Rekomendasi <span class="text-maroon">Untuk Kamu</span></h5>
            <a href="{{ route('category.index') }}" class="text-maroon small fw-bold text-decoration-none">Lihat Semua Kategori <i class="fa fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach($relatedProducts as $rp)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card-premium" style="position: relative;">
                        <div class="pc-img-wrapper">
                            <img src="{{ $rp->image_url }}" alt="{{ $rp->name }}">
                        </div>
                        <div class="pc-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-muted" style="font-size: 0.6rem;">{{ strtoupper($rp->condition) }}</span>
                                @if($rp->reviews->count() > 0)
                                    <div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>{{ number_format($rp->reviews->avg('rating'), 1) }}</div>
                                @endif
                            </div>
                            <h6 class="pc-title" style="font-size: 0.85rem;">
                                <a href="{{ route('product.show', $rp->id) }}" class="text-decoration-none text-dark stretched-link">{{ $rp->name }}</a>
                            </h6>
                            <div class="text-maroon fw-900">Rp {{ number_format($rp->price, 0, ',', '.') }}</div>
                            
                            <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                                <div class="d-flex align-items-center">
                                    <img src="{{ optional($rp->seller)->photo ? asset('storage/' . $rp->seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode(optional($rp->seller)->name ?? 'S').'&background=F8F9FA&color=9F1521' }}" class="rounded-circle me-1" style="object-fit: cover;" width="20" height="20">
                                    <span class="text-dark fw-bold" style="font-size: 0.7rem;">{{ explode(' ', optional($rp->seller)->name ?? 'Seller')[0] }}</span>
                                </div>
                                <div style="position: relative; z-index: 2;">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $rp->id }}">
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
    </div>
    @endif
</div>

<script>
    const productPrice = {{ $product->price }};
    const maxStock = {{ $product->stock }};
    const qtyInput = document.getElementById('quantityInput');
    const subtotalDisplay = document.getElementById('subtotalDisplay');

    function changeImage(url, thumb) {
        document.getElementById('mainImage').src = url;
        document.querySelectorAll('.thumb-item').forEach(i => i.classList.remove('active'));
        thumb.classList.add('active');
    }

    function incrementQty() {
        let val = parseInt(qtyInput.value);
        if (val < maxStock) {
            qtyInput.value = val + 1;
            updateSubtotal();
        }
    }

    function decrementQty() {
        let val = parseInt(qtyInput.value);
        if (val > 1) {
            qtyInput.value = val - 1;
            updateSubtotal();
        }
    }

    function updateSubtotal() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        qtyInput.value = val;
        
        const total = val * productPrice;
        subtotalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function buyNow() {
        // Submit the form but maybe add a hidden input to indicate direct checkout
        const form = document.querySelector('form[action="{{ route('cart.add') }}"]');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'buy_now';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    }

    function copyLink() {
        navigator.clipboard.writeText(window.location.href);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Link produk berhasil disalin!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    function toggleDescription() {
        const container = document.getElementById('descContainer');
        const btn = document.getElementById('readMoreBtn');
        
        container.classList.toggle('expanded');
        if(container.classList.contains('expanded')) {
            btn.innerText = 'Tampilkan Lebih Sedikit';
        } else {
            btn.innerText = 'Baca Selengkapnya';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('descContainer');
        const btn = document.getElementById('readMoreBtn');
        const gradient = document.getElementById('descGradient');
        
        // Hide Read More if text is short
        if (container && container.scrollHeight <= 120) {
            btn.style.display = 'none';
            gradient.style.display = 'none';
            container.style.maxHeight = 'none';
        }
    });
</script>

@endsection
