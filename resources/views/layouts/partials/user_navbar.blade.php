<!-- TOP BAR DIPINDAHKAN KE FOOTER -->

<!-- MAIN NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-2">
  <div class="container align-items-center">
    
    <!-- BRAND (LOGO) -->
    <a class="navbar-brand me-4" href="{{ route('home') }}">
      <strong class="text-danger fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">Telcopedia</strong>
    </a>
    
    <!-- MOBILE TOGGLE BUTTON -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      
      <!-- KATEGORI DROPDOWN -->
      <div class="nav-item dropdown me-3 d-none d-lg-block">
          <a class="nav-link dropdown-toggle text-dark rounded px-3 py-2 bg-light" href="#" role="button" data-bs-toggle="dropdown" style="font-size: 0.95rem;">
            Kategori
          </a>
          <ul class="dropdown-menu shadow-sm border-0">
            @foreach(\App\Models\Category::all() as $cat)
              <li><a class="dropdown-item" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
            @endforeach
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item fw-bold text-danger" href="{{ route('category.index') }}">Semua Kategori <i class="fa fa-arrow-right ms-1 small"></i></a></li>
          </ul>
      </div>

      <!-- SEARCH BAR WIDESCREEN -->
      <form action="{{ route('home') }}" method="GET" class="d-flex flex-grow-1 me-4 my-3 my-lg-0">
        <div class="input-group">
            <input name="keyword" value="{{ request('keyword') }}" class="form-control border-danger-subtle shadow-none py-2" type="search" placeholder="Cari di Telcopedia..." style="font-size: 0.95rem;">
            <button class="btn btn-danger px-4 shadow-none" type="submit"><i class="fa fa-search"></i></button>
        </div>
      </form>

      <!-- ICONS AREA -->
      <ul class="navbar-nav align-items-center mb-2 mb-lg-0 flex-row justify-content-center">
        @if(Auth::check() && Auth::user()->role !== 'admin')
            <!-- CART WITH BADGE -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark position-relative hover-danger" href="{{ route('cart.index') }}" title="Keranjang">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                    @php 
                      $cartCount = \App\Models\CartItem::whereHas('cart', function($q) { $q->where('user_id', auth()->id()); })->count(); 
                    @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- PESAN/CHAT -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark hover-danger" href="{{ route('chat.index') }}" title="Pesan">
                    <i class="fa-solid fa-envelope fs-5"></i>
                </a>
            </li>

            <!-- FAVORITE -->
            <li class="nav-item me-4 me-lg-3">
                <a class="nav-link text-dark hover-danger" href="{{ route('favorites.index') ?? '#' }}" title="Wishlist">
                    <i class="fa-solid fa-heart fs-5"></i>
                </a>
            </li>

            <!-- VOUCHER -->
            <li class="nav-item me-lg-3">
                <a class="nav-link text-dark hover-danger" href="{{ url('/vouchers') }}" title="Voucher">
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
                <li class="px-3 py-3 border-bottom d-flex align-items-center mb-2 bg-light rounded-top">
                    <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" class="rounded-circle shadow-sm me-3 object-fit-cover" width="50" height="50">
                    <div>
                        <div class="fw-bold text-dark lh-sm text-truncate" style="max-width: 170px;">{{ Auth::user()->name }}</div>
                        <span class="badge bg-danger mt-1 text-uppercase border" style="font-size: 0.65rem;">{{ Auth::user()->role }} Account</span>
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
                    <button class="btn btn-outline-danger w-100 fw-bold rounded-pill">Keluar</button>
                  </form>
                </li>
              </ul>
            </li>
        @else
            <!-- Guest Buttons -->
            <div class="d-flex w-100 mt-3 mt-lg-0 gap-2">
                <li class="nav-item flex-grow-1 flex-lg-grow-0">
                    <a class="btn btn-outline-danger w-100 px-4 fw-bold" href="{{ route('login.form') }}">Masuk</a>
                </li>
                <li class="nav-item flex-grow-1 flex-lg-grow-0">
                    <a class="btn btn-danger w-100 px-4 fw-bold" href="{{ route('register.form') }}">Daftar</a>
                </li>
            </div>
        @endif
      </ul>

    </div>
  </div>
</nav>

<style>
/* Custom Navbar Overrides Khusus Desain Baru */
.hover-danger { transition: color 0.15s ease-in-out; }
.hover-danger:hover { color: var(--telkom-red) !important; }
.w-15px { width: 18px; text-align: center; }
.form-control:focus { border-color: var(--telkom-red); box-shadow: 0 0 0 0.25rem rgba(159, 21, 33, 0.25); }
.border-danger-subtle { border-color: #f5c2c7 !important; }
.input-group .btn { z-index: 0; }
</style>
