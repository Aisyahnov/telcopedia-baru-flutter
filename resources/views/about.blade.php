@extends('layouts.app')
@section('title', 'Tentang Kami - Telcopedia')

@push('styles')
<style>
    .legal-header { 
        background: #9F1521; 
        background-image: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 25%), 
                          radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 25%);
        color: white; 
        padding: 80px 0 120px; 
        border-radius: 0 0 50px 50px;
        position: relative;
    }
    .about-card { 
        border: none; 
        border-radius: 30px; 
        box-shadow: 0 20px 60px rgba(0,0,0,0.06); 
        background: #fff; 
        margin-top: -80px;
        position: relative;
        z-index: 5;
        overflow: hidden;
    }
    .icon-box { 
        width: 60px; height: 60px; border-radius: 18px; 
        background: #fff5f5; display: flex; align-items: center; justify-content: center; 
        font-size: 1.5rem; color: #9F1521; margin-bottom: 20px;
        transition: 0.3s;
    }
    .story-card:hover .icon-box { background: #9F1521; color: #fff; transform: scale(1.1); }
    .stat-number { font-size: 3.5rem; font-weight: 900; color: #9F1521; line-height: 1; letter-spacing: -2px; }
    .bg-maroon-soft { background-color: #fffcfc; border: 1px solid #fff0f0; }
    
    .fade-in-up { animation: fadeInUp 0.8s ease-out; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="legal-header text-center">
    <div class="container fade-in-up">
        <h1 class="display-4 fw-900 mb-3">Tentang <span class="text-white">Telcopedia</span></h1>
        <p class="lead opacity-90 mx-auto fw-500" style="max-width: 700px;">Platform Jual-Beli Terpercaya Khusus Mahasiswa Telkom University. Bangun Ekonomi Kampus yang Berkelanjutan.</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="about-card p-4 p-md-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?q=80&w=1470&auto=format&fit=crop" class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute bottom-0 end-0 bg-white p-3 rounded-4 shadow-sm m-3 d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                            <span class="small fw-bold text-dark">Verified Campus Platform</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h6 class="text-maroon fw-900 text-uppercase letter-spacing-2 mb-3" style="color: #9F1521;">Visi & Misi</h6>
                <h2 class="fw-900 mb-4" style="letter-spacing: -1px;">Membangun Ekonomi Digital yang Berintegritas di Kampus</h2>
                <p class="text-muted mb-4 lh-lg fw-500">Telcopedia lahir dari kebutuhan mahasiswa akan platform yang aman, mudah, dan terpercaya untuk bertransaksi barang-barang preloved berkualitas di lingkungan Telkom University.</p>
                
                <div class="d-flex mb-4">
                    <div class="icon-box me-3 flex-shrink-0 shadow-sm"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Ekonomi Berkelanjutan</h5>
                        <p class="text-muted small mb-0">Membantu mahasiswa mendapatkan barang berkualitas dengan harga terjangkau sekaligus memberikan penghasilan tambahan bagi penjual.</p>
                    </div>
                </div>

                <div class="d-flex mb-0">
                    <div class="icon-box me-3 flex-shrink-0 shadow-sm"><i class="fa-solid fa-shield-check"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Keamanan Terjamin</h5>
                        <p class="text-muted small mb-0">Setiap transaksi dipantau dan diverifikasi oleh admin untuk memastikan tidak ada penipuan di lingkungan kampus kita.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS SECTION --}}
        <div class="row text-center mt-5 py-5 bg-maroon-soft rounded-4 border">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-number">1k+</div>
                <p class="fw-bold text-muted small text-uppercase mt-2">Mahasiswa Terdaftar</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="stat-number">500+</div>
                <p class="fw-bold text-muted small text-uppercase mt-2">Barang Terjual</p>
            </div>
            <div class="col-md-4">
                <div class="stat-number">50+</div>
                <p class="fw-bold text-muted small text-uppercase mt-2">Kategori Produk</p>
            </div>
        </div>

        {{-- VALUES --}}
        <div class="text-center mt-5 pt-4">
            <h3 class="fw-900 mb-5">Kenapa Memilih Telcopedia?</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-light p-4 h-100 rounded-4 transition-transform story-card">
                        <div class="icon-box mx-auto shadow-sm"><i class="fa-solid fa-users"></i></div>
                        <h5 class="fw-bold">Komunitas Eksklusif</h5>
                        <p class="text-muted small mb-0">Hanya untuk civitas akademika Telkom University menggunakan NIM resmi yang tervalidasi.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-light p-4 h-100 rounded-4 transition-transform story-card">
                        <div class="icon-box mx-auto shadow-sm"><i class="fa-solid fa-truck-fast"></i></div>
                        <h5 class="fw-bold">COD Area Kampus</h5>
                        <p class="text-muted small mb-0">Transaksi aman dengan sistem Cash on Delivery di area asrama, fakultas, atau GKU.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-light p-4 h-100 rounded-4 transition-transform story-card">
                        <div class="icon-box mx-auto shadow-sm"><i class="fa-solid fa-leaf"></i></div>
                        <h5 class="fw-bold">Ramah Lingkungan</h5>
                        <p class="text-muted small mb-0">Mendukung gerakan zero waste dengan memberikan kehidupan kedua bagi barang-barang preloved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
