@extends('layouts.app')
@section('title', 'Syarat & Ketentuan - Telcopedia')

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
    .term-item { padding: 25px; border-radius: 20px; background: #fafafa; border: 1px solid #f0f0f0; margin-bottom: 20px; transition: 0.3s; }
    .term-item:hover { background: #fff; border-color: #9F1521; transform: translateX(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
    
    .term-number { 
        width: 35px; height: 35px; border-radius: 50%; 
        background: #9F1521; color: white; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 800; font-size: 0.9rem; margin-bottom: 15px;
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
        <h1 class="display-4 fw-900 mb-3">Syarat & <span class="text-white">Ketentuan</span></h1>
        <p class="lead opacity-90 mx-auto" style="max-width: 600px;">Aturan main menggunakan layanan Telcopedia agar tercipta ekosistem kampus yang sehat.</p>
    </div>
</div>

<div class="container mb-5 pb-5 fade-in-up">
    <div class="legal-card p-4 p-md-5">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">01</div>
                    <h5 class="fw-800 text-dark mb-3">Keanggotaan Mahasiswa</h5>
                    <p class="text-muted small lh-lg">
                        Layanan Telcopedia hanya dapat diakses oleh mahasiswa aktif Telkom University yang memiliki email student resmi. Setiap pengguna wajib melakukan verifikasi akun sebelum melakukan transaksi.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">02</div>
                    <h5 class="fw-800 text-dark mb-3">Kualitas Barang</h5>
                    <p class="text-muted small lh-lg">
                        Penjual bertanggung jawab penuh atas keakuratan deskripsi dan foto produk. Barang yang dijual harus merupakan barang milik pribadi dan tidak melanggar hukum atau peraturan universitas.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">03</div>
                    <h5 class="fw-800 text-dark mb-3">Sistem Pembayaran</h5>
                    <p class="text-muted small lh-lg">
                        Pembayaran dilakukan melalui sistem transfer yang disediakan platform atau COD. Untuk keamanan, dana akan ditahan oleh sistem hingga pembeli mengonfirmasi penerimaan barang.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">04</div>
                    <h5 class="fw-800 text-dark mb-3">Kebijakan Retur</h5>
                    <p class="text-muted small lh-lg">
                        Retur barang hanya diperbolehkan jika barang tidak sesuai dengan deskripsi atau terdapat cacat yang tidak diinfokan sebelumnya. Pengajuan harus disertai video unboxing/pemeriksaan saat COD.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">05</div>
                    <h5 class="fw-800 text-dark mb-3">Larangan Penipuan</h5>
                    <p class="text-muted small lh-lg">
                        Segala bentuk penipuan, spam, atau penyalahgunaan platform akan ditindak tegas dengan pemblokiran akun permanen dan pelaporan ke pihak kemahasiswaan universitas.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="term-item h-100">
                    <div class="term-number">06</div>
                    <h5 class="fw-800 text-dark mb-3">Perubahan Layanan</h5>
                    <p class="text-muted small lh-lg">
                        Telcopedia berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan diinfokan melalui email atau notifikasi aplikasi agar pengguna tetap terupdate.
                    </p>
                </div>
            </div>
        </div>

        <div class="alert bg-maroon-soft border-maroon border-opacity-20 mt-5 rounded-4 p-4">
            <div class="d-flex align-items-center">
                <div class="me-3 fs-3 text-maroon"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div>
                    <h6 class="fw-800 mb-1">Penting untuk Diingat!</h6>
                    <p class="small mb-0 opacity-75">Dengan mendaftar dan menggunakan layanan Telcopedia, Anda dianggap telah menyetujui seluruh butir Syarat & Ketentuan di atas tanpa kecuali.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
