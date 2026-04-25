@extends('layouts.app')
@section('title', 'Admin Dashboard - Telcopedia')

@push('styles')
<style>
    .admin-header { background: #1a1a1a; color: white; padding: 40px 0; border-bottom: 5px solid #9F1521; }
    .stat-card { transition: 0.3s; border: none; overflow: hidden; border-radius: 15px; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .btn-maroon { background: #9F1521; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; }
    .btn-maroon:hover { background: #7c111b; color: white; }
    .action-quick { background: #fdfdfd; border: 1px dashed #ddd; border-radius: 15px; padding: 30px; text-align: center; }
</style>
@endpush

@section('content')
@section('hero_title', 'Dashboard Admin Telcopedia')
@section('hero_subtitle', 'Pusat kontrol ekosistem Telcopedia.')
@section('hero_emoji', '')
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-dark border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-3 me-3 text-secondary shadow-sm" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-users fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 x-small fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">TOTAL USER</p>
                            <h4 class="fw-900 mb-0">{{ number_format($stats['total_users']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-maroon border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-maroon-soft rounded-circle p-3 me-3 text-maroon shadow-sm" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-coins fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 x-small fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">REVENUE ADMIN</p>
                            <h4 class="fw-900 mb-0">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-management shadow-sm h-100 bg-white border-0 border-bottom border-success border-3 hover-translate-y transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle rounded-circle p-3 me-3 text-success shadow-sm" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-box-open fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 x-small fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">TOTAL PRODUK</p>
                            <h4 class="fw-900 mb-0">{{ number_format($stats['total_products']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ANALYTICS CHART -->
    <div class="card card-management shadow-sm border-0 bg-white mb-5">
        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">Grafik Pertumbuhan Transaksi</h6>
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-pill px-3 border" type="button">
                    30 Hari Terakhir <i class="fa fa-chevron-down ms-1 small"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-4">
            <div style="height: 350px; width: 100%;">
                <canvas id="adminChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxAdmin = document.getElementById('adminChart').getContext('2d');
        new Chart(ctxAdmin, {
            type: 'bar',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [{
                    label: 'Volume Transaksi',
                    data: [45, 82, 60, 110],
                    backgroundColor: '#9F1521',
                    borderRadius: 10,
                    barThickness: 40,
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
                        grid: { drawBorder: false, color: '#f0f0f0' }
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
