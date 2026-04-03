@extends('layouts.app')
@section('title', $category->name . ' - Telcopedia')

@push('styles')
<style>
    .category-header { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1558655146-d09347e92766?q=80&w=1528&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 20px; min-height: 200px; display: flex; align-items: center; justify-content: center; }
    .product-card { border-radius: 12px; border: none; transition: 0.2s; background: #fff; overflow: hidden; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .btn-maroon { background: #9F1521; color: #fff; transition: 0.2s; border: none; }
    .btn-maroon:hover { background: #7c111b; color: #fff; transform: scale(1.05); }
    .category-description { max-width: 600px; margin: 0 auto; color: #f8f9fa; text-shadow: 1px 1px 4px rgba(0,0,0,0.3); }
</style>
@endpush

@section('content')
<div class="container my-4">
    {{-- Category Banner --}}
    <div class="category-header shadow-sm mb-5 text-center p-4">
        <div class="text-white">
            <div class="mb-3">
                <i class="fa-solid {{ $category->icon }} fa-3x mb-3 text-white"></i>
            </div>
            <h1 class="fw-bold display-5 mb-2">{{ $category->name }}</h1>
            @if($category->description)
                <p class="category-description small opacity-90">{{ $category->description }}</p>
            @else
                <p class="category-description small opacity-90">Jelajahi koleksi barang preloved berkualitas dalam kategori {{ $category->name }} khusus untuk mahasiswa Telkom.</p>
            @endif
        </div>
    </div>

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
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm">
                    <div class="bg-light w-100 position-relative" style="height:190px;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=f8f9fa&color=9F1521&size=200" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body p-3">
                        <h6 class="card-title fw-bold text-truncate mb-1">{{ $p->name }}</h6>
                        <p class="text-muted small mb-3">Oleh: {{ optional($p->seller)->name }}</p>
                        <p class="fw-bold text-danger mb-3">Rp {{ number_format($p->price,0,',','.') }}</p>
                        <div class="d-flex gap-2">
                          <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $p->id }}">
                                <button class="btn btn-sm btn-maroon w-100 rounded-pill py-2">Tambah</button>
                            </form>
                          <a href="{{ route('product.show', $p->id) }}" class="btn btn-sm btn-outline-secondary px-3 rounded-pill py-2">
                             Detail
                          </a>
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
