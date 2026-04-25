<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top py-2" style="background: #1a1a1a !important;">
  <div class="container">
    <!-- BRAND TEXT -->
    <a class="navbar-brand fw-900 tracking-tight" href="{{ Auth::user()->role == 'admin' ? route('admin.dashboard') : route('seller.dashboard') }}">
      <span class="text-white">TELCO</span><span class="text-maroon">PEDIA</span>
      <span class="badge bg-maroon-soft text-maroon ms-2 x-small border border-danger border-opacity-25">{{ strtoupper(Auth::user()->role) }}</span>
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
.fw-900 { font-weight: 900; }
.tracking-tight { letter-spacing: -1px; }
.text-maroon { color: #9F1521 !important; }
.bg-maroon-soft { background: rgba(159, 21, 33, 0.1); }
.hover-white:hover { color: white !important; }
.x-small { font-size: 0.7rem; }
.dropdown-item:active { background-color: #9F1521; }
</style>
