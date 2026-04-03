@extends('layouts.app')
@section('title', $product->name . ' - Telcopedia')

@push('styles')
<style>
    .product-img { width: 100%; border-radius: 12px; padding: 10px; background: #fff; border: 1px solid #ddd; object-fit: contain; }
    .badge-condition { padding: 5px 14px; font-size: 12px; font-weight: 600; border-radius: 20px; }
    .badge-good { background: #d6f5d6; color: #0c7a17; }
    .badge-new { background: #dce9ff; color: #003b8e; }
    .buy-box { background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 12px; }
    .btn-red { background: #9F1521; color: #fff; border-radius: 10px; font-weight: 600; }
    .btn-red:hover { background: #7c111b; }
    .seller-box { background: #fff; border: 1px solid #ddd; padding: 12px 15px; border-radius: 12px; }
</style>
@endpush

@section('content')

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="row g-4">
        <!-- LEFT: PRODUCT IMAGE -->
        <div class="col-md-5">
            <img src="{{ 'https://ui-avatars.com/api/?name='.urlencode($product->name).'&color=fff&background=9F1521&size=500' }}" 
                 class="product-img shadow-sm"
                 alt="{{ $product->name }}">
        </div>

        <!-- CENTER: PRODUCT INFO -->
        <div class="col-md-4">
            <h2 class="fw-bold">{{ $product->name }}</h2>
            <div class="mt-2 mb-3">
                <span class="badge-condition badge-good">Very Good</span>
                <span class="badge-condition badge-new">Pre-Loved</span>
            </div>
            <h3 class="fw-bold text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
            <p class="mt-3 text-secondary" style="line-height: 1.7;">
                {{ $product->description ?? 'No description available.' }}
            </p>

            <div class="seller-box d-flex align-items-center mt-4">
                <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px;">
                    <i class="fa fa-store"></i>
                </div>
                <div>
                    <strong>{{ $product->seller->name ?? 'Penjual' }}</strong><br>
                    <span class="text-muted small">Online • Trusted Seller</span>
                </div>
            </div>

            <div class="mt-4">
                <h6 class="fw-bold">Spesifikasi Detail</h6>
                <ul class="text-secondary small mt-2">
                    <li>Stok Gudang: {{ $product->stock }} Unit</li>
                    <li>Kondisi Barang: Sesuai Deskripsi</li>
                    <li>Kategori Katalog: {{ $product->category->name ?? '-' }}</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT: BUY BOX -->
        <div class="col-md-3">
            <div class="buy-box shadow-sm position-sticky" style="top: 20px;">
                <h6 class="fw-bold mb-3">Buy / Add to Cart</h6>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Stock Tersedia</span>
                    <span class="fw-bold text-dark">{{ $product->stock }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2 mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Harga Satuan</span>
                    <span class="fw-bold text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                @if($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <label class="fw-semibold text-muted small d-block mb-1">Jumlah</label>
                        <input type="number" min="1" max="{{ $product->stock }}" value="1" name="quantity" class="form-control mb-3">
                        <button class="btn btn-red w-100 mb-2 py-2"><i class="fa fa-cart-plus me-1"></i> Add to Cart</button>
                    </form>
                @else
                    <button class="btn btn-secondary w-100 mt-2 mb-2 py-2" disabled>Stok Habis</button>
                @endif

                <div class="d-flex justify-content-between mt-3 px-2">
                    <a href="{{ route('chat.start', $product->id) }}" class="text-decoration-none text-muted small fw-bold">
                        <i class="fa-regular fa-comment text-primary"></i> Hubungi Penjual
                    </a>
                    <form action="{{ route('favorite.toggle') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" style="background:none;border:none;padding:0;color:inherit;" class="text-muted small fw-bold">
                            <i class="fa-regular fa-heart text-danger"></i> Wishlist
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
