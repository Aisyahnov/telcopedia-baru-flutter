@extends('layouts.app')
@section('title', 'Kelola Pesanan - Telcopedia')

@section('hero_title', 'Pesanan Masuk')
@section('hero_subtitle', 'Pantau pesanan masuk, verifikasi pembayaran, dan atur pengiriman barang.')
@section('hero_emoji', '')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Pesanan Masuk</h5>
            <p class="text-muted small mb-0">Total {{ count($orders) }} transaksi yang tercatat.</p>
        </div>
    </div>

    <div class="card card-management mb-5">
        <div class="card-body p-0">
            <table class="table table-management table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID PESANAN</th>
                        <th class="text-center">PEMBELI</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 py-4 fw-bold text-dark">
                                <div class="x-small text-muted mb-1">REFERENCE:</div>
                                <div class="fw-bold">#TPD-{{ $order->id }}</div>
                                <div class="x-small text-muted fw-normal mt-1">{{ $order->created_at->format('d M, H:i') }}</div>
                            </td>
                            <td class="text-center py-4">
                                <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                                <div class="x-small fw-bold {{ $order->payment_method == 'cod' ? 'text-success' : 'text-primary' }}">
                                    <i class="fa {{ $order->payment_method == 'cod' ? 'fa-handshake' : 'fa-building-columns' }} me-1"></i>
                                    {{ strtoupper($order->payment_method) }}
                                </div>
                            </td>
                            <td class="text-center py-4 fw-bold text-maroon">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-center py-4">
                                @if($order->status == 'pending_payment')
                                    <span class="badge-status bg-warning-subtle text-warning border border-warning">BELUM BAYAR</span>
                                @elseif($order->status == 'paid_verifying')
                                    <span class="badge-status bg-primary-subtle text-primary border border-primary pulse-blue">PERLU VERIFIKASI</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge-status bg-info-subtle text-info border border-info">DIPROSES</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge-status bg-success-subtle text-success border border-success">SELESAI</span>
                                @else
                                    <span class="badge-status bg-danger-subtle text-danger border border-danger">{{ strtoupper($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-4 text-nowrap">
                                <button class="btn btn-sm btn-maroon rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#orderDetail{{ $order->id }}">
                                    Cek Detail
                                </button>
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

    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>

    @foreach($orders as $order)
    <div class="modal fade" id="orderDetail{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-24 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-maroon">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-3 text-start">
                    <div class="bg-light rounded-15 p-3 mb-4 border border-dashed">
                        <div class="row g-3">
                            <div class="col-6">
                                <span class="x-small text-muted d-block mb-1">ID PESANAN</span>
                                <div class="fw-bold text-dark">#TPD-{{ $order->id }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <span class="x-small text-muted d-block mb-1">PEMBELI</span>
                                <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="x-small text-muted d-block mb-2">ALAMAT PENGIRIMAN / TITIK COD</span>
                        <div class="bg-white p-3 rounded-15 border text-dark shadow-sm" style="font-size: 0.9rem;">
                            <i class="fa fa-location-dot text-maroon me-2"></i> {{ $order->shipping_address ?? 'Tidak ada alamat' }}
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <span class="x-small text-muted d-block mb-1">METODE PEMBAYARAN</span>
                            <span class="badge-status bg-dark text-white border-0">{{ strtoupper($order->payment_method ?? 'TRANSFER') }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="x-small text-muted d-block mb-1">TOTAL PEMBAYARAN</span>
                            <div class="fw-900 text-maroon h5 mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    @if($order->payment_proof)
                    <div class="mb-4">
                        <span class="x-small text-muted d-block mb-2">BUKTI PEMBAYARAN</span>
                        <img src="{{ asset('storage/' . $order->payment_proof) }}" class="img-fluid rounded-20 border shadow-sm w-100" style="max-height: 250px; object-fit: cover;">
                    </div>
                    @endif

                    <div class="pt-2">
                    @if($order->status == 'paid_verifying')
                        <div class="d-flex gap-3">
                            <form action="{{ route('seller.payments.approve', $order->id) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-maroon w-100 rounded-pill py-3 shadow-sm fw-bold">
                                    <i class="fa fa-check-circle me-2"></i> {{ $order->payment_method == 'cod' ? 'Terima Pesanan' : 'Konfirmasi Bayar' }}
                                </button>
                            </form>
                            <form action="{{ route('seller.payments.reject', $order->id) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark w-100 rounded-pill py-3 fw-bold">
                                    {{ $order->payment_method == 'cod' ? 'Tolak / Batalkan' : 'Tolak' }}
                                </button>
                            </form>
                        </div>
                    @elseif($order->status == 'processing')
                        <form action="{{ route('seller.orders.tracking', $order->id) }}" method="POST" class="bg-light p-3 rounded-20 border mb-3">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="x-small text-muted d-block mb-2">
                                    {{ $order->payment_method == 'cod' ? 'Catatan Serah Terima' : 'Nomor Resi / Info Kurir' }}
                                </label>
                                <input type="text" name="tracking_number" class="form-control rounded-pill px-4 border-0 shadow-sm" value="{{ $order->tracking_number }}" placeholder="{{ $order->payment_method == 'cod' ? 'Serah terima di...' : 'RESI12345...' }}" required>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3 fw-bold">
                                {{ $order->payment_method == 'cod' ? 'Selesaikan Pesanan' : 'Update Tracking' }}
                            </button>
                        </form>
                        
                        <form action="{{ route('seller.payments.reject', $order->id) }}" method="POST" class="text-center">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger text-decoration-none small fw-bold">
                                <i class="fa fa-times-circle me-1"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection
