<div class="h-100 d-flex flex-column bg-white">
    <!-- SIDEBAR HEADER -->
    <div class="p-4 border-bottom bg-white text-dark">
        <div class="d-flex justify-content-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="Telcopedia Logo" style="height: 45px; width: auto;">
        </div>
        
        <!-- USER PROFILE (New, because Navbar is gone) -->
        <div class="d-flex align-items-center p-2 bg-light rounded-3 border shadow-sm">
            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" 
                 class="rounded-circle border me-2 object-fit-cover" 
                 width="32" height="32" alt="Avatar">
            <div class="overflow-hidden">
                <div class="text-dark fw-bold small text-truncate" style="max-width: 140px;">{{ Auth::user()->name }}</div>
                <div class="text-muted x-small" style="font-size: 0.6rem;">Master Admin</div>
            </div>
        </div>
    </div>

    <!-- SIDEBAR MENU -->
    <div class="flex-grow-1 py-4 overflow-y-auto">
        <div class="px-3 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">OVERVIEW</small>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high me-3"></i> Ringkasan Utama
        </a>
        <a href="{{ route('notifications.index') }}" class="sidebar-link {{ Request::is('notifications*') ? 'active' : '' }}">
            <i class="fa-solid fa-bell me-3"></i> Notifikasi
            @php $countNotif = auth()->user()->unreadNotifications->count(); @endphp
            @if($countNotif > 0)
                <span class="badge bg-danger ms-auto rounded-pill">{{ $countNotif }}</span>
            @endif
        </a>

        <div class="px-3 mt-4 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">TRANSAKSI & DANA</small>
        </div>
        <a href="{{ route('admin.payments') }}" class="sidebar-link {{ Request::is('admin/payments*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-bill-transfer me-3"></i> Kelola Pembayaran
        </a>
        <a href="{{ route('admin.penarikan.index') }}" class="sidebar-link {{ Request::is('admin/penarikan*') ? 'active' : '' }}">
            <i class="fa-solid fa-hand-holding-dollar me-3"></i> Persetujuan Dana
            @php $countWithdraw = \App\Models\PenarikanDana::where('status', 'pending')->count(); @endphp
            @if($countWithdraw > 0)
                <span class="badge bg-danger ms-auto rounded-pill">{{ $countWithdraw }}</span>
            @endif
        </a>

        <div class="px-3 mt-4 mb-2">
            <small class="x-small text-muted fw-bold ps-2" style="font-size: 0.6rem;">MANAGEMENT DATA</small>
        </div>
        <a href="{{ route('admin.users') }}" class="sidebar-link {{ Request::is('admin/users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear me-3"></i> Kelola User
        </a>
        <a href="{{ route('admin.products') }}" class="sidebar-link {{ Request::is('admin/products*') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-packing me-3"></i> Screening Produk
            @php $countPendingProd = \App\Models\Product::where('status', 'pending')->count(); @endphp
            @if($countPendingProd > 0)
                <span class="badge bg-warning text-dark ms-auto rounded-pill">{{ $countPendingProd }}</span>
            @endif
        </a>
        <a href="{{ route('admin.vouchers') }}" class="sidebar-link {{ Request::is('admin/vouchers*') ? 'active' : '' }}">
            <i class="fa-solid fa-ticket-simple me-3"></i> Kelola Voucher
        </a>
    </div>

    <!-- SIDEBAR FOOTER -->
    <div class="p-4 border-top bg-white mt-auto">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm w-100 rounded-pill fw-bold shadow-sm" style="background-color: #9F1521; color: white;">
                <i class="fa fa-sign-out-alt me-2"></i> KELUAR
            </button>
        </form>
    </div>
</div>
