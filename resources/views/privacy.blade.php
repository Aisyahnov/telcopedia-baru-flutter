@extends('layouts.app')
@section('title', 'Kebijakan Privasi - Telcopedia')

@push('styles')
<style>
    body { background-color: #f8f9fa; }
    .legal-header { 
        background: #9F1521; 
        background-image: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 25%), 
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
            <a href="{{ route('home') }}">Beranda</a> <span>/</span> <a href="{{ route('contact') }}">Bantuan</a> <span>/</span> <span class="text-white opacity-100">Privasi</span>
        </div>
        <h1 class="fw-bold head-title mb-1">Kebijakan Privasi</h1>
        <p class="small opacity-75 mb-0">Halaman perlindungan data mahasiswa Telkom University</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="row g-4 mt-1">
        {{-- SIDEBAR NAV --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sticky-nav bg-white p-3 rounded-4 shadow-sm border">
                <p class="fw-bold text-dark mb-3 small text-uppercase letter-spacing-1">Navigasi Pasal</p>
                <a href="#pengumpulan" class="nav-link-legal mb-1">1. Pengumpulan Data</a>
                <a href="#keamanan" class="nav-link-legal mb-1">2. Protokol Keamanan</a>
                <a href="#cookies" class="nav-link-legal mb-1">3. Kebijakan Cookies</a>
                <a href="#hak" class="nav-link-legal mb-1">4. Hak Akses User</a>
                <a href="#kontak" class="nav-link-legal mb-1">5. Hubungi Admin</a>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-lg-9">
            <div class="legal-card p-4 p-md-5">
                <div id="pengumpulan" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon"><i class="fa-solid fa-database"></i></div>
                        <h5 class="fw-bold mb-0">1. Pengumpulan Data Identitas</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg">Sebagai platform internal, Telcopedia mengumpulkan data akademik secara terbatas:</p>
                        <ul class="text-muted lh-lg small">
                            <li><strong>Identitas:</strong> Nama lengkap dan NIM resmi kampus.</li>
                            <li><strong>Verifikasi:</strong> Nomor WhatsApp untuk koordinasi COD.</li>
                        </ul>
                    </div>
                </div>

                <div id="keamanan" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon"><i class="fa-solid fa-lock"></i></div>
                        <h5 class="fw-bold mb-0">2. Protokol Keamanan Data</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Seluruh password dienkripsi menggunakan algoritme <strong>BCRYPT</strong> yang aman. Kami menjamin data NIM Anda tidak akan dibagikan ke pihak luar kampus.</p>
                    </div>
                </div>

                <div id="cookies" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon"><i class="fa-solid fa-cookie-bite"></i></div>
                        <h5 class="fw-bold mb-0">3. Kebijakan Cookies</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Cookies digunakan murni untuk menjaga sesi login Anda agar tetap lancar tanpa harus login berulang kali.</p>
                    </div>
                </div>

                <div id="hak" class="legal-section">
                    <div class="d-flex align-items-center mb-4">
                        <div class="section-icon"><i class="fa-solid fa-user-check"></i></div>
                        <h5 class="fw-bold mb-0">4. Hak Akses Mahasiswa</h5>
                    </div>
                    <div class="ps-md-5 ms-md-2">
                        <p class="text-muted lh-lg small">Anda berhak mengubah profil dan menghapus data history transaksi kapan saja melalui dashboard Telcopedia.</p>
                    </div>
                </div>

                <div id="kontak" class="text-center mt-4">
                    <div class="p-4 bg-light rounded-4 border border-dashed">
                        <h6 class="fw-bold mb-2">Punya pertanyaan privasi?</h6>
                        <a href="mailto:cs@telcopedia.id" class="text-danger fw-bold text-decoration-none">cs@telcopedia.id</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
