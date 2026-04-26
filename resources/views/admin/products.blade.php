@extends('layouts.app')
@section('title', 'Screening Produk - Admin')

@section('hero_subtitle', 'Audit dan takedown produk yang melanggar ketentuan kampus.')
@section('hero_emoji', '')

@section('content')
@section('content')
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-lg rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3">
                <i class="fa fa-check-circle fs-4 text-success"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0">Berhasil!</h6>
                <small class="text-muted">{{ session('success') }}</small>
            </div>
        </div>
    @endif

    <div class="card card-management">
        <div class="card-body p-0">
            <table class="table table-management table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">DETAIL BARANG</th>
                        <th>LAPAK/PENJUAL</th>
                        <th class="text-center">HARGA & STOK</th>
                        <th class="text-end pe-4">MODERASI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ $p->image_url }}" width="55" height="55" class="rounded-15 object-fit-cover shadow-sm border">
                                        @if($p->status === 'pending')
                                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle">
                                                <span class="visually-hidden">New Alert</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-900 text-dark mb-1">{{ $p->name }}</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge-status bg-light text-muted border">{{ strtoupper($p->category->name ?? 'UNCATEGORIZED') }}</span>
                                            @if($p->status === 'pending')
                                                <span class="badge-status bg-warning-subtle text-warning border border-warning">WAITING REVIEW</span>
                                            @elseif($p->status === 'approved' || $p->status === 'active')
                                                <span class="badge-status bg-success-subtle text-success border border-success">LIVE</span>
                                            @else
                                                <span class="badge-status bg-danger-subtle text-danger border border-danger">REJECTED</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="small fw-bold text-dark"><i class="fa fa-shop text-maroon me-1"></i> {{ $p->seller->name ?? 'No Seller' }}</div>
                                <div class="x-small text-muted">ID: #{{ $p->seller_id }}</div>
                            </td>
                            <td class="text-center">
                                <div class="fw-900 text-maroon">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                <div class="x-small text-muted fw-bold">STOCK: {{ $p->stock }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($p->status === 'pending')
                                        <form action="{{ route('admin.products.approve', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.products.reject', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger rounded-circle border shadow-sm" style="width: 32px; height: 32px;" onclick="return confirm('Takedown produk ini dari publik?')" title="Hapus Permanen">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

<style>
    .x-small { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection
