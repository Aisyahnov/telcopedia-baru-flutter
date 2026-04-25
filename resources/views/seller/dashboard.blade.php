@extends('layouts.app')
@section('title', 'Seller Center - Telcopedia')

@section('hero_title', 'Pusat Seller Telcopedia')
@section('hero_subtitle', 'Kelola operasional dan pantau performa lapak Anda di satu tempat.')
@section('hero_emoji', '')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Ringkasan Utama</h5>
            <p class="text-muted small mb-0">Statistik performa lapak Anda hari ini.</p>
        </div>
        <div class="text-muted small">
            <i class="fa fa-calendar-day me-1"></i> {{ date('d M Y') }}
        </div>
    </div>

    @if(Auth::user()->is_banned_from_posting)
        <div class="alert alert-danger border-0 shadow-sm rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3 text-danger">
                <i class="fa fa-ban fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark mb-1">Peringatan: Lapak Dibekukan Sebagian</div>
                <div class="text-muted small">Anda telah mencapai batas maksimal poin penalti <b>({{ Auth::user()->penalty_points }} dari 3 retur disetujui)</b>. Akses Anda untuk memposting produk baru telah diblokir sementara waktu. Harap selesaikan pesanan yang masih berjalan dengan baik.</div>
            </div>
        </div>
    @endif

    <!-- STATISTIC TILES -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-maroon border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-maroon-soft p-3 rounded-circle text-maroon shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-box-archive fa-lg"></i>
                        </div>
                        <span class="badge bg-light text-muted x-small">STOK</span>
                    </div>
                    <h3 class="fw-900 mb-1 text-dark">{{ $totalProducts }}</h3>
                    <p class="text-muted fw-bold text-uppercase mb-0 x-small" style="font-size: 0.6rem; letter-spacing: 1px;">Produk Terdaftar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-primary border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-primary-subtle p-3 rounded-circle text-primary shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-cart-shopping fa-lg"></i>
                        </div>
                        <span class="badge bg-primary-subtle text-primary x-small">LIVE</span>
                    </div>
                    <h3 class="fw-900 mb-1 text-dark">0</h3>
                    <p class="text-muted fw-bold text-uppercase mb-0 x-small" style="font-size: 0.6rem; letter-spacing: 1px;">Pesanan Baru</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-success border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-success-subtle p-3 rounded-circle text-success shadow-sm" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-wallet fa-lg"></i>
                        </div>
                        <span class="badge bg-success-subtle text-success x-small">CAIR</span>
                    </div>
                    <h3 class="fw-900 mb-1 text-dark">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</h3>
                    <p class="text-muted fw-bold text-uppercase mb-0 x-small" style="font-size: 0.6rem; letter-spacing: 1px;">Saldo Tersedia</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PERFORMANCE CHART -->
    <div class="card card-management shadow-sm border-0 bg-white mb-5">
        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Grafik Penjualan Mingguan</h6>
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-pill px-3 border" type="button">
                    7 Hari Terakhir <i class="fa fa-chevron-down ms-1 small"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div style="height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [120000, 190000, 30000, 50000, 200000, 300000, 450000],
                    borderColor: '#9F1521',
                    backgroundColor: 'rgba(159, 21, 33, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#9F1521'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { drawBorder: false, color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection
