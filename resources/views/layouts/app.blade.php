<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Telcopedia')</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @stack('styles')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

@if(!Request::is('login') && !Request::is('register'))
  @if(Request::is('seller*'))
    @include('layouts.partials.seller_admin_navbar')
  @elseif(Request::is('admin*'))
    @include('layouts.partials.seller_admin_navbar') {{-- Sementara gunakan seller_navbar untuk admin atau buat admin_navbar nanti --}}
  @else
    @include('layouts.partials.user_navbar')
  @endif
@endif

<main class="flex-grow-1">
  @yield('content')
</main>

@if(!Request::is('login') && !Request::is('register'))
  @include('layouts.partials.user_footer')
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
