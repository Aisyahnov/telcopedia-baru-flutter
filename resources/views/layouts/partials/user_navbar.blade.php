<!-- TOP BAR DIPINDAHKAN KE FOOTER -->

<!-- MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-2">
  <div class="container align-items-center">
    
    <!-- BRAND (LOGO) -->
    <a class="navbar-brand me-4" href="{{ route('home') }}">
      <img src="{{ asset('images/logo.png') }}" alt="Telcopedia Logo" style="height: 40px; object-fit: contain;">
    </a>
    
    <!-- MOBILE TOGGLE BUTTON -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      
      <!-- KATEGORI LINK -->
      <div class="nav-item me-3 d-none d-lg-block">
          <a class="nav-link text-dark rounded px-3 py-2 bg-light fw-bold hover-maroon" href="{{ route('category.index') }}" style="font-size: 0.95rem;">
            Semua Kategori
          </a>
      </div>

      <!-- SEARCH BAR WIDESCREEN -->
      @php
          $searchAction = url('/');
          $searchPlaceholder = 'Cari barang atau nama seller...';
          $currentRoute = Route::currentRouteName();

          if ($currentRoute === 'orders.index') {
              $searchAction = url('/orders');
              $searchPlaceholder = 'Cari ID pesanan atau nama barang...';
          } elseif ($currentRoute === 'vouchers.index') {
              $searchAction = url('/vouchers');
              $searchPlaceholder = 'Cari kode voucher diskon...';
          } elseif ($currentRoute === 'category.index') {
              $searchAction = url('/categories');
              $searchPlaceholder = 'Cari nama kategori...';
          }
      @endphp
      <form action="{{ $searchAction }}" method="GET" class="d-flex flex-grow-1 me-4 my-3 my-lg-0">
        <!-- Preserve filter query on order page if exists -->
        @if(request()->has('filter') && $currentRoute === 'orders.index')
            <input type="hidden" name="filter" value="{{ request('filter') }}">
        @endif
        <div class="input-group">
            <input name="keyword" value="{{ request('keyword') }}" class="form-control border-maroon border-opacity-25 shadow-none py-2 rounded-start-pill ps-4" type="search" placeholder="{{ $searchPlaceholder }}" style="font-size: 0.95rem;">
            <button class="btn btn-maroon px-4 shadow-none rounded-end-pill" type="submit"><i class="fa fa-search"></i></button>
        </div>
      </form>

      <!-- ICONS AREA -->
      <ul class="navbar-nav align-items-center mb-2 mb-lg-0 flex-row justify-content-center">
        @if(Auth::check() && Auth::user()->role !== 'admin')
            <!-- CART WITH BADGE -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark position-relative hover-maroon" href="{{ route('cart.index') }}" title="Keranjang">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    @php 
                      $cartCount = \App\Models\CartItem::whereHas('cart', function($q) { $q->where('user_id', auth()->id()); })->count(); 
                    @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-maroon" style="font-size: 0.65rem;">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- NOTIFICATIONS -->
            <li class="nav-item me-4 me-lg-3 dropdown">
                <a class="nav-link text-dark position-relative hover-maroon" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bell fs-5"></i>
                    @php 
                      $notifCount = auth()->user()->unreadNotifications->count(); 
                    @endphp
                    @if($notifCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-maroon" style="font-size: 0.65rem;">
                            {{ $notifCount > 99 ? '99+' : $notifCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0 overflow-hidden" style="width: 320px; border-radius: 15px;">
                    <li class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-dark">Notifikasi</span>
                        @if($notifCount > 0)
                            <a href="{{ route('notifications.read_all') }}" class="x-small text-maroon text-decoration-none fw-bold">Tandai Semua Dibaca</a>
                        @endif
                    </li>
                    <div style="max-height: 400px; overflow-y: auto;">
                        @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notif)
                            <li class="border-bottom {{ $notif->unread() ? 'bg-light' : '' }}">
                                <a class="dropdown-item py-3 px-3 d-flex align-items-start whitespace-normal position-relative" href="{{ route('notifications.read', $notif->id) }}">
                                    @if($notif->unread())
                                        <span class="position-absolute top-0 start-0 translate-middle p-1 bg-maroon border border-light rounded-circle" style="margin-left: 12px; margin-top: 12px;"></span>
                                    @endif
                                    <div class="me-3 mt-1">
                                        @php
                                            $icon = 'fa-bell text-secondary';
                                            if(($notif->data['type'] ?? '') == 'product') $icon = 'fa-box-open text-warning';
                                            if(($notif->data['type'] ?? '') == 'order') $icon = 'fa-shopping-cart text-success';
                                            if(($notif->data['type'] ?? '') == 'penarikan') $icon = 'fa-wallet text-primary';
                                        @endphp
                                        <i class="fa-solid {{ $icon }} fs-5"></i>
                                    </div>
                                    <div style="white-space: normal;">
                                        <div class="fw-bold text-dark small mb-1">{{ $notif->data['title'] ?? 'Notifikasi Baru' }}</div>
                                        <p class="text-muted mb-1" style="font-size: 0.78rem; line-height: 1.3;">{{ $notif->data['message'] ?? '' }}</p>
                                        <span class="text-muted x-small opacity-75">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-4 py-5 text-center text-muted">
                                <i class="fa-solid fa-bell-slash fs-1 opacity-25 mb-3"></i>
                                <p class="small mb-0">Belum ada notifikasi baru</p>
                            </li>
                        @endforelse
                    </div>
                    <li class="bg-light text-center border-top">
                        <a href="{{ route('notifications.index') }}" class="dropdown-item py-2 fw-bold text-maroon small">Lihat Semua Notifikasi</a>
                    </li>
                </ul>
            </li>

            <!-- PESAN/CHAT -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark hover-maroon" href="{{ route('chat.index') }}" title="Pesan">
                    <i class="fa-solid fa-envelope fs-5"></i>
                </a>
            </li>

            <!-- FAVORITE -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark hover-maroon" href="{{ route('favorites.index') ?? '#' }}" title="Wishlist">
                    <i class="fa-solid fa-heart fs-5"></i>
                </a>
            </li>

            <!-- VOUCHER -->
            <li class="nav-item me-lg-3">
                <a class="nav-link text-dark hover-maroon" href="{{ url('/vouchers') }}" title="Voucher">
                    <i class="fa-solid fa-ticket-simple fs-5"></i>
                </a>
            </li>
        @endif
      </ul>

      <!-- VERTICAL DIVIDER -->
      @if(Auth::check())
      <div class="vr mx-3 d-none d-lg-block bg-secondary opacity-25"></div>
      @endif

      <!-- USER PROFILE AREA -->
      <ul class="navbar-nav align-items-center">
        @if(Auth::check())
            <li class="nav-item dropdown">
              <a class="nav-link p-0 d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                <!-- Real Avatar Generator -->
                <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" class="rounded-circle border border-white shadow-sm object-fit-cover" width="38" height="38" alt="Avatar">
                <span class="ms-2 text-dark fw-bold d-none d-lg-block" style="font-size: 0.95rem;">{{ explode(' ', Auth::user()->name)[0] }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="width: 280px;">
                <!-- Profile Header -->
                <li class="px-3 py-3 border-bottom d-flex align-items-center mb-2 bg-maroon-soft rounded-top">
                    <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" class="rounded-circle shadow-sm me-3 object-fit-cover" width="50" height="50">
                    <div>
                        <div class="fw-bold text-dark lh-sm text-truncate" style="max-width: 170px;">{{ Auth::user()->name }}</div>
                        <span class="badge bg-maroon mt-1 text-uppercase border-0" style="font-size: 0.65rem;">{{ Auth::user()->role }} Account</span>
                    </div>
                </li>
                
                <!-- Menu Items -->
                @if(Auth::user()->role === 'admin')
                  <li><a class="dropdown-item py-2 fw-semibold text-secondary" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-line text-dark me-2 w-15px"></i> Dashboard Control</a></li>
                @endif

                @if(Auth::user()->role === 'seller')
                  <li><a class="dropdown-item py-2 fw-semibold text-secondary" href="{{ route('seller.dashboard') }}"><i class="fa-solid fa-store text-dark me-2 w-15px"></i> Kelola Toko</a></li>
                @endif
                
                @if(Auth::user()->role !== 'admin')
                  <li><a class="dropdown-item py-2 fw-semibold text-secondary" href="{{ url('/profile') }}"><i class="fa-solid fa-user-gear text-dark me-2 w-15px"></i> Pengaturan Akun</a></li>
                  <li><a class="dropdown-item py-2 fw-semibold text-secondary" href="{{ url('/orders') }}"><i class="fa-solid fa-clock-rotate-left text-dark me-2 w-15px"></i> Riwayat Belanja</a></li>
                @endif

                <li><hr class="dropdown-divider my-2"></li>
                
                <!-- Logout Button -->
                <li>
                  <form action="{{ route('logout') }}" method="POST" class="px-3 pb-2 pt-1">
                    @csrf
                    <button class="btn btn-outline-maroon w-100 fw-bold rounded-pill">Keluar</button>
                  </form>
                </li>
              </ul>
            </li>
        @else
            <!-- Guest Buttons -->
            <div class="d-flex w-100 mt-3 mt-lg-0 gap-2">
                <li class="nav-item flex-grow-1 flex-lg-grow-0">
                    <a class="btn btn-outline-maroon w-100 px-4 fw-bold" href="{{ route('login.form') }}">Masuk</a>
                </li>
                <li class="nav-item flex-grow-1 flex-lg-grow-0">
                    <a class="btn btn-maroon w-100 px-4 fw-bold" href="{{ route('register.form') }}">Daftar</a>
                </li>
            </div>
        @endif
      </ul>

    </div>
  </div>
</nav>

<style>
/* Custom Navbar Overrides Khusus Desain Baru */
.hover-maroon { transition: color 0.15s ease-in-out; }
.hover-maroon:hover { color: var(--telco-maroon) !important; }
.w-15px { width: 18px; text-align: center; }
.form-control:focus { border-color: var(--telco-maroon); box-shadow: 0 0 0 0.25rem rgba(159, 21, 33, 0.25); }
.border-maroon-subtle { border-color: #f5c2c7 !important; }
.input-group .btn { z-index: 0; }

/* Override Bootstrap Dropdown Blue (Agresif) */
.dropdown-item:hover {
    background-color: var(--telco-maroon-soft) !important;
    color: var(--telco-maroon) !important;
}
.dropdown-item:focus, .dropdown-item:active, .dropdown-item.active {
    background-color: var(--telco-maroon) !important;
    color: white !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>
