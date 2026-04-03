@extends('layouts.app')
@section('title', 'Eksplorasi Kategori - Telcopedia')

@push('styles')
<style>
    .category-explorer-card { border-radius: 18px; border: none; transition: 0.3s; background: #fff; overflow: hidden; }
    .category-explorer-card:hover { transform: translateY(-7px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
    .category-icon-box { width: 80px; height: 80px; border-radius: 20px; background: #fef2f2; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; transition: 0.3s; }
    .category-explorer-card:hover .category-icon-box { background: #9F1521; color: #fff; }
    .category-icon-box i { font-size: 2.2rem; color: #9F1521; transition: 0.3s; }
    .category-explorer-card:hover .category-icon-box i { color: #fff; }
    .count-badge { background: #f8f9fa; color: #6c757d; font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; border: 1px solid #eee; }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Telusuri Kategori</h2>
        <p class="text-muted">Temukan berbagai barang preloved berkualitas berdasarkan hobimu.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach($categories as $cat)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('category.show', $cat->slug) }}" class="text-decoration-none">
                    <div class="card category-explorer-card h-100 p-4 text-center shadow-sm">
                        <div class="category-icon-box shadow-sm">
                            <i class="fa-solid {{ $cat->icon }}"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">{{ $cat->name }}</h6>
                        <div>
                            <span class="count-badge">{{ $cat->products_count }} Barang</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Banner CTA --}}
    <div class="mt-5 p-5 bg-danger bg-opacity-10 rounded-4 text-center border border-danger-subtle">
        <h4 class="fw-bold">Tidak menemukan kategori yang dicari?</h4>
        <p class="text-muted mb-4 px-lg-5">Kategori kami akan terus bertambah seiring banyaknya mahasiswa yang berjualan di Telcopedia.</p>
        <a href="{{ route('home') }}" class="btn btn-danger px-4 rounded-pill fw-bold shadow-sm">Lihat Semua Produk</a>
    </div>
</div>
@endsection
