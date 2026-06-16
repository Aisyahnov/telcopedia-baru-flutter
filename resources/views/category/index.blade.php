@extends('layouts.app')
@section('title', 'Eksplorasi Kategori - Telcopedia')

@push('styles')
<style>
    .cat-nav-wrapper {
        display: flex;
        overflow-x: auto;
        padding: 10px 0;
        gap: 12px;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .cat-nav-wrapper::-webkit-scrollbar { display: none; }
    
    .cat-btn {
        white-space: nowrap;
        padding: 12px 24px;
        border-radius: 100px;
        background: #fff;
        border: 1px solid #eee;
        color: #555;
        font-weight: 700;
        font-size: 0.9rem;
        transition: 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cat-btn i { color: #9F1521; transition: 0.3s; }
    .cat-btn:hover { border-color: #9F1521; color: #9F1521; transform: translateY(-2px); }
    .cat-btn.active { background: #9F1521; color: #fff; border-color: #9F1521; box-shadow: 0 8px 20px rgba(159, 21, 33, 0.2); }
    .cat-btn.active i { color: #fff; }

    /* Product Card Improvements */
    .product-card-premium {
        background: white;
        border-radius: 20px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        transition: 0.3s all;
        height: 100%;
        animation: fadeIn 0.5s ease backwards;
        position: relative; /* Penting untuk stretched-link */
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
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

    .loading-overlay {
        display: none;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.7);
        z-index: 10;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-900">Eksplorasi <span class="text-maroon">Kategori</span></h2>
        <p class="text-muted">Pilih kategori untuk melihat produk terbaik.</p>
    </div>

    <!-- Category Nav -->
    <div class="cat-nav-wrapper mb-3 pb-2">
        <button class="cat-btn {{ !$firstCategory ? 'active' : '' }}" 
                onclick="loadCategory('all', this)">
            <i class="fa-solid fa-border-all"></i>
            Semua Kategori
        </button>
        @foreach($categories as $index => $cat)
            <button class="cat-btn {{ $firstCategory && $firstCategory->id == $cat->id ? 'active' : '' }}" 
                    onclick="loadCategory({{ $cat->id }}, this)">
                <i class="fa-solid {{ $cat->icon }}"></i>
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    <!-- Sub-Category Nav (Dynamic) -->
    <div id="subcat-container" class="d-flex flex-wrap gap-2 mb-5">
        @if($firstCategory && $firstCategory->subcategories->count() > 0)
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 active" onclick="loadSubCategory({{ $firstCategory->id }}, this)">Semua</button>
            @foreach($firstCategory->subcategories as $sub)
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="loadSubCategory({{ $sub->id }}, this)">{{ $sub->name }}</button>
            @endforeach
        @endif
    </div>

    <!-- Product Grid Section -->
    <div class="position-relative">
        <div id="loading" class="loading-overlay">
            <div class="spinner-border text-maroon" role="status"></div>
        </div>
        
        <div class="row g-4" id="product-grid">
            @forelse($products as $p)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card-premium">
                        <div class="pc-img-wrapper">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                            <span class="pc-badge shadow-sm">{{ optional($p->category)->name }}</span>
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
                                <div class="d-flex align-items-center overflow-hidden" style="max-width: 60%;">
                                    <img src="{{ optional($p->seller)->photo ? asset('storage/' . $p->seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode(optional($p->seller)->name ?? 'S').'&background=F8F9FA&color=9F1521' }}" class="rounded-circle me-1" style="object-fit: cover;" width="20" height="20">
                                    <span class="text-dark fw-bold text-truncate" style="font-size: 0.7rem;">{{ explode(' ', optional($p->seller)->name ?? 'Seller')[0] }}</span>
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
                    <p class="text-muted">Belum ada produk di kategori ini.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Show More Button -->
        <div class="text-center mt-5 {{ $products->count() < 12 ? 'd-none' : '' }}" id="more-container">
            <a href="{{ $firstCategory ? route('home', ['category_id' => $firstCategory->id]) : '#' }}" id="btn-show-more" class="btn btn-outline-maroon px-5 rounded-pill fw-bold">
                Tampilkan Produk Lebih Banyak <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function loadCategory(catId, btn) {
        // Toggle active class
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const grid = document.getElementById('product-grid');
        const loading = document.getElementById('loading');
        const moreContainer = document.getElementById('more-container');
        const btnMore = document.getElementById('btn-show-more');
        const subcatContainer = document.getElementById('subcat-container');

        loading.style.display = 'flex';
        
        try {
            // Kita ambil data kategori ini dari API
            const response = await fetch(`/categories/products/${catId}`);
            const data = await response.json();
            
            // Update Sub-kategori chips
            const catData = @json($categories).find(c => c.id == catId);
            if (catData && catData.subcategories.length > 0) {
                subcatContainer.innerHTML = `<button class="btn btn-sm btn-outline-secondary rounded-pill px-3 active" onclick="loadSubCategory(${catId}, this)">Semua</button>`;
                catData.subcategories.forEach(sub => {
                    subcatContainer.innerHTML += `<button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="loadSubCategory(${sub.id}, this)">${sub.name}</button>`;
                });
            } else {
                subcatContainer.innerHTML = '';
            }

            grid.innerHTML = '';
            
            if (data.products.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">Belum ada produk di kategori ini.</p></div>';
                moreContainer.classList.add('d-none');
            } else {
                data.products.forEach(p => {
                    const card = createProductCard(p);
                    grid.appendChild(card);
                });

                if (data.has_more) {
                    moreContainer.classList.remove('d-none');
                    btnMore.href = `/?category_id=${catId}`;
                } else {
                    moreContainer.classList.add('d-none');
                }
            }
        } catch (error) {
            console.error('Error loading category:', error);
        } finally {
            loading.style.display = 'none';
        }
    }

    async function loadSubCategory(subId, btn) {
        // Toggle active class
        document.querySelectorAll('#subcat-container button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const grid = document.getElementById('product-grid');
        const loading = document.getElementById('loading');
        const moreContainer = document.getElementById('more-container');
        
        loading.style.display = 'flex';
        
        try {
            const response = await fetch(`/categories/products/${subId}`);
            const data = await response.json();
            
            grid.innerHTML = '';
            
            if (data.products.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">Belum ada produk di kategori ini.</p></div>';
                moreContainer.classList.add('d-none');
            } else {
                data.products.forEach(p => {
                    const card = createProductCard(p);
                    grid.appendChild(card);
                });

                if (data.has_more) {
                    moreContainer.classList.remove('d-none');
                    document.getElementById('btn-show-more').href = `/?category_id=${subId}`;
                } else {
                    moreContainer.classList.add('d-none');
                }
            }
        } catch (error) {
            console.error('Error loading sub-category:', error);
        } finally {
            loading.style.display = 'none';
        }
    }

    function createProductCard(p) {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-lg-3';
        
        const price = new Intl.NumberFormat('id-ID').format(p.price);
        const sellerName = p.seller ? p.seller.name.split(' ')[0] : 'Seller';
        const ratingHtml = p.reviews && p.reviews.length > 0 
            ? `<div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>5.0</div>` 
            : '';

        col.innerHTML = `
            <div class="product-card-premium">
                <div class="pc-img-wrapper">
                    <img src="${p.image_url}" alt="${p.name}">
                    <span class="pc-badge shadow-sm">${p.category ? p.category.name : 'Umum'}</span>
                </div>
                <div class="pc-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-light text-muted" style="font-size: 0.6rem;">${p.condition.toUpperCase()}</span>
                        ${ratingHtml}
                    </div>
                    <h6 class="pc-title">
                        <a href="/product/${p.id}" class="text-decoration-none text-dark stretched-link">${p.name}</a>
                    </h6>
                    <div class="pc-price">Rp ${price}</div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <div class="d-flex align-items-center overflow-hidden" style="max-width: 60%;">
                            <img src="${p.seller && p.seller.photo ? '/storage/' + p.seller.photo : 'https://ui-avatars.com/api/?name='+encodeURIComponent(sellerName)+'&background=F8F9FA&color=9F1521'}" class="rounded-circle me-1" style="object-fit: cover;" width="20" height="20">
                            <span class="text-dark fw-bold text-truncate" style="font-size: 0.7rem;">${sellerName}</span>
                        </div>
                        <div class="d-flex gap-1" style="position: relative; z-index: 2;">
                            <a href="/product/${p.id}" class="btn btn-light btn-sm rounded-circle border p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-eye" style="font-size: 0.6rem;"></i>
                            </a>
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="${p.id}">
                                <button class="btn btn-maroon btn-sm rounded-circle p-0" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-cart-plus" style="font-size: 0.6rem;"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return col;
    }
</script>
@endpush
@endsection
