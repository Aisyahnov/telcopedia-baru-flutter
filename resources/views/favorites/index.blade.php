@extends('layouts.app')
@section('title', 'Daftar Keinginan (Wishlist) - Telcopedia')

@section('content')
<div class="container my-5">
    
    <div class="d-flex align-items-center mb-5">
        <h4 class="fw-bold mb-0">❤️ Daftar Keinginan Anda</h4>
    </div>

    @if($favorites->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
            <div class="card-body py-5">
                <i class="fa fa-heart-crack fa-4x text-muted opacity-25 mb-4"></i>
                <h5 class="fw-bold mb-3">Belum ada produk favorit.</h5>
                <p class="text-muted mb-4">Cari barang bekas yang menarik perhatianmu dan klik ikon hati untuk menyimpannya di sini.</p>
                <a href="{{ route('home') }}" class="btn btn-danger px-5 rounded-pill shadow-sm">Jelajahi Produk</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($favorites as $fav)
            @php $p = $fav->product; @endphp
            @if($p) <!-- Pastikan product belum dihapus -->
            <div class="col-6 col-md-3">
                <div class="card product-card h-100 border-0 shadow-sm bg-white position-relative">
                    
                    <!-- Tombol Hapus Favorit Cepat -->
                    <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <form action="{{ route('favorite.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $p->id }}">
                            <button class="btn btn-light rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Hapus dari Favorit">
                                <i class="fa fa-heart text-danger"></i>
                            </button>
                        </form>
                    </div>

                    @if($p->image && \Storage::disk('public')->exists($p->image))
                        <img src="{{ Storage::url($p->image) }}" class="card-img-top border-bottom" style="height:190px; object-fit:cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center border-bottom text-muted" style="height:190px;">
                            <i class="fa fa-image fa-2x opacity-25"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column p-4">
                        <h6 class="card-title text-truncate fw-bold mb-1" title="{{ $p->name }}">{{ $p->name }}</h6>
                        <p class="text-muted small mb-2"><i class="fa fa-store me-1"></i> {{ $p->seller->name ?? 'Toko' }}</p>
                        <h5 class="fw-bold text-danger mt-1 mb-3">Rp {{ number_format($p->price, 0, ',', '.') }}</h5>
                        
                        <div class="mt-auto">
                            <a href="{{ route('product.show', $p->id) }}" class="btn btn-outline-danger w-100 rounded-pill">Lihat Produk</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
