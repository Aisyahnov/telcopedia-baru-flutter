@extends('layouts.app')
@section('title', 'Admin Dashboard - Telcopedia')

@section('content')
<div class="bg-dark text-white border-bottom shadow-sm">
    <div class="container py-5">
        <div class="d-flex align-items-center">
            <div class="bg-danger rounded-circle p-3 me-4 shadow" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                <i class="fa fa-shield-halved fa-2x text-white"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">🛡️ Dashboard Super Admin</h2>
                <p class="text-white-50 mb-0">Pusat kontrol dan moderasi ekosistem Telcopedia.</p>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        <!-- SIDENAV PINTASAN ADMIN -->
        <div class="col-md-3">
            @include('layouts.partials.admin_sidebar')
        </div>

        <!-- STATS & INFO -->
        <div class="col-md-9">
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-dark border-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="bg-light rounded-circle p-3 me-3 text-secondary shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-user-check fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">PENGGUNA</p>
                                <h4 class="fw-bold mb-0">Aktif</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-danger border-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="bg-light rounded-circle p-3 me-3 text-danger shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-money-bill-transfer fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">PENDING</p>
                                <h4 class="fw-bold mb-0">{{ \App\Models\Order::where('status', 'paid_verifying')->count() }} Bayar</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-bottom border-success border-3">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="bg-light rounded-circle p-3 me-3 text-success shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-boxes fa-lg"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small fw-bold">MODERASI</p>
                                <h4 class="fw-bold mb-0">{{ \App\Models\Product::count() }} Barang</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesanan Terbaru Tinjauan Cepat -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <i class="fa fa-server fa-5x text-dark opacity-10"></i>
                    </div>
                    <h4 class="fw-bold">Seluruh Sistem Berjalan Normal</h4>
                    <p class="text-muted mx-auto" style="max-width: 600px;">
                        Gunakan tab navigasi di sebelah kiri untuk mengawasi akun mahasiswa, meninjau pergerakan produk lapak, atau memverifikasi bukti pembayaran yang masuk.
                    </p>
                    <div class="mt-4 gap-2 d-flex justify-content-center">
                        <a href="{{ route('admin.payments') }}" class="btn btn-danger rounded-pill px-4 fw-bold">Cek Pembayaran</a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Database User</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
