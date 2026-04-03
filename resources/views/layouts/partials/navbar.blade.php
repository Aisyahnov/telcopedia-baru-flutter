<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
      <strong class="text-danger fs-4 fw-bold">Telcopedia</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">

        <form action="{{ route('home') }}" method="GET" class="d-flex ms-3 search-form" role="search" style="flex: 1; max-width: 600px;">
          <input name="keyword" class="form-control me-2" type="search" placeholder="Cari produk di Telcopedia..." value="{{ request('keyword') }}">
          <button class="btn btn-outline-danger" type="submit"><i class="fa fa-search"></i></button>
        </form>

        <li class="nav-item ms-lg-3"><a class="nav-link" href="#">About Us</a></li>
        
        @auth
          <li class="nav-item mx-2">
              <a class="nav-link text-dark position-relative" href="{{ route('cart.index') }}">
                  <i class="fa fa-shopping-cart fa-lg"></i>
              </a>
          </li>
          
          <li class="nav-item dropdown ms-2">
            <a class="nav-link dropdown-toggle font-weight-bold text-dark" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fa fa-user-circle fa-lg me-1"></i> {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
              <li class="dropdown-item-text text-muted small">Role: <strong>{{ ucfirst(Auth::user()->role) }}</strong></li>
              <li><hr class="dropdown-divider"></li>
              
              @if(Auth::user()->role === 'admin')
                <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="fa fa-gauge me-2 opacity-50"></i> Dashboard Admin</a></li>
              @elseif(Auth::user()->role === 'seller')
                <li><a class="dropdown-item py-2" href="{{ route('seller.dashboard') }}"><i class="fa fa-store me-2 opacity-50"></i> Dashboard Toko</a></li>
              @endif
              
              @if(Auth::user()->role !== 'admin')
                  <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="fa fa-box me-2 opacity-50"></i> Riwayat Belanja Saya</a></li>
              @endif
              
              <li><a class="dropdown-item py-2" href="{{ route('profile.index') }}"><i class="fa fa-user me-2 opacity-50"></i> Profil & Pengaturan</a></li>
              <li><a class="dropdown-item py-2" href="{{ route('chat.index') }}"><i class="fa fa-comments me-2 opacity-50"></i> Kotak Masuk</a></li>
              <li><hr class="dropdown-divider"></li>
              
              <li>
                <form action="{{ route('logout') }}" method="POST" class="px-3 pb-2 pt-1">
                  @csrf
                  <button class="btn btn-sm btn-outline-danger w-100">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-outline-secondary w-100" href="{{ route('login.form') }}">Login</a></li>
          <li class="nav-item ms-lg-2 mt-2 mt-lg-0"><a class="btn btn-danger w-100" href="{{ route('register.form') }}">Register</a></li>
        @endauth

      </ul>
    </div>
  </div>
</nav>
