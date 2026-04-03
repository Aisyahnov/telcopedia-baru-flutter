@extends('layouts.app')
@section('title', 'Moderasi Produk - Admin')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0"><i class="fa fa-boxes-packing me-2 text-danger"></i> Moderasi Produk</h4>
            <p class="text-white-50 mb-0 small">Audit dan takedown produk yang melanggar ketentuan kampus.</p>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.admin_sidebar')
        </div>

        <!-- MAIN TABLE -->
        <div class="col-lg-9">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0 bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">DETAIL BARANG</th>
                                <th class="py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">LAPAK/PENJUAL</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">HARGA & STOK</th>
                                <th class="text-end pe-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">MODERASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $p)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-3 p-1 me-3 border shadow-sm">
                                                @if($p->image)
                                                    <img src="{{ asset('storage/' . $p->image) }}" width="45" height="45" class="rounded-2 object-fit-cover shadow-sm">
                                                @else
                                                    <div class="bg-white rounded-2 d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;">
                                                        <i class="fa fa-box text-muted opacity-50"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $p->name }}</div>
                                                <span class="badge bg-light text-muted border x-small d-inline-block mt-1">{{ $p->category->name ?? 'Uncategorized' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="small fw-bold text-dark"><i class="fa fa-shop text-muted me-1"></i> {{ $p->seller->name ?? 'No Seller' }}</div>
                                        <div class="x-small text-muted">Join: {{ $p->seller->created_at->format('M Y') ?? '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-danger">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                        <div class="x-small text-muted fw-bold">STOK: {{ $p->stock }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Takedown produk ilegal ini dari publik?')">
                                                <i class="fa fa-trash-alt me-1"></i> Takedown
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection
