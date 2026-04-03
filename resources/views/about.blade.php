@extends('layouts.app')
@section('title', 'Tentang Kami - Telcopedia')

@push('styles')
<style>
    .hero-about { background: linear-gradient(rgba(159, 21, 33, 0.85), rgba(159, 21, 33, 0.85)), url('https://images.unsplash.com/photo-1523050853064-8558ef7cc0a5?q=80&w=1470&auto=format&fit=crop'); background-size: cover; background-position: center; color: white; padding: 100px 0; border-radius: 0 0 50px 50px; }
    .story-card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
    .icon-box { width: 70px; height: 70px; border-radius: 20px; background: #fff5f5; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #9F1521; margin-bottom: 20px; }
    .stat-number { font-size: 2.5rem; font-weight: 800; color: #9F1521; }
    .bg-maroon-soft { background-color: #fff5f5; }
</style>
@endpush

@section('content')
{{-- HERO SECTION --}}
<div class="hero-about text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Tentang Telcopedia</h1>
        <p class="lead opacity-90 mx-auto" style="max-width: 700px;">Platform Jual-Beli Terpercaya Khusus Mahasiswa Telkom University. Bangun Ekonomi Kampus yang Berkelanjutan.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?q=80&w=1470&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg">
        </div>
        <div class="col-lg-6">
            <h2 class="fw-bold mb-4">Misi Kami</h2>
            <p class="text-muted mb-4">Telcopedia lahir dari kebutuhan mahasiswa akan platform yang aman, mudah, dan terpercaya untuk bertransaksi barang-barang preloved berkualitas di lingkungan Telkom University.</p>
            
            <div class="d-flex mb-4">
                <div class="icon-box me-3 flex-shrink-0"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <div>
                    <h5 class="fw-bold">Ekonomi Berkelanjutan</h5>
                    <p class="text-muted small">Membantu mahasiswa mendapatkan barang berkualitas dengan harga terjangkau sekaligus memberikan penghasilan tambahan bagi penjual.</p>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="icon-box me-3 flex-shrink-0"><i class="fa-solid fa-shield-check"></i></div>
                <div>
                    <h5 class="fw-bold">Keamanan Terjamin</h5>
                    <p class="text-muted small">Setiap transaksi dipantau dan diverifikasi oleh admin untuk memastikan tidak ada penipuan di lingkungan kampus kita.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS SECTION --}}
    <div class="row text-center mt-5 py-5 bg-maroon-soft rounded-4">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="stat-number">1000+</div>
            <p class="fw-bold text-muted">Mahasiswa Terdaftar</p>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="stat-number">500+</div>
            <p class="fw-bold text-muted">Barang Terjual</p>
        </div>
        <div class="col-md-4">
            <div class="stat-number">50+</div>
            <p class="fw-bold text-muted">Kategori Produk</p>
        </div>
    </div>

    {{-- THE TEAM / VALUES --}}
    <div class="text-center mt-5 pt-5 mb-5">
        <h2 class="fw-bold mb-5">Kenapa Memilih Telcopedia?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card story-card p-4 h-100">
                    <i class="fa-solid fa-users fa-3x text-danger mb-3 opacity-25"></i>
                    <h5 class="fw-bold">Komunitas Eksklusif</h5>
                    <p class="text-muted small">Hanya untuk civitas akademika Telkom University menggunakan NIM resmi.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card story-card p-4 h-100">
                    <i class="fa-solid fa-truck-fast fa-3x text-danger mb-3 opacity-25"></i>
                    <h5 class="fw-bold">COD Area Kampus</h5>
                    <p class="text-muted small">Transaksi lebih mudah dengan sistem Cash on Delivery di area asrama atau fakultas.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card story-card p-4 h-100">
                    <i class="fa-solid fa-leaf fa-3x text-danger mb-3 opacity-25"></i>
                    <h5 class="fw-bold">Ramah Lingkungan</h5>
                    <p class="text-muted small">Mengurangi limbah dengan memberikan kehidupan kedua bagi barang-barang preloved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
