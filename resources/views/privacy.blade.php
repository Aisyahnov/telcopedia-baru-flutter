@extends('layouts.app')
@section('title', 'Kebijakan Privasi - Telcopedia')

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
    .legal-card { 
        border: none; 
        border-radius: 24px; 
        box-shadow: 0 20px 60px rgba(0,0,0,0.06); 
        background: #fff; 
        margin-top: -80px;
        position: relative;
        z-index: 5;
        overflow: hidden;
    }
    .privacy-section { border-bottom: 1px solid #f0f0f0; padding-bottom: 30px; margin-bottom: 30px; }
    .privacy-section:last-child { border-bottom: none; }
    
    .section-icon { 
        width: 45px; height: 45px; border-radius: 12px; 
        background: #fff5f5; color: #9F1521; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 1.2rem; margin-bottom: 15px;
    }
    
    .nav-privacy { position: sticky; top: 100px; }
    .nav-privacy-link { 
        display: block; 
        padding: 12px 20px; 
        color: #666; 
        text-decoration: none; 
        border-left: 3px solid transparent;
        transition: 0.3s;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .nav-privacy-link:hover, .nav-privacy-link.active { 
        color: #9F1521; 
        background: #fff5f5; 
        border-left-color: #9F1521; 
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
        <h1 class="display-4 fw-900 mb-3">Kebijakan <span class="text-white">Privasi</span></h1>
        <p class="lead opacity-90 mx-auto" style="max-width: 600px;">Bagaimana Telcopedia menjaga dan mengelola data pribadi mahasiswa dengan aman.</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <div class="nav-privacy">
                <h6 class="fw-800 text-dark mb-3 px-3 uppercase small letter-spacing-1">Navigasi</h6>
                <a href="#pengumpulan" class="nav-privacy-link active">Pengumpulan Data</a>
                <a href="#penggunaan" class="nav-privacy-link">Penggunaan Data</a>
                <a href="#keamanan" class="nav-privacy-link">Keamanan Data</a>
                <a href="#hak" class="nav-privacy-link">Hak Pengguna</a>
                <a href="#kontak" class="nav-privacy-link">Kontak Kami</a>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="legal-card p-4 p-md-5">
                <div id="pengumpulan" class="privacy-section">
                    <div class="section-icon"><i class="fa-solid fa-database"></i></div>
                    <h4 class="fw-800 text-dark mb-3">1. Pengumpulan Informasi</h4>
                    <p class="text-muted lh-lg">
                        Kami mengumpulkan informasi yang Anda berikan langsung kepada kami saat mendaftar akun Telcopedia. Informasi ini mencakup:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li>Identitas Pribadi (Nama lengkap, NIM, Fakultas/Prodi)</li>
                        <li>Informasi Kontak (Alamat email student.telkomuniversity.ac.id, nomor WhatsApp)</li>
                        <li>Data Profil (Foto profil, deskripsi singkat)</li>
                        <li>Informasi Transaksi (Riwayat pesanan, ulasan produk)</li>
                    </ul>
                </div>

                <div id="penggunaan" class="privacy-section">
                    <div class="section-icon"><i class="fa-solid fa-gear"></i></div>
                    <h4 class="fw-800 text-dark mb-3">2. Penggunaan Informasi</h4>
                    <p class="text-muted lh-lg">
                        Informasi yang kami kumpulkan digunakan untuk tujuan berikut:
                    </p>
                    <ul class="text-muted lh-lg">
                        <li>Memverifikasi bahwa pengguna adalah mahasiswa aktif Telkom University.</li>
                        <li>Memfasilitasi komunikasi antara pembeli dan penjual.</li>
                        <li>Mengelola akun dan menyediakan layanan dukungan pelanggan.</li>
                        <li>Meningkatkan fungsionalitas dan pengalaman pengguna di platform kami.</li>
                    </ul>
                </div>

                <div id="keamanan" class="privacy-section">
                    <div class="section-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h4 class="fw-800 text-dark mb-3">3. Keamanan Data</h4>
                    <p class="text-muted lh-lg">
                        Telcopedia berkomitmen untuk menjaga keamanan data Anda. Kami menggunakan enkripsi standar industri dan protokol keamanan server untuk mencegah akses yang tidak sah, pengungkapan, atau modifikasi data pribadi Anda. Namun, perlu diingat bahwa tidak ada metode transmisi melalui internet yang 100% aman.
                    </p>
                </div>

                <div id="hak" class="privacy-section">
                    <div class="section-icon"><i class="fa-solid fa-user-check"></i></div>
                    <h4 class="fw-800 text-dark mb-3">4. Hak Pengguna</h4>
                    <p class="text-muted lh-lg">
                        Anda memiliki hak untuk mengakses, memperbarui, atau menghapus informasi pribadi Anda kapan saja melalui pengaturan profil. Jika Anda ingin menghapus akun Anda secara permanen, silakan hubungi tim dukungan kami.
                    </p>
                </div>

                <div id="kontak" class="text-center mt-4">
                    <div class="p-4 bg-maroon-soft rounded-4 border border-maroon border-opacity-10">
                        <h6 class="fw-800 mb-2">Punya pertanyaan seputar privasi?</h6>
                        <p class="text-muted small mb-3">Tim data kami siap membantu menjelaskan bagaimana data Anda dikelola.</p>
                        <a href="mailto:privacy@telcopedia.id" class="text-maroon fw-bold text-decoration-none">
                            <i class="fa-solid fa-envelope-open-text me-2"></i>privacy@telcopedia.id
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
