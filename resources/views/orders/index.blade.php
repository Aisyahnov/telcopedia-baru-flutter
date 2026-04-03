@extends('layouts.app')
@section('title', 'Riwayat Pesanan - Telcopedia')

@section('content')
<div class="container my-5">
    
    <div class="mb-4 d-flex align-items-center">
        <h4 class="fw-bold mb-0">Riwayat Belanja Saya</h4>
    </div>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
            <div class="card-body py-5">
                <i class="fa fa-shopping-bag fa-4x text-muted opacity-25 mb-4"></i>
                <h5 class="fw-bold mb-3">Anda belum pernah berbelanja.</h5>
                <p class="text-muted mb-4">Ayo mulai cari barang kebutuhan kuliah yang menarik di katalog Telcopedia.</p>
                <a href="{{ route('home') }}" class="btn btn-danger px-5 rounded-pill shadow-sm">Mulai Belanja</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <!-- Order Header -->
                        <div class="bg-light px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold me-3">Belanja <span class="text-danger">#ORD-{{ $order->id }}</span></span>
                                <small class="text-muted"><i class="fa fa-calendar-alt me-1"></i> {{ $order->created_at->format('d M Y') }}</small>
                            </div>
                            <div>
                                <span class="badge 
                                    @if($order->status == 'pending_payment') bg-warning text-dark 
                                    @elseif($order->status == 'paid_verifying') bg-info text-white
                                    @elseif($order->status == 'processing') bg-primary
                                    @elseif($order->status == 'shipped') bg-info
                                    @elseif($order->status == 'completed') bg-success
                                    @else bg-secondary @endif 
                                    px-3 py-2 rounded-pill">
                                    @if($order->status == 'pending_payment') MENUNGGU PEMBAYARAN
                                    @elseif($order->status == 'paid_verifying') MENUNGGU VERIFIKASI
                                    @elseif($order->status == 'processing') DIPROSES
                                    @elseif($order->status == 'shipped') DIKIRIM / COD
                                    @elseif($order->status == 'completed') SELESAI
                                    @else {{ strtoupper($order->status) }} @endif
                                </span>
                            </div>
                        </div>

                        <!-- Order Items & Tracking -->
                        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                @foreach($order->items as $item)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fa fa-box-open text-muted me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                                            <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($order->shipping_address)
                                    <div class="mt-3 bg-light p-3 rounded-3 border-start border-4 border-maroon">
                                        <span class="small fw-bold text-dark d-block mb-1"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Lokasi Penyerahan / COD:</span>
                                        <p class="mb-0 small text-muted">{{ $order->shipping_address }}</p>
                                    </div>
                                @endif

                                @if($order->tracking_number)
                                    <div class="mt-2 bg-light p-2 rounded border border-warning d-inline-block">
                                        <span class="small fw-bold text-dark"><i class="fa fa-truck me-1"></i> Resi Paket (Jika ada):</span>
                                        <code class="ms-1 fs-6 text-danger">{{ $order->tracking_number }}</code>
                                    </div>
                                @endif
                            </div>

                            <div class="text-md-end mt-3 mt-md-0">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-md-end gap-3 mb-1 small text-muted">
                                        <span>Subtotal: Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                                        <span>Admin: Rp {{ number_format($order->admin_fee, 0, ',', '.') }}</span>
                                    </div>
                                    @if($order->discount_amount > 0)
                                        <div class="text-success small fw-bold mb-1">Potongan Voucher: - Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</div>
                                    @endif
                                    <p class="text-muted small mb-0">Total Tagihan Akhir</p>
                                    <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h4>
                                </div>
                                
                                <!-- Bukti Pembayaran (Modificator) -->
                                @if($order->status == 'pending_payment')
                                    @if($order->payment_proof)
                                        <div class="alert alert-success border-0 small mb-0 py-2">
                                            <i class="fa fa-clock me-1"></i> Bukti Terkirim. Tunggu Admin memverifikasi.
                                        </div>
                                    @else
                                        <!-- Form Upload Bukti (Direct Upload / Trivial Modal) -->
                                        <form action="{{ route('checkout.upload_bukti', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center justify-content-md-end">
                                            @csrf
                                            <input type="file" name="payment_proof" class="form-control form-control-sm w-auto me-2" required accept="image/*">
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">Upload Bukti</button>
                                        </form>
                                        <small class="text-muted mt-1 d-block">Mohon lampirkan struk transfer Anda.</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
