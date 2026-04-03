@extends('layouts.app')
@section('title', 'Syarat & Ketentuan - Telcopedia')

@push('styles')
<style>
    body { background-color: #f8f9fa; }
    .legal-header { 
        background: #9F1521; 
        background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.05) 0%, transparent 25%), 
                          radial-gradient(circle at 80% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 25%);
        color: white; 
        padding: 50px 0 90px; 
        border-radius: 0 0 40px 40px;
        position: relative;
    }
    .legal-card { 
        border: none; 
        border-radius: 25px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); 
        background: #fff; 
        margin-top: -60px;
        position: relative;
        z-index: 5;
    }
    .sticky-nav { position: sticky; top: 100px; }
    .nav-link-legal { 
        color: #495057; 
        font-weight: 600; 
        padding: 10px 15px; 
        border-radius: 10px; 
        transition: 0.2s; 
        display: block;
        text-decoration: none;
        font-size: 0.85rem;
    }
    .nav-link-legal:hover { background: #f8f9fa; color: #9F1521; }
    .nav-link-legal.active { background: #fee2e2; color: #9F1521 !important; }
    
    .section-icon { 
        width: 42px; height: 42px; border-radius: 10px; 
        background: #9F1521; display: flex; align-items: center; justify-content: center; 
        color: #fff; font-size: 1.1rem; margin-right: 15px;
        box-shadow: 0 4px 10px rgba(159, 21, 33, 0.2);
    }
    .legal-section { padding-bottom: 35px; margin-bottom: 35px; border-bottom: 1px solid #f1f1f1; }
    .legal-section:last-child { border-bottom: none; }
    
    .breadcrumb-legal a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.8rem; transition: 0.2s; }
    .breadcrumb-legal a:hover { color: #fff; }
    .breadcrumb-legal span { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin: 0 8px; }
    
    .fade-in-up { animation: fadeInUp 0.6s ease-out; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="legal-header text-center">
    <div class="container fade-in-up">
        <div class="breadcrumb-legal mb-2">
            <a href="{{ route('home') }}">Beranda</a> <span>/</span> <a href="{{ route('contact') }}">Bantuan</a> <span>/</span> <span class="text-white opacity-100">Syarat</span>
        </div>
        <h1 class="fw-bold head-title mb-1">Syarat & Ketentuan</h1>
        <p class="small opacity-75 mb-0">Aturan main transaksi di lingkungan Telkom University</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="row g-4 mt-1">
        {{-- SIDEBAR NAV (Desktop Only) --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-nav bg-white p-3 rounded-4 shadow-sm border">
                <p class="fw-bold text-dark mb-3 small text-uppercase letter-spacing-1">Navigasi Pasal</p>
                <a href="#kepatuhan" class="nav-link-legal mb-1">1. Ketentuan Civitas</a>
                <a href="#mekanisme" class="nav-link-legal mb-1">2. Mekanisme Jual Beli</a>
                <a href="#biaya" class="nav-link-legal mb-1">3. Biaya Layanan 5%</a>
                <a href="#pembatasan" class="nav-link-legal mb-1">4. Barang Terlarang</a>
                <a href="#sanksi" class="nav-link-legal mb-1">5. Sanksi Pelanggaran</a>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-lg-9">
            <div class="legal-card p-4 p-md-5">
                <div id="kepatuhan" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon shadow-sm"><i class="fa-solid fa-graduation-cap"></i></div>
                        <h5 class="fw-bold mb-0">1. Kepatuhan Akademis Mahasiswa</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Telcopedia hanya dapat diakses oleh civitas akademika Telkom University yang memiliki <strong>NIM (Nomor Induk Mahasiswa)</strong> yang aktif. User bertanggung jawab atas segala aktivitas di dalam akunnya.</p>
                    </div>
                </div>

                <div id="mekanisme" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon shadow-sm"><i class="fa-solid fa-cart-shopping"></i></div>
                        <h5 class="fw-bold mb-0">2. Mekanisme Transaksi & COD</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Kami menyarankan metode <strong>COD (Cash on Delivery)</strong> di area kampus demi kenyamanan bersama. Harap deskripsikan kondisi barang preloved Anda secara jujur.</p>
                    </div>
                </div>

                <div id="biaya" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon shadow-sm"><i class="fa-solid fa-receipt"></i></div>
                        <h5 class="fw-bold mb-0">3. Kebijakan Biaya Admin 5%</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Tiap transaksi dikenakan biaya admin tetap sebesar <strong>5% dari Subtotal</strong> untuk pemeliharaan sistem Telcopedia.</p>
                    </div>
                </div>

                <div id="pembatasan" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon shadow-sm"><i class="fa-solid fa-ban"></i></div>
                        <h5 class="fw-bold mb-0">4. Larangan Barang Ilegal</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Dilarang menjual barang curian, joki tugas akademik, produk crack, atau barang terlarang lainnya sesuai aturan kampus.</p>
                    </div>
                </div>

                <div id="sanksi" class="text-center mt-4">
                    <div class="p-4 bg-dark text-white rounded-4 shadow-sm border-0">
                        <p class="small mb-0 opacity-75">Tindakan penipuan akan dilaporkan ke pihak kemahasiswaan kampus Telkom University.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
