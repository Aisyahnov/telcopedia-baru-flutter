@extends('layouts.app')
@section('title', 'Pusat Bantuan - Telcopedia')

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
    .contact-card-container { 
        border: none; 
        border-radius: 30px; 
        box-shadow: 0 20px 60px rgba(0,0,0,0.06); 
        background: #fff; 
        margin-top: -80px;
        position: relative;
        z-index: 5;
        overflow: hidden;
    }
    .faq-card { border: none; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 15px; }
    .accordion-button:not(.collapsed) { background-color: #fff5f5; color: #9F1521; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; border-color: #9F1521; }
    
    .contact-option { 
        border-radius: 20px; 
        transition: 0.3s; 
        border: 2px solid #f8f9fa; 
        background: #fdfdfd;
    }
    .contact-option:hover { 
        border-color: #9F1521; 
        transform: translateY(-5px); 
        box-shadow: 0 15px 40px rgba(0,0,0,0.08); 
        background: #fff;
    }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 12px; font-weight: 700; transition: 0.3s; padding: 12px 25px; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: translateY(-2px); }
    
    .icon-circle { 
        width: 80px; height: 80px; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 2rem; margin-bottom: 20px;
    }
    
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
        <h1 class="display-4 fw-900 mb-3" style="letter-spacing: -1px;">Hi, Ada yang Bisa Dibantu? 👋</h1>
        <p class="lead opacity-90 mx-auto fw-500" style="max-width: 600px;">Pusat Bantuan & Layanan Pelanggan Telcopedia siap melayani Anda setiap hari.</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="contact-card-container p-4 p-md-5">
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card p-4 p-md-5 contact-option h-100 shadow-sm">
                    <div class="icon-circle bg-success text-white mx-auto shadow-sm">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-900">WhatsApp Bantuan</h4>
                        <p class="text-muted small mb-4 fw-500">Chat Admin untuk bantuan cepat seputar verifikasi pembayaran, kendala login, atau laporan transaksi.</p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-maroon w-100 shadow-sm">
                            Mulai Chat Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4 p-md-5 contact-option h-100 shadow-sm">
                    <div class="icon-circle bg-maroon text-white mx-auto shadow-sm" style="background-color: #9F1521;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-900">Email Support</h4>
                        <p class="text-muted small mb-4 fw-500">Gunakan email untuk pengajuan kerjasama, penghapusan akun, atau keluhan teknis yang membutuhkan lampiran dokumen.</p>
                        <a href="mailto:cs@telcopedia.id" class="btn btn-outline-dark fw-bold rounded-pill w-100 py-3">
                            Kirim Email Resmi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQ SECTION --}}
        <div class="faq-container mx-auto" style="max-width: 900px;">
            <div class="text-center mb-5">
                <h6 class="text-maroon fw-900 text-uppercase letter-spacing-2" style="color: #9F1521;">FAQ</h6>
                <h3 class="fw-900">Pertanyaan Sering Diajukan</h3>
            </div>
            
            <div class="accordion accordion-flush" id="faqAccordion">
                <div class="accordion-item faq-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-800 py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Bagaimana cara menjadi Penjual (Seller)?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted lh-lg">
                            Login ke akun Anda, buka menu profil di pojok kanan atas, lalu pilih <strong>"Mulai Jualan"</strong>. Anda perlu melengkapi nomor WhatsApp yang aktif untuk memudahkan koordinasi dengan pembeli di area kampus.
                        </div>
                    </div>
                </div>

                <div class="accordion-item faq-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Berapa biaya layanan di Telcopedia?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted lh-lg">
                            Kami mengenakan biaya layanan tetap sebesar <strong>5%</strong> dari total belanja. Dana ini digunakan sepenuhnya untuk pengembangan infrastruktur Telcopedia dan memastikan keamanan transaksi mahasiswa.
                        </div>
                    </div>
                </div>

                <div class="accordion-item faq-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Metode pembayaran apa saja yang didukung?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted lh-lg">
                            Kami mendukung **Transfer Bank** (Manual Verification) dan **COD (Cash on Delivery)**. Untuk COD, pastikan Anda membuat janji temu di area publik kampus seperti GKU, Asrama, atau Kantin Fakultas.
                        </div>
                    </div>
                </div>

                <div class="accordion-item faq-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Apa yang harus dilakukan jika barang tidak sesuai?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted lh-lg">
                            Anda dapat mengajukan **Retur** melalui halaman Riwayat Pesanan sebelum menekan tombol "Pesanan Diterima". Pastikan Anda melampirkan foto/video sebagai bukti ketidaksesuaian barang.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5 pt-4">
            <p class="text-muted small">Belum menemukan jawaban? Hubungi kami melalui media sosial Telcopedia.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="text-dark fs-4"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-dark fs-4"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-dark fs-4"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
