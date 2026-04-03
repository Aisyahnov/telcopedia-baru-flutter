<div class="sidebar-menu shadow-sm border bg-white mb-4" style="border-radius: 15px; overflow: hidden;">
    <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
        <small class="text-uppercase fw-bold text-muted" style="letter-spacing: 1px; font-size: 0.7rem;">Menu Utama</small>
        <span class="badge bg-danger rounded-pill" style="font-size: 0.5rem;">Seller</span>
    </div>
    <div class="d-flex flex-column">
        <a href="{{ route('seller.dashboard') }}" class="sidebar-link text-decoration-none {{ Request::is('seller/dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line me-3" style="width: 20px;"></i> Ringkasan
        </a>
        <a href="{{ route('seller.products.index') }}" class="sidebar-link text-decoration-none {{ Request::is('seller/products*') ? 'active' : '' }}">
            <i class="fa-solid fa-box-archive me-3" style="width: 20px;"></i> Kelola Produk
        </a>
        <a href="{{ route('seller.orders.index') }}" class="sidebar-link text-decoration-none {{ Request::is('seller/orders*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt me-3" style="width: 20px;"></i> Pesanan Masuk
        </a>
        <a href="{{ route('chat.index') }}" class="sidebar-link text-decoration-none {{ Request::is('chat*') ? 'active' : '' }}">
            <i class="fa-solid fa-comments me-3" style="width: 20px;"></i> Chat Pembeli
        </a>
        <a href="{{ route('profile.index') }}" class="sidebar-link text-decoration-none {{ Request::is('profile*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear me-3" style="width: 20px;"></i> Profil Lapak
        </a>
    </div>
</div>

<style>
    .sidebar-link { padding: 14px 20px; color: #444; border-left: 4px solid transparent; transition: 0.25s; font-weight: 500; font-size: 0.9rem; display: block; }
    .sidebar-link:hover { background: #fdf2f2; color: #9F1521; border-left-color: #eee; }
    .sidebar-link.active { background: #fee2e2; color: #9F1521; border-left-color: #9F1521; font-weight: 700; }
</style>
