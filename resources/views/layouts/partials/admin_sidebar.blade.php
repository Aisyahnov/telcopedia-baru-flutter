<div class="sidebar-admin shadow-sm border bg-white mb-4" style="border-radius: 20px; overflow: hidden;">
    <div class="p-4 bg-dark text-white border-bottom d-flex align-items-center">
        <div class="bg-danger rounded-circle p-2 me-3 shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-shield-halved text-white"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Admin Panel</h6>
            <small class="opacity-50 x-small">Control Center</small>
        </div>
    </div>
    <div class="d-flex flex-column py-2">
        <a href="{{ route('admin.dashboard') }}" class="admin-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high me-3"></i> Ringkasan Utama
        </a>
        <a href="{{ route('admin.payments') }}" class="admin-link {{ Request::is('admin/payments*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-bill-transfer me-3"></i> Kelola Pembayaran
            @php $countPending = \App\Models\Order::where('status', 'paid_verifying')->count(); @endphp
            @if($countPending > 0)
                <span class="badge bg-danger ms-auto rounded-pill">{{ $countPending }}</span>
            @endif
        </a>
        <a href="{{ route('admin.users') }}" class="admin-link {{ Request::is('admin/users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear me-3"></i> Database Pengguna
        </a>
        <a href="{{ route('admin.products') }}" class="admin-link {{ Request::is('admin/products*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-packing me-3"></i> Moderasi Produk
        </a>
        <a href="{{ route('admin.vouchers') }}" class="admin-link {{ Request::is('admin/vouchers*') ? 'active' : '' }}">
            <i class="fa-solid fa-ticket-simple me-3"></i> Manajemen Voucher
        </a>
    </div>
</div>

<style>
    .admin-link { padding: 14px 20px; color: #555; transition: 0.3s; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; text-decoration: none; border-left: 5px solid transparent; }
    .admin-link:hover { background: #f8f9fa; color: #9F1521; }
    .admin-link.active { background: #fdf2f2; color: #9F1521; border-left-color: #9F1521; }
    .x-small { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
