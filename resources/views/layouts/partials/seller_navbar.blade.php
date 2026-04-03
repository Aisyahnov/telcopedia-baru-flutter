<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top py-2" style="background: #1a1a1a !important;">
  <div class="container">
    
    <!-- BRAND (SELLER CENTER LOGO) -->
    <a class="navbar-brand d-flex align-items-center" href="{{ route('seller.dashboard') }}">
      <strong class="text-white fw-bold me-2" style="font-size: 1.5rem;">Telcopedia</strong>
    </a>
    
    <!-- MOBILE TOGGLE -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#sellerNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="sellerNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <!-- USER PROFILE DROPDOWN (MINIMAL) -->
        <li class="nav-item dropdown">
          <a class="nav-link p-0 d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
            <div class="pe-2 text-end d-none d-lg-block">
                <div class="text-white fw-bold small lh-1">{{ Auth::user()->name }}</div>
            </div>
            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=9F1521&color=fff&bold=true' }}" 
                 class="rounded-circle border border-secondary shadow-sm object-fit-cover" 
                 width="38" height="38" alt="Avatar">
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2" style="width: 220px; border-radius: 15px;">
            <li>
                <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center mb-1" href="{{ route('profile.index') }}">
                    <i class="fa-solid fa-user-gear me-3 opacity-50" style="width: 20px;"></i>
                    <span class="fw-semibold small">Pengaturan Akun</span>
                </a>
            </li>
            <li><hr class="dropdown-divider opacity-50"></li>
            <li>
              <form action="{{ route('logout') }}" method="POST" class="p-1">
                @csrf
                <button class="btn btn-outline-danger btn-sm w-100 fw-bold rounded-pill border-2 mt-1">
                    <i class="fa fa-sign-out-alt me-2"></i> Keluar
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>

  </div>
</nav>

<style>
.hover-white:hover { color: white !important; }
.x-small { font-size: 0.7rem; }
.dropdown-item:active { background-color: #9F1521; }
</style>
