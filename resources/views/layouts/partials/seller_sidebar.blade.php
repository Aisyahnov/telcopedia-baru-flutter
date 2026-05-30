<div class="h-100 d-flex flex-column bg-white">
    <!-- SIDEBAR HEADER -->
    <div class="p-4 border-bottom bg-white">
        <div class="d-flex justify-content-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Telcopedia Logo" style="height: 45px; width: auto;">
        </div>
        
        <!-- USER PROFILE (New, because Navbar is gone) -->
        <div class="d-flex align-items-center p-2 bg-white rounded-3 border shadow-sm">
            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" 
                 class="rounded-circle border me-2 object-fit-cover" 
                 width="32" height="32" alt="Avatar">
            <div class="overflow-hidden">
                <div class="text-dark fw-bold small text-truncate" style="max-width: 140px;">{{ Auth::user()->name }}</div>
                <div class="text-muted x-small" style="font-size: 0.6rem;">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
    </div>

    <!-- SIDEBAR MENU -->
    <div class="flex-grow-1 py-4 overflow-y-auto">
        <div class="px-3 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">MENU UTAMA</small>
        </div>
        <a href="{{ route('seller.dashboard') }}" class="sidebar-link {{ Request::is('seller/dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie me-3"></i> Ringkasan Utama
        </a>
        <a href="{{ route('notifications.index') }}" class="sidebar-link {{ Request::is('notifications*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell me-3"></i> Notifikasi
            @php $countNotif = auth()->user()->unreadNotifications->count(); @endphp
            @if($countNotif > 0)
                <span class="badge bg-danger ms-auto rounded-pill">{{ $countNotif }}</span>
            @endif
        </a>
        <a href="{{ route('seller.products.index') }}" class="sidebar-link {{ Request::is('seller/products*') ? 'active' : '' }}">
            <i class="fa-solid fa-box-open me-3"></i> Kelola Produk
        </a>
        <a href="{{ route('seller.orders.index') }}" class="sidebar-link {{ Request::is('seller/orders*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt me-3"></i> Kelola Pesanan
        </a>
        <a href="{{ route('seller.returns.index') }}" class="sidebar-link {{ Request::is('seller/returns*') ? 'active' : '' }}">
            <i class="fa-solid fa-rotate-left me-3"></i> Retur & Komplain
        </a>
        
        <div class="px-3 mt-4 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">KOMUNIKASI</small>
        </div>
        <a href="{{ route('seller.chats') }}" class="sidebar-link {{ Request::is('seller/chats*') ? 'active' : '' }}">
            <i class="fa-solid fa-comments me-3"></i> Chat Pembeli
        </a>

        <div class="px-3 mt-4 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">PENGATURAN</small>
        </div>
        <a href="{{ route('seller.penarikan.index') }}" class="sidebar-link {{ Request::is('seller/penarikan*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet me-3"></i> Saldo & Penarikan
        </a>
        <a href="{{ route('profile.index') }}" class="sidebar-link {{ Request::is('profile*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear me-3"></i> Pengaturan Toko
        </a>
    </div>

    <!-- SIDEBAR FOOTER -->
    <div class="p-4 border-top bg-light mt-auto">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm w-100 rounded-pill fw-bold shadow-sm" style="background-color: #9F1521; color: white;">
                <i class="fa fa-sign-out-alt me-2"></i> KELUAR
            </button>
        </form>
    </div>
</div>
