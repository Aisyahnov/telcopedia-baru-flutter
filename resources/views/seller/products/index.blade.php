@extends('layouts.app')
@section('title', 'Kelola Produk - Telcopedia')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm" style="background: #1a1a1a !important;">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0"><i class="fa fa-box-open me-2 text-danger"></i> Daftar Inventaris</h4>
            <p class="text-white-50 mb-0 small">List semua produk yang Anda miliki di Telcopedia.</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="fa fa-plus me-1"></i> Tambah Produk
        </a>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.seller_sidebar')
        </div>

        <!-- MAIN TABLE -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted">
                                <th class="ps-4 py-3" style="font-size: 0.75rem; letter-spacing: 1px;">DETAIL PRODUK</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; letter-spacing: 1px;">KATEGORI</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; letter-spacing: 1px;">HARGA</th>
                                <th class="text-end pe-4 py-3" style="font-size: 0.75rem; letter-spacing: 1px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded-3 me-3 border" width="55" height="55" style="object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center border" style="width: 55px; height: 55px;">
                                                    <i class="fa fa-image text-muted opacity-50"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ $product->name }}</h6>
                                                <small class="text-muted">{{ $product->condition == 'new' ? 'Baru' : 'Pre-loved' }} • Stok: {{ $product->stock }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill px-3">{{ $product->category->name }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-danger">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm border shadow-sm rounded-circle p-0" style="width: 32px; height: 32px;" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li><a class="dropdown-item py-2" href="{{ route('seller.products.edit', $product->id) }}"><i class="fa fa-edit me-2 text-primary"></i> Edit</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item py-2 text-danger"><i class="fa fa-trash me-2"></i> Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fa fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                                            <p class="text-muted mb-0">Belum ada produk yang dijual.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
