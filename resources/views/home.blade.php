@extends('layouts.app')
@section('title', 'Telcopedia - Home')

@push('styles')
<style>
    .hero-card { border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
    .product-card { border-radius: 10px; transition: transform .15s; border: 1px solid #eee !important; }
    .product-card:hover { transform: translateY(-6px); box-shadow:0 10px 20px rgba(0,0,0,0.08); }
    .category-btn { border-radius: 20px; padding: 6px 20px; font-weight: 500; font-size: 14px; }
    .category-btn.active { background-color: #9F1521; color: #fff; border-color: #9F1521; }
    .testimonial { background:#fff; border-radius:10px; padding:20px; box-shadow:0 6px 18px rgba(0,0,0,0.04); height: 100%; }
    .usp-icon { font-size: 2rem; color: #9F1521; margin-bottom: 15px; }
    .step-number { width: 40px; height: 40px; border-radius: 50%; background: #9F1521; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin: 0 auto 15px; }
    .seller-cta { background: linear-gradient(135deg, #9F1521 0%, #4a0910 100%); border-radius: 15px; color: white; padding: 40px; }
</style>
@endpush

@section('content')

<!-- HERO / CAROUSEL -->
<div class="container my-4">
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="row align-items-center">
          <div class="col-lg-7">
            <div class="p-4">
              <h1 class="display-6 text-danger fw-bold">Find Quality Used Items at Affordable Prices!</h1>
              <p class="lead text-muted">Look for high-quality used goods at pocket-friendly prices. Start shopping now!</p>
              <a href="#products" class="btn btn-danger btn-lg rounded-pill px-4">Start Shopping</a>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="bg-secondary rounded hero-card" style="height:350px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- USP Section -->
<div class="container mb-5">
  <div class="row text-center g-4">
    <div class="col-md-4">
      <div class="usp-icon"><i class="fa-solid fa-shield-halved"></i></div>
      <h6 class="fw-bold">Aman & Terpercaya</h6>
      <p class="text-muted small">Seluruh user terverifikasi dengan NIM Mahasiswa Telkom University.</p>
    </div>
    <div class="col-md-4">
      <div class="usp-icon"><i class="fa-solid fa-tags"></i></div>
      <h6 class="fw-bold">Harga Mahasiswa</h6>
      <p class="text-muted small">Cari barang preloved berkualitas dengan harga yang ramah di kantong.</p>
    </div>
    <div class="col-md-4">
      <div class="usp-icon"><i class="fa-solid fa-handshake-simple"></i></div>
      <h6 class="fw-bold">Mudah & Cepat</h6>
      <p class="text-muted small">Transaksi bisa langsung COD di area kampus atau sekitaran Dayeuhkolot.</p>
    </div>
  </div>
</div>

<!-- CATEGORIES -->
<div class="container mb-4">
  <h5 class="mb-3">Featured Category</h5>
  <div class="d-flex flex-row gap-2 overflow-auto pb-2">
    <a href="{{ route('home') }}" class="btn btn-outline-secondary category-btn {{ !request('category_id') ? 'active' : '' }}">Semua</a>
    @foreach(\App\Models\Category::all() as $cat)
      <a href="{{ route('category.show', $cat->slug) }}" 
         class="btn btn-outline-secondary category-btn {{ (isset($category) && $category->id == $cat->id) ? 'active' : '' }}">
         {{ $cat->name }}
      </a>
    @endforeach
  </div>
</div>

<!-- PRODUCTS -->
<div id="products" class="container mb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h5 class="fw-bold mb-1">
        @if(request('keyword'))
          Hasil Pencarian: "{{ request('keyword') }}"
        @elseif(request('category_id'))
          Kategori: {{ \App\Models\Category::find(request('category_id'))->name ?? 'Semua' }}
        @else
          Produk Terbaru!
        @endif
      </h5>
      <p class="text-muted small mb-0">Menampilkan {{ $products->count() }} produk terbaik untukmu.</p>
    </div>
    @if(request('keyword') || request('category_id'))
      <a href="{{ route('home') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus Filter</a>
    @endif
  </div>

  <div class="row g-3" id="productsContainer">
    @forelse($products as $p)
      <div class="col-12 col-md-4 col-lg-3 product-item">
        <div class="card product-card h-100 border-0 shadow-sm overflow-hidden">
          {{-- Product Image Placeholder --}}
          <div class="bg-light w-100 position-relative" style="height:190px;">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($p->name) }}&background=f8f9fa&color=9F1521&size=200" class="w-100 h-100 object-fit-cover">
            <span class="position-absolute top-0 end-0 m-2 badge bg-white text-danger shadow-sm">{{ optional($p->category)->name }}</span>
          </div>
          <div class="card-body p-3">
            <h6 class="card-title fw-bold text-truncate mb-1">{{ $p->name }}</h6>
            <p class="fw-bold text-danger mb-3">Rp {{ number_format($p->price,0,',','.') }}</p>
            <div class="d-flex gap-2">
              <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                    <button class="btn btn-sm btn-danger w-100 rounded-8 py-2">Tambah</button>
                </form>
              <a href="{{ route('product.show', $p->id) }}" class="btn btn-sm btn-outline-secondary px-3 rounded-8 py-2">
                <i class="fa fa-eye"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <div class="mb-3">
            <i class="fa-solid fa-magnifying-glass fa-4x text-muted opacity-25"></i>
        </div>
        <h5 class="fw-bold">Oops! Produk tidak ditemukan</h5>
        <p class="text-muted">Coba gunakan kata kunci lain atau telusuri kategori yang berbeda.</p>
        <a href="{{ route('home') }}" class="btn btn-danger rounded-pill px-4 mt-2">Lihat Semua Produk</a>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="d-flex justify-content-center mt-5">
    {{ $products->appends(request()->query())->links() }}
  </div>
</div>

<!-- HOW IT WORKS Section -->
<div class="bg-white py-5 mb-5">
  <div class="container">
    <div class="text-center mb-5">
      <h4 class="fw-bold">Bagaimana Cara Belanja?</h4>
      <p class="text-muted">Cuma 3 langkah mudah untuk mendapatkan barang impianmu.</p>
    </div>
    <div class="row text-center g-4">
      <div class="col-md-4">
        <div class="step-number">1</div>
        <h6 class="fw-bold">Cari Produk</h6>
        <p class="text-muted small px-lg-5">Temukan barang yang kamu butuhkan dari galeri produk kami.</p>
      </div>
      <div class="col-md-4">
        <div class="step-number">2</div>
        <h6 class="fw-bold">Hubungi Penjual</h6>
        <p class="text-muted small px-lg-5">Gunakan fitur Chat untuk bertanya detail atau nego harga.</p>
      </div>
      <div class="col-md-4">
        <div class="step-number">3</div>
        <h6 class="fw-bold">Transaksi & COD</h6>
        <p class="text-muted small px-lg-5">Atur janji ketemuan dan lakukan transaksi dengan aman.</p>
      </div>
    </div>
  </div>
</div>

<!-- TESTIMONIAL -->
<div class="container mb-5">
  <h5 class="mb-3">Testimonial</h5>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="testimonial">
        <p>"I was skeptical about buying second-hand items online, but this platform exceeded my expectations!"</p>
        <div class="fw-bold fs-6 mt-3">Aisyah Noviani</div>
        <small class="text-danger">Student</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="testimonial">
        <p>"I love how easy it is to find gently used items at affordable prices."</p>
        <div class="fw-bold fs-6 mt-3">Siti Amany</div>
        <small class="text-danger">Student</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="testimonial">
        <p>"The selection is amazing! I found exactly what I needed."</p>
        <div class="fw-bold fs-6 mt-3">Andi Bayu</div>
        <small class="text-danger">Student</small>
      </div>
    </div>
  </div>
</div>

<!-- SELLER CTA Section -->
<div class="container mb-5">
  <div class="seller-cta text-center shadow">
    <h3 class="fw-bold mb-3">Mau Jual Barang Tak Terpakai?</h3>
    <p class="mb-4">Mulai hasilkan uang dari barang-barangmu di Telcopedia. Proses cepat dan tanpa ribet.</p>
    <a href="{{ route('register.form') }}" class="btn btn-light btn-lg px-4 fw-bold rounded-pill" style="color: #9F1521;">Buka Toko Sekarang</a>
  </div>
</div>

@endsection
