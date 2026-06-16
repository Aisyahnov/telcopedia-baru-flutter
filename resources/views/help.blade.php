@extends('layouts.app')
@section('title', 'Pusat Bantuan - Telcopedia')

@push('styles')
<style>
    /* Styling konsisten dengan halaman lain */
    .help-sidebar {
        position: sticky;
        top: 20px;
    }
    .help-nav .nav-link {
        color: #555;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 5px;
        transition: 0.2s;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .help-nav .nav-link:hover {
        background-color: var(--telco-maroon-soft);
        color: var(--telco-maroon);
    }
    .help-nav .nav-link.active {
        background-color: var(--telco-maroon);
        color: white;
    }
    .help-nav .nav-link i {
        width: 24px;
        text-align: center;
        margin-right: 10px;
    }
    .help-content-card {
        border-radius: 16px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
    .accordion-button:not(.collapsed) {
        color: var(--telco-maroon);
        background-color: var(--telco-maroon-soft);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    
    <!-- Title Section Consistent with Other Pages -->
    <div class="text-center mb-5 mt-4">
        <h2 class="fw-900" style="letter-spacing: -1px;">Pusat <span class="text-maroon">Bantuan</span></h2>
        <p class="text-muted">Temukan jawaban, panduan, privasi, dan aturan layanan Telcopedia.</p>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="help-sidebar">
                <div class="nav flex-column nav-pills help-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq" type="button" role="tab">
                        <i class="fa-solid fa-circle-question"></i> FAQ & Bantuan
                    </button>
                    <button class="nav-link" id="v-pills-terms-tab" data-bs-toggle="pill" data-bs-target="#v-pills-terms" type="button" role="tab">
                        <i class="fa-solid fa-file-contract"></i> Syarat & Ketentuan
                    </button>
                    <button class="nav-link" id="v-pills-privacy-tab" data-bs-toggle="pill" data-bs-target="#v-pills-privacy" type="button" role="tab">
                        <i class="fa-solid fa-shield-halved"></i> Kebijakan Privasi
                    </button>
                    <button class="nav-link" id="v-pills-contact-tab" data-bs-toggle="pill" data-bs-target="#v-pills-contact" type="button" role="tab">
                        <i class="fa-solid fa-headset"></i> Hubungi Kami
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="card help-content-card bg-white p-4 p-md-5">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- FAQ TAB -->
                    <div class="tab-pane fade show active" id="v-pills-faq" role="tabpanel">
                        <h4 class="fw-bold mb-4 border-bottom pb-3">Pertanyaan yang Sering Diajukan (FAQ)</h4>
                        
                        <div class="accordion" id="accordionFAQ">
                            <div class="accordion-item mb-3 rounded-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Apakah Telcopedia dijamin aman dari penipuan?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted lh-lg">
                                        Sangat aman. Pendaftaran Telcopedia mewajibkan verifikasi menggunakan email mahasiswa (NIM) resmi Telkom University. Sehingga penjual dan pembeli dapat saling mengetahui identitas aslinya. Jika terjadi indikasi penipuan, tim admin dapat melacak pelakunya.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item mb-3 rounded-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Bagaimana sistem pembayaran bekerja di Telcopedia?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted lh-lg">
                                        Saat ini, Telcopedia mendukung 2 metode utama:<br>
                                        <strong>1. COD (Cash on Delivery)</strong> - Membayar tunai saat bertemu langsung dengan penjual di area kampus.<br>
                                        <strong>2. Transfer Langsung</strong> - Mentransfer dana antar rekening bank atau e-wallet yang disepakati dengan penjual via chat.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item mb-3 rounded-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Apakah dikenakan biaya admin setiap bertransaksi?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted lh-lg">
                                        Telcopedia dikelola untuk kesejahteraan mahasiswa. Untuk mendukung biaya operasional dan pengembangan platform, kami mengenakan <strong>Biaya Layanan (Admin Fee) sebesar 5%</strong> dari total transaksi. Biaya ini sangat terjangkau dan akan dikembalikan untuk program kerja BEM Telkom University.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item mb-3 rounded-3 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Barang apa saja yang dilarang keras untuk dijual?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body text-muted lh-lg">
                                        Demi menjaga integritas kampus, Telcopedia melarang keras penjualan produk bajakan, jasa pembuatan tugas/joki, senjata tajam, obat-obatan terlarang, makanan kadaluarsa, serta barang yang melanggar hukum dan norma.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TERMS TAB -->
                    <div class="tab-pane fade" id="v-pills-terms" role="tabpanel">
                        <h4 class="fw-bold mb-4 border-bottom pb-3">Syarat & Ketentuan Layanan</h4>
                        <div class="text-muted lh-lg">
                            <h6 class="fw-bold text-dark mt-4">1. Ketentuan Akun Pengguna</h6>
                            <p>Pengguna wajib menggunakan identitas asli yang divalidasi dengan NIM Telkom University yang masih aktif. Satu NIM hanya diperkenankan untuk mendaftarkan satu akun. Anda bertanggung jawab penuh atas keamanan kata sandi akun Anda.</p>
                            
                            <h6 class="fw-bold text-dark mt-4">2. Tanggung Jawab Penjual</h6>
                            <p>Setiap penjual diwajibkan untuk mendeskripsikan kondisi barang secara jujur, detail, dan transparan, terutama jika barang memiliki kecacatan (minus). Foto yang diunggah haruslah foto asli barang (real pict).</p>
                            
                            <h6 class="fw-bold text-dark mt-4">3. Kewajiban Pembeli</h6>
                            <p>Pembeli diwajibkan untuk menyelesaikan pembayaran sesuai dengan kesepakatan nominal dan metode yang telah disetujui bersama penjual. Pembatalan pesanan secara sepihak tanpa alasan yang jelas dapat mengakibatkan peringatan.</p>
                            
                            <h6 class="fw-bold text-dark mt-4">4. Penyelesaian Sengketa</h6>
                            <p>Telcopedia memposisikan diri sebagai platform perantara. Jika terjadi sengketa (misalnya barang tidak sesuai deskripsi), masalah diselesaikan secara kekeluargaan terlebih dahulu. Tim Admin dapat membantu mediasi jika sangat diperlukan.</p>
                        </div>
                    </div>

                    <!-- PRIVACY TAB -->
                    <div class="tab-pane fade" id="v-pills-privacy" role="tabpanel">
                        <h4 class="fw-bold mb-4 border-bottom pb-3">Kebijakan Privasi</h4>
                        <div class="text-muted lh-lg">
                            <p class="mb-4">Kami sangat menghargai privasi dan keamanan data seluruh civitas akademika. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan dengan ketat melindungi informasi pribadi Anda.</p>
                            
                            <h6 class="fw-bold text-dark mt-4">Data yang Kami Kumpulkan</h6>
                            <p>Untuk mengoperasikan platform ini, kami mengumpulkan informasi dasar meliputi: nama lengkap, Nomor Induk Mahasiswa (NIM), alamat email, dan nomor WhatsApp. Pengumpulan ini semata-mata untuk keperluan verifikasi identitas internal.</p>
                            
                            <h6 class="fw-bold text-dark mt-4">Bagaimana Data Digunakan?</h6>
                            <p>Data kontak seperti nomor WhatsApp hanya akan diungkapkan kepada pihak pembeli atau penjual <strong>setelah</strong> sebuah transaksi disepakati di dalam sistem. Hal ini mencegah penyalahgunaan data untuk *spam*.</p>
                            
                            <h6 class="fw-bold text-dark mt-4">Keamanan Tingkat Lanjut</h6>
                            <p>Kami menjamin tidak akan pernah menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga atau entitas komersial di luar lingkup internal Telkom University.</p>
                        </div>
                    </div>

                    <!-- CONTACT TAB -->
                    <div class="tab-pane fade" id="v-pills-contact" role="tabpanel">
                        <h4 class="fw-bold mb-4 border-bottom pb-3">Hubungi Tim Kami</h4>
                        <p class="text-muted mb-4">Punya pertanyaan khusus, kendala teknis, atau laporan kecurangan? Hubungi kami lewat kontak di bawah ini.</p>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="card border border-light-subtle shadow-sm h-100 p-4 text-center">
                                    <div class="mb-3">
                                        <i class="fa-brands fa-whatsapp text-success" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">WhatsApp Bantuan</h6>
                                    <p class="small text-muted mb-3">Senin - Jumat, 08:00 - 17:00</p>
                                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success w-100 rounded-pill">Chat Admin</a>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border border-light-subtle shadow-sm h-100 p-4 text-center">
                                    <div class="mb-3">
                                        <i class="fa-regular fa-envelope text-maroon" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Email Support</h6>
                                    <p class="small text-muted mb-3">Balasan dalam waktu 1x24 Jam</p>
                                    <a href="mailto:support@telcopedia.id" class="btn btn-outline-maroon w-100 rounded-pill">Tulis Email</a>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <div class="bg-light p-4 rounded-3 text-center">
                                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-location-dot text-maroon me-2"></i>Pusat Operasional</h6>
                                    <p class="small text-muted mb-0">Bandung, Jawa Barat</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
