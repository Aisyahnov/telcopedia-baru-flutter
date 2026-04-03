@extends('layouts.app')
@section('title', 'Customer Service - Telcopedia')

@push('styles')
<style>
    .cs-header { background: #9F1521; color: white; padding: 60px 0; border-radius: 0 0 50px 50px; }
    .faq-card { border: none; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.04); overflow: hidden; }
    .accordion-button:not(.collapsed) { background-color: #fff5f5; color: #9F1521; box-shadow: none; }
    .accordion-button:focus { box-shadow: none; border-color: #9F1521; }
    .contact-card { border-radius: 20px; transition: 0.3s; border: 2px solid transparent; }
    .contact-card:hover { border-color: #9F1521; transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 12px; font-weight: 600; transition: 0.3s; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: scale(1.05); }
    .btn-outline-maroon { border: 2px solid #9F1521; color: #9F1521; font-weight: 600; border-radius: 12px; transition: 0.3s; }
    .btn-outline-maroon:hover { background-color: #9F1521; color: white; }
</style>
@endpush

@section('content')
<div class="cs-header text-center mb-5">
    <div class="container">
        <h1 class="fw-bold mb-3">Hi, Ada yang Bisa Dibantu? 👋</h1>
        <p class="opacity-90">Pusat Bantuan & Layanan Pelanggan Telcopedia siap melayani Anda.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4 mb-5 text-center">
        <div class="col-md-6">
            <div class="card p-5 contact-card h-100 shadow-sm border-0">
                <i class="fab fa-whatsapp fa-4x text-success mb-3"></i>
                <h4 class="fw-bold">WhatsApp Bantuan</h4>
                <p class="text-muted small mb-4">Chat Admin untuk bantuan cepat seputar verifikasi pembayaran & kendala transaksi.</p>
                <div class="mt-auto">
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-maroon px-4 py-2 w-100 shadow-sm">Hubungi via WA</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-5 contact-card h-100 shadow-sm border-0">
                <i class="fa-solid fa-envelope fa-4x text-maroon mb-3" style="color: #9F1521;"></i>
                <h4 class="fw-bold">Email Bisnis</h4>
                <p class="text-muted small mb-4">Hubungi kami melalui email untuk kerjasama atau keluhan teknis akun yang lebih mendalam.</p>
                <div class="mt-auto">
                    <a href="mailto:cs@telcopedia.id" class="btn btn-outline-maroon px-4 py-2 w-100">Kirim Email</a>
                </div>
            </div>
        </div>
    </div>

    {{-- FAQ SECTION --}}
    <div class="faq-container mx-auto" style="max-width: 800px;">
        <h3 class="fw-bold text-center mb-5">Pertanyaan Sering Diajukan (FAQ)</h3>
        
        <div class="accordion accordion-flush" id="faqAccordion">
            {{-- FAQ 1 --}}
            <div class="accordion-item faq-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Bagaimana cara menjadi Penjual (Seller)?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Pastikan Anda sudah login, lalu buka menu profil di pojok kanan atas. Pilih menu "Jadi Seller" atau "Daftarkan Produk". Anda perlu melengkapi nomor HP yang aktif agar pembeli mudah menghubungi Anda.
                    </div>
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="accordion-item faq-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Berapa biaya admin di Telcopedia?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Kami mengenakan biaya admin sebesar **5%** dari subtotal belanja Anda. Biaya ini digunakan untuk pengembangan sistem dan pemeliharaan server agar transaksi di Telkom tetap aman.
                    </div>
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div class="accordion-item faq-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Bagaimana jika bukti pembayaran ditolak?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Bukti pembayaran bisa ditolak oleh admin jika gambar tidak terbaca atau nominal tidak sesuai. Silakan upload ulang bukti bayar yang benar di halaman Riwayat Belanja atau hubungi admin via WhatsApp untuk bantuan manual.
                    </div>
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="accordion-item faq-card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Apakah bisa COD (Cash on Delivery)?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        Tentu! Kami sangat menyarankan COD di area Telkom University (Fakultas, Asrama, atau GKU). Silakan atur lokasi pertemuan dengan Seller melalui nomor WhatsApp yang tertera di detail produk.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
