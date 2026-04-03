@extends('layouts.app')
@section('title', 'Seller Center - Telcopedia')

@push('styles')
<style>
    .seller-header { background: #1a1a1a; color: white; padding: 40px 0; border-bottom: 5px solid #9F1521; }
    .stat-card { transition: 0.3s; border: none; overflow: hidden; border-radius: 15px; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .btn-maroon { background: #9F1521; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; }
    .btn-maroon:hover { background: #7c111b; color: white; }
    .action-quick { background: #fdfdfd; border: 1px dashed #ddd; border-radius: 15px; padding: 30px; text-align: center; }
</style>
@endpush

@section('content')
<!-- SELLER HERO/HEADER -->
<div class="seller-header mb-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">Pusat Seller Telcopedia 👋</h2>
        <p class="opacity-75 mb-0">Kelola operasional dan pantau performa lapak Anda di satu tempat.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU PARTIAL -->
        <div class="col-lg-3">
            @include('layouts.partials.seller_sidebar')
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-lg-9">
            
            <!-- STATISTIC TILES -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100 border">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="stat-icon bg-danger text-white me-3 shadow-sm" style="background: #9F1521 !important;">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $totalProducts }}</h4>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Produk Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100 border opacity-75">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="stat-icon bg-warning text-white me-3 shadow-sm">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">0</h4>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Pesanan Berjalan</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100 border opacity-75">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="stat-icon bg-success text-white me-3 shadow-sm">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">Rp 0</h4>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Total Saldo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTION AREA -->
            <div class="card shadow-sm border rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bolt-lightning text-warning me-2"></i> Akses Kilat</h6>
                    <a href="{{ route('seller.products.create') }}" class="btn btn-maroon btn-sm px-4 rounded-pill">
                        <i class="fa fa-plus me-1"></i> Tambah Produk Baru
                    </a>
                </div>
                <div class="card-body p-5 bg-white">
                    <div class="action-quick">
                        <div class="mb-4">
                            <i class="fa-solid fa-store-slash fa-4x text-muted opacity-25"></i>
                        </div>
                        <h4 class="fw-bold">Belum ada pesanan masuk?</h4>
                        <p class="text-muted max-width-500 mx-auto">
                            Bagikan link lapak Anda ke grup kelas atau media sosial untuk mendapatkan calon pembeli pertama hari ini!
                        </p>
                        <div class="mt-4 gap-2 d-flex justify-content-center">
                            <a href="{{ route('seller.products.index') }}" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold">Kelola Produk</a>
                            <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Buka Etalase</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
