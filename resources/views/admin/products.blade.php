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
                                    @if($p->status === 'pending' || $p->status === 'rejected' || $p->status === 'inactive')
                                        <form action="{{ route('admin.products.approve', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" title="Setujui Produk">
                                                Setujui
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($p->status === 'pending' || $p->status === 'approved' || $p->status === 'active')
                                        <form action="{{ route('admin.products.reject', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" title="Tolak Produk">
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button class="btn btn-sm btn-light text-dark rounded-circle border shadow-sm" style="width: 32px; height: 32px;" 
                                            data-bs-toggle="modal" data-bs-target="#modalDetail{{ $p->id }}" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </button>

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

                        <!-- Modal Detail -->
                        <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-24 shadow-lg">
                                    <div class="modal-header border-0 p-4 pb-0">
                                        <h5 class="fw-900 mb-0">Detail Produk Screening</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-4">
                                            <div class="col-md-5">
                                                <img src="{{ $p->image_url }}" class="img-fluid rounded-20 shadow-sm border w-100 object-fit-cover" style="height: 300px;">
                                                
                                                <div class="mt-3 d-flex gap-2 overflow-x-auto pb-2">
                                                    @foreach($p->images as $img)
                                                        <img src="{{ asset('storage/' . $img->image_url) }}" class="rounded-8 border object-fit-cover" width="60" height="60">
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <span class="badge-status bg-maroon-soft text-maroon border border-maroon mb-2">{{ strtoupper($p->category->name ?? 'UNCATEGORIZED') }}</span>
                                                    <h4 class="fw-900 text-dark mb-1">{{ $p->name }}</h4>
                                                    <div class="text-maroon fw-800 fs-5 mb-3">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                                </div>

                                                <div class="bg-light p-3 rounded-15 mb-3 border">
                                                    <div class="row text-center g-2">
                                                        <div class="col-6 border-end">
                                                            <div class="x-small text-muted mb-1">KONDISI</div>
                                                            <div class="fw-bold text-dark">{{ strtoupper($p->condition) }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="x-small text-muted mb-1">STOK</div>
                                                            <div class="fw-bold text-dark">{{ $p->stock }} Unit</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <div class="x-small text-muted mb-2">DESKRIPSI PRODUK</div>
                                                    <div class="text-secondary small" style="line-height: 1.6;">
                                                        {!! nl2br(e($p->description)) !!}
                                                    </div>
                                                </div>

                                                <div class="p-3 bg-maroon-soft rounded-15 d-flex align-items-center mb-4 border border-maroon border-opacity-10">
                                                    <img src="{{ $p->seller->photo ? asset('storage/' . $p->seller->photo) : 'https://ui-avatars.com/api/?name='.urlencode($p->seller->name).'&background=9F1521&color=fff' }}" 
                                                         class="rounded-circle me-3 border" width="45" height="45">
                                                    <div>
                                                        <div class="x-small text-muted opacity-75">DATA PENJUAL</div>
                                                        <div class="fw-bold text-dark">{{ $p->seller->name }}</div>
                                                        <div class="x-small text-maroon">{{ $p->seller->nim }} | {{ $p->seller->phone }}</div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2 pt-3 border-top">
                                                    @if($p->status === 'pending' || $p->status === 'rejected')
                                                        <form action="{{ route('admin.products.approve', $p->id) }}" method="POST" class="flex-grow-1">
                                                            @csrf
                                                            <button class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm">
                                                                <i class="fa fa-check-circle me-2"></i>SETUJUI PRODUK
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($p->status === 'pending' || $p->status === 'approved' || $p->status === 'active')
                                                        <form action="{{ route('admin.products.reject', $p->id) }}" method="POST" class="flex-grow-1">
                                                            @csrf
                                                            <button class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold">
                                                                <i class="fa fa-times-circle me-2"></i>TOLAK PRODUK
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
