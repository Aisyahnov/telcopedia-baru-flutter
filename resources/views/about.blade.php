@extends('layouts.app')
@section('title', 'Tentang Kami - Telcopedia')

@push('styles')
<style>
    body { 
        background-color: #fcfcfc; 
        background-image: radial-gradient(rgba(159, 21, 33, 0.08) 1px, transparent 1px);
        background-size: 24px 24px;
    }
    
    /* Clean Hero with Subtle Ornaments */
    .about-hero {
        padding: 80px 0 120px;
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        position: absolute;
        top: 0; right: 0;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(159,21,33,0.03) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .hero-title {
        font-weight: 900;
        color: #111;
        letter-spacing: -1px;
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .text-maroon { color: #9F1521 !important; }
    .bg-maroon-soft { background-color: #fff5f5; }

    /* Image Styling */
    .about-img-main {
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 10px solid white;
    }

    /* Content Cards */
    .value-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        border: 1px solid #f0f0f0;
        height: 100%;
        transition: 0.3s ease;
    }
    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
        border-color: #ffe5e5;
    }
    
    .icon-box {
        width: 65px; height: 65px;
        border-radius: 18px;
        background: #fff5f5;
        color: #9F1521;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    /* Stats Section */
    .stats-container {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #f0f0f0;
        margin-top: -40px;
        position: relative;
        z-index: 10;
    }
    .stat-item {
        text-align: center;
        padding: 20px;
    }
    .stat-number {
        font-size: 3rem;
        font-weight: 900;
        color: #9F1521;
        line-height: 1;
        margin-bottom: 10px;
    }
    .stat-label {
        color: #666;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    /* Animations */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes floatAnimation {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    .animate-fade-up { opacity: 0; animation: fadeUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards; animation-play-state: paused; }
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    
    .float-img { animation: floatAnimation 6s infinite ease-in-out; }
    
    @media (max-width: 768px) {
        .stat-item { border-bottom: 1px solid #eee; padding: 15px; }
        .stat-item:last-child { border-bottom: none; }
        .hero-title { font-size: 2.2rem; }
    }
</style>
@endpush

@section('content')

<!-- HERO SECTION -->
<section class="about-hero text-center">
    <div class="container position-relative z-3 animate-fade-up">
        <h1 class="hero-title">Platform Jual Beli Khusus <br><span class="text-maroon">Mahasiswa Telkom</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px; line-height: 1.8;">
            Membangun ekosistem ekonomi yang aman, terpercaya, dan berkelanjutan di dalam lingkungan kampus Telkom University.
        </p>
    </div>
</section>

<!-- STATS SECTION -->
<section class="container animate-fade-up delay-1">
    <div class="stats-container">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-item border-end-md">
                    <div class="stat-number"><span class="counter" data-target="1000">0</span>+</div>
                    <div class="stat-label">Mahasiswa Bergabung</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item border-end-md">
                    <div class="stat-number"><span class="counter" data-target="500">0</span>+</div>
                    <div class="stat-label">Barang Preloved Terjual</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-number"><span class="counter" data-target="100">0</span>%</div>
                    <div class="stat-label">Pengguna Terverifikasi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STORY SECTION -->
<section class="container py-5 mt-4">
    <div class="row align-items-center g-5">
        <div class="col-lg-6 animate-fade-up">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1471&auto=format&fit=crop" class="img-fluid about-img-main float-img" alt="Kampus Telkom University">
        </div>
        <div class="col-lg-6 animate-fade-up delay-1">
            <div class="d-inline-block px-3 py-1 bg-maroon-soft text-maroon rounded-pill fw-bold small mb-3">Latar Belakang</div>
            <h2 class="fw-900 mb-4" style="letter-spacing: -0.5px;">Dari Mahasiswa, Oleh Mahasiswa, Untuk Mahasiswa.</h2>
            <p class="text-muted mb-4" style="line-height: 1.8;">
                Telcopedia hadir dari keresahan mahasiswa yang sering kesulitan mencari barang bekas berkualitas (seperti buku cetak, meja lipat, atau perlengkapan asrama) dengan harga miring dari kakak tingkat.
            </p>
            <p class="text-muted mb-4" style="line-height: 1.8;">
                Selain itu, platform luar seringkali rawan penipuan. Dengan mewajibkan penggunaan NIM aktif Telkom University untuk pendaftaran, Telcopedia menciptakan lingkungan transaksi jual-beli yang jauh lebih transparan dan aman.
            </p>
            
            <div class="d-flex align-items-center bg-white p-3 rounded-3 border mt-4 transition-transform hover-scale">
                <i class="fa-solid fa-shield-check text-success fs-4 me-3"></i>
                <div class="small text-muted">Didukung penuh oleh <strong>Mahasiswa Telkom University</strong> untuk menunjang kesejahteraan bersama.</div>
            </div>
        </div>
    </div>
</section>

<!-- VALUES SECTION -->
<section class="container py-5 mb-5">
    <div class="text-center mb-5 animate-fade-up">
        <h2 class="fw-900 text-dark">Kenapa Menggunakan Telcopedia?</h2>
        <p class="text-muted">Keuntungan bertransaksi di dalam platform eksklusif kampus.</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4 animate-fade-up delay-1">
            <div class="value-card">
                <div class="icon-box"><i class="fa-solid fa-user-shield"></i></div>
                <h5 class="fw-bold mb-3">Aman & Terverifikasi</h5>
                <p class="text-muted mb-0 small lh-lg">Tidak ada akun anonim. Setiap pembeli dan penjual adalah mahasiswa aktif yang datanya terverifikasi, sehingga meminimalisir tindak kejahatan.</p>
            </div>
        </div>
        <div class="col-lg-4 animate-fade-up delay-2">
            <div class="value-card">
                <div class="icon-box"><i class="fa-solid fa-motorcycle"></i></div>
                <h5 class="fw-bold mb-3">Mudah Bertemu (COD)</h5>
                <p class="text-muted mb-0 small lh-lg">Hemat ongkos kirim. Anda bisa langsung melakukan Cash on Delivery (COD) di sekitar lingkungan GKU, Fakultas, atau Asrama.</p>
            </div>
        </div>
        <div class="col-lg-4 animate-fade-up delay-3">
            <div class="value-card">
                <div class="icon-box"><i class="fa-solid fa-recycle"></i></div>
                <h5 class="fw-bold mb-3">Ekonomi Sirkular</h5>
                <p class="text-muted mb-0 small lh-lg">Bantu kurangi limbah dengan menjual barang yang tak lagi dipakai kepada mahasiswa baru yang membutuhkan dengan harga terjangkau.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="container mb-5 animate-fade-up">
    <div class="bg-maroon-soft border border-danger border-opacity-10 rounded-4 p-5 text-center position-relative overflow-hidden">
        <div style="position:absolute; top:-50px; left:-50px; width:200px; height:200px; background:rgba(159,21,33,0.05); border-radius:50%;" class="float-img"></div>
        <div style="position:absolute; bottom:-50px; right:-50px; width:150px; height:150px; background:rgba(159,21,33,0.05); border-radius:50%; animation-delay:1s;" class="float-img"></div>
        
        <div class="position-relative z-3">
            <h3 class="fw-900 text-maroon mb-3">Mulai Berjualan atau Berbelanja Sekarang!</h3>
            <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Jadilah bagian dari komunitas cerdas Telkom University dan manfaatkan barang-barang preloved berkualitas di sekitarmu.</p>
            <a href="{{ route('home') }}" class="btn btn-maroon px-5 py-3 rounded-pill fw-bold" style="transition:0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">Jelajahi Produk</a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                    
                    // Trigger counters if it's the stats section
                    if(entry.target.classList.contains('stats-container') || entry.target.querySelector('.counter')) {
                        runCounters();
                    }
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-fade-up').forEach(el => {
            observer.observe(el);
        });

        // Animated Counters
        let countersRan = false;
        function runCounters() {
            if(countersRan) return;
            countersRan = true;
            
            const counters = document.querySelectorAll('.counter');
            const speed = 100; 

            counters.forEach(counter => {
                const animate = () => {
                    const value = +counter.getAttribute('data-target');
                    const data = +counter.innerText;
                    const time = value / speed;
                    
                    if(data < value) {
                        counter.innerText = Math.ceil(data + time);
                        setTimeout(animate, 20);
                    } else {
                        counter.innerText = value;
                    }
                }
                animate();
            });
        }
    });
</script>
@endpush

@endsection
