@extends('layouts.app')
@section('title', $category->name . ' - Telcopedia')

@push('styles')
<style>
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
<div class="container my-4">


    {{-- Product Grid --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Tersedia {{ $products->total() }} Barang</h5>
        <div class="breadcrumb text-muted mb-0 small">
            <a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a> 
            <span class="mx-2">/</span>
            <a href="{{ route('category.index') }}" class="text-decoration-none text-muted">Kategori</a>
            <span class="mx-2">/</span>
            <span class="text-danger fw-bold">{{ $category->name }}</span>
        </div>
    </div>

    <div class="row g-3">
        @forelse($products as $p)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card-premium">
                    <div class="pc-img-wrapper">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}">
                        <span class="pc-badge shadow-sm">{{ $category->name }}</span>
                    </div>
                    <div class="pc-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-light text-muted" style="font-size: 0.6rem;">{{ strtoupper($p->condition) }}</span>
                            @if(optional($p->reviews)->count() > 0)
                                <div class="text-warning" style="font-size: 0.7rem;"><i class="fa fa-star me-1"></i>{{ number_format($p->reviews->avg('rating'), 1) }}</div>
                            @endif
                        </div>
                        <h6 class="pc-title">{{ $p->name }}</h6>
                        <div class="pc-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div class="d-flex align-items-center overflow-hidden" style="max-width: 60%;">
                                <img src="{{ optional($p->seller)->photo ? asset('storage/' . $p->seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode(optional($p->seller)->name ?? 'S').'&background=F8F9FA&color=9F1521' }}" class="rounded-circle me-1" style="object-fit: cover;" width="20" height="20">
                                <span class="text-dark fw-bold text-truncate" style="font-size: 0.7rem;">{{ explode(' ', optional($p->seller)->name ?? 'Seller')[0] }}</span>
                            </div>
                            <div class="d-flex gap-1">
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
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fa-solid fa-face-frown fa-4x text-muted opacity-25"></i>
                </div>
                <h5 class="fw-bold">Yah, Belum Ada Barang...</h5>
                <p class="text-muted">Jadilah mahasiswa pertama yang menjual barang di kategori <strong>{{ $category->name }}</strong>!</p>
                <a href="{{ route('home') }}" class="btn btn-danger rounded-pill px-4 mt-2">Cari di Kategori Lain</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>
</div>
@endsection
