<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Telcopedia')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --telco-maroon: #9F1521;
      --telco-maroon-soft: rgba(159, 21, 33, 0.05);
      --telco-gray-main: #F8F9FA;
      --telco-gray-border: #EEEEEE;
      --telco-dark: #1A1A1A;
    }
    body { font-family: 'Inter', sans-serif; background-color: var(--telco-gray-main); color: #333; }
    .text-maroon { color: var(--telco-maroon) !important; }
    .bg-maroon { background-color: var(--telco-maroon) !important; }
    .bg-maroon-soft { background-color: var(--telco-maroon-soft) !important; }
    .border-maroon { border-color: var(--telco-maroon) !important; }
    .bg-maroon-subtle { background-color: #fff5f5 !important; color: #9F1521 !important; border: 1px solid #fee2e2 !important; }
    .bg-info-subtle { background-color: #fef2f2 !important; color: #9F1521 !important; border: 1px solid #fee2e2 !important; } /* Ganti dari biru ke maroon-subtle */
    .bg-primary-subtle { background-color: #fff1f2 !important; color: #be123c !important; border: 1px solid #fecdd3 !important; } /* Ganti dari biru ke rose/maroon-soft */
    .bg-warning-subtle { background-color: #fffbeb !important; color: #b45309 !important; border: 1px solid #fef3c7 !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; color: #15803d !important; border: 1px solid #bbf7d0 !important; }
    .bg-secondary-subtle { background-color: #f9fafb !important; color: #4b5563 !important; border: 1px solid #f3f4f6 !important; }
    
    /* Global Focus Ring Maroon */
    *:focus {
        box-shadow: 0 0 0 0.25rem rgba(159, 21, 33, 0.25) !important;
        outline: none !important;
    }
    
    /* Global Management Styles */
    .card-management { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); overflow: hidden; }
    .badge-status { 
        font-size: 0.68rem; 
        padding: 6px 14px; 
        border-radius: 100px; 
        font-weight: 800; 
        letter-spacing: 0.5px; 
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .table-management thead th {
        background: #FDFDFD;
        color: #888;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1px;
        padding: 18px 15px;
        border-bottom: 1px solid #F0F0F0;
        text-transform: uppercase;
    }
    .table-management tbody td {
        padding: 20px 15px;
        vertical-align: middle;
    }
    .btn-maroon { 
        background: var(--telco-maroon); 
        color: white; 
        border: 1px solid var(--telco-maroon); 
        border-radius: 100px; 
        font-weight: 700; 
        padding: 10px 24px; 
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        background-color: var(--telco-maroon); 
        color: white; 
        border: none; 
        transition: 0.3s; 
    }
    .btn-maroon:hover { background-color: #8a121c; color: white; transform: translateY(-1px); }
    
    .btn-outline-maroon { 
        border: 2px solid var(--telco-maroon); 
        color: var(--telco-maroon); 
        background: transparent;
        transition: 0.3s;
    }
    .btn-outline-maroon:hover { background: var(--telco-maroon); color: white; }

    /* KILL BOOTSTRAP BLUE GLOBALLY */
    a, button, .btn, .form-control, .form-select, .dropdown-item {
        outline: none !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--telco-maroon) !important;
        box-shadow: 0 0 0 0.25rem rgba(159, 21, 33, 0.25) !important;
    }
    .btn:focus, a:focus {
        box-shadow: none !important;
    }
    .dropdown-item:active, .dropdown-item.active {
        background-color: var(--telco-maroon) !important;
        color: white !important;
    }
    .dropdown-item:hover, .dropdown-item:focus {
        background-color: var(--telco-maroon-soft) !important;
        color: var(--telco-maroon) !important;
    }
    .nav-link:focus, .nav-link:hover {
        color: var(--telco-maroon) !important;
    }
    .page-link { color: var(--telco-maroon); }
    .page-item.active .page-link {
        background-color: var(--telco-maroon);
        border-color: var(--telco-maroon);
    }
    .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(159, 21, 33, 0.25);
    }
    
    .rounded-8 { border-radius: 8px !important; }
    .rounded-12 { border-radius: 12px !important; }
    .rounded-15 { border-radius: 15px !important; }
    .rounded-20 { border-radius: 20px !important; }
    .fw-800 { font-weight: 800 !important; }
    .x-small { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Unified Sidebar Styles */
    .sidebar-container { border-radius: 24px; overflow: hidden; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #EEE; }
    .sidebar-link { 
        padding: 14px 25px; 
        color: #555; 
        border-left: 5px solid transparent; 
        transition: 0.3s all cubic-bezier(0.4, 0, 0.2, 1); 
        font-weight: 600; 
        font-size: 0.88rem; 
        display: flex;
        align-items: center;
        text-decoration: none;
    }
    .sidebar-link i { width: 22px; font-size: 1.1rem; opacity: 0.6; transition: 0.3s; }
    .sidebar-link:hover { background: #FCFCFC; color: var(--telco-maroon); border-left-color: #EEE; }
    .sidebar-link:hover i { opacity: 1; transform: scale(1.1); color: var(--telco-maroon); }
    .sidebar-link.active { background: var(--telco-maroon-soft); color: var(--telco-maroon); border-left-color: var(--telco-maroon); box-shadow: inset 0 0 10px rgba(159, 21, 33, 0.02); }
    .sidebar-link.active i { opacity: 1; color: var(--telco-maroon); transform: scale(1.1); }
    .fw-900 { font-weight: 900 !important; }
    .tracking-tighter { letter-spacing: -0.05em; }

    /* Animations */
    @keyframes pulse-blue {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
    @keyframes pulse-orange {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    .pulse-blue { animation: pulse-blue 2s infinite; }
    .pulse-orange { animation: pulse-orange 2s infinite; }
    .transition-all { transition: 0.3s all ease; }
    .hover-translate-y:hover { transform: translateY(-5px); }
  </style>
  @stack('styles')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

@if(!Request::is('login') && !Request::is('register'))
  @php
    $isSellerAdminArea = (Request::is('seller*') && !Request::routeIs('seller.profile')) || 
                        Request::is('admin*') || 
                        (Request::is('chat*') && Auth::check() && Auth::user()->role !== 'buyer') || 
                        (Request::is('profile*') && Auth::check() && Auth::user()->role !== 'buyer') ||
                        (Request::is('notifications*') && Auth::check() && Auth::user()->role !== 'buyer');
  @endphp
  
  @if($isSellerAdminArea)
    <div class="dashboard-wrapper">
        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar">
            @if(Auth::user()->role === 'admin')
                @include('layouts.partials.admin_sidebar')
            @else
                @include('layouts.partials.seller_sidebar')
            @endif
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="dashboard-main">
            {{-- NAVBAR REMOVED AS PER USER REQUEST --}}
            
            <!-- PAGE HEADER / HERO -->
            <div class="dashboard-header">
                <div class="container-fluid px-4 px-lg-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="fw-900 text-white mb-2 tracking-tight">
                                @yield('hero_title', Auth::user()->role === 'admin' ? 'Dashboard Control Admin' : 'Pusat Seller Telcopedia') 
                                @if(trim($__env->yieldContent('hero_emoji')))
                                    <span class="ms-2">@yield('hero_emoji')</span>
                                @endif
                            </h2>
                            <p class="text-white-50 mb-0 lead-sm">
                                @yield('hero_subtitle', Auth::user()->role === 'admin' ? 'Pantau aktivitas kampus dan moderasi produk secara menyeluruh.' : 'Kelola operasional dan pantau performa lapak Anda di satu tempat.')
                            </p>
                        </div>
                        @hasSection('hero_action')
                            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                                @yield('hero_action')
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <main class="dashboard-content p-4 p-lg-5">
                @yield('content')
            </main>
        </div>
    </div>
  @else
    @include('layouts.partials.user_navbar')
    <main class="flex-grow-1">
      @yield('content')
    </main>
    @include('layouts.partials.user_footer')
  @endif
@else
    <main class="flex-grow-1">
      @yield('content')
    </main>
@endif

<style>
    /* DASHBOARD LAYOUT STYLES */
    .dashboard-wrapper {
        display: flex;
        min-height: 100vh;
        background-color: var(--telco-gray-main);
    }
    .dashboard-sidebar {
        width: 280px;
        background: white;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        border-right: 1px solid #EEE;
        overflow-y: auto;
    }
    .dashboard-main {
        flex: 1;
        margin-left: 280px;
        display: flex;
        flex-direction: column;
    }
    .dashboard-header { 
        background: #1a1a1a; 
        color: white; 
        padding: 60px 0; 
        border-bottom: 4px solid var(--telco-maroon);
        background-image: radial-gradient(circle at 10% 20%, rgba(159, 21, 33, 0.2) 0%, transparent 40%),
                          radial-gradient(circle at 90% 80%, rgba(159, 21, 33, 0.15) 0%, transparent 40%);
        position: relative;
        overflow: hidden;
    }
    .dashboard-header::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('https://www.transparenttextures.com/patterns/grid-me.png');
        opacity: 0.05;
        pointer-events: none;
    }
    
    /* MOBILE ADJUSTMENTS */
    @media (max-width: 991.98px) {
        .dashboard-sidebar {
            transform: translateX(-100%);
            transition: 0.3s transform ease;
        }
        .dashboard-sidebar.show {
            transform: translateX(0);
        }
        .dashboard-main {
            margin-left: 0;
        }
    }

    .tracking-tight { letter-spacing: -0.025em; }
    .lead-sm { font-size: 1.1rem; font-weight: 500; opacity: 0.8; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
