@extends('layouts.app')
@section('title', 'Pesanan Masuk - Telcopedia')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm" style="background: #1a1a1a !important;">
    <div class="container">
        <h4 class="fw-bold mb-0"><i class="fa fa-clipboard-list me-2 text-danger"></i> Pesanan Masuk</h4>
        <p class="text-white-50 mb-0 small">Kelola pesanan dari pembeli dan update status pengiriman.</p>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.seller_sidebar')
        </div>

        <!-- MAIN ORDERS LIST -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted">
                                <th class="ps-4 py-3" style="font-size: 0.75rem; letter-spacing: 1px;">ID PESANAN</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; letter-spacing: 1px;">PEMBELI</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; letter-spacing: 1px;">TOTAL</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; letter-spacing: 1px;">STATUS</th>
                                <th class="text-end pe-4 py-3" style="font-size: 0.75rem; letter-spacing: 1px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="ps-4 py-3 fw-bold text-dark">#TPD-{{ $order->id }}</td>
                                    <td class="text-center">{{ $order->user->name }}</td>
                                    <td class="text-center fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3">Menunggu</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-info-subtle text-info border border-info rounded-pill px-3">Diproses</span>
                                        @elseif($order->status == 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Selesai</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <button class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}">Cek Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fa fa-receipt fa-4x text-muted opacity-25 mb-4 d-block mx-auto"></i>
                                            <h6 class="fw-bold mb-0">Belum ada pesanan masuk.</h6>
                                            <p class="text-muted small">Coba bagikan produk Anda di media sosial agar pembeli datang!</p>
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
