@extends('layouts.app')
@section('title', 'Keranjang Belanja - Telcopedia')

@push('styles')
<style>
    .cart-table thead { background: #f8f9fa; border-top: 2px solid #9F1521; }
    .cart-item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 1px solid #eee; }
    .qty-input { width: 70px; border-radius: 8px; text-align: center; font-weight: 600; }
    .summary-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 10px; font-weight: 600; transition: 0.3s; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: translateY(-2px); }
    .btn-outline-maroon { border: 2px solid #9F1521; color: #9F1521; border-radius: 10px; font-weight: 600; }
    .btn-outline-maroon:hover { background-color: #9F1521; color: white; }
    .empty-cart-icon { font-size: 5rem; color: #dee2e6; margin-bottom: 20px; }
</style>
@endpush

@section('content')

<!-- Body -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Keranjang Belanja</h4>
        <span class="text-muted">{{ $items->items->count() }} Produk terpilih</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        {{-- LEFT: CART ITEMS --}}
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm overflow-hidden border">
                @if($items->items->count() > 0)
                <table class="table cart-table align-middle mb-0">
                    <thead>
                        <tr class="text-muted small">
                            <th class="ps-4 py-3">PRODUK</th>
                            <th class="text-center py-3">KUANTITAS</th>
                            <th class="text-center py-3">HARGA</th>
                            <th class="text-end pe-4 py-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->items as $item)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($item->product->name) }}&background=f8f9fa&color=9F1521&size=100" class="cart-item-img me-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                        <span class="text-muted small d-block mb-2">{{ $item->product->category->name ?? 'Kategori' }}</span>
                                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-0 border-0 bg-transparent text-danger small fw-bold" style="font-size: 11px;">
                                                <i class="fa fa-trash-can me-1"></i> HAPUS
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4">
                                <form method="POST" action="{{ route('cart.update') }}" class="d-flex align-items-center justify-content-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="itemId" value="{{ $item->id }}">
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control qty-input shadow-none p-1 border-0 bg-light me-2">
                                    <button type="submit" class="btn btn-sm btn-light border p-1 rounded-circle">
                                        <i class="fa fa-rotate text-muted" style="font-size: 10px;"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-center py-4">
                                <small class="text-muted d-block" style="font-size: 10px;">Harga Satuan</small>
                                <span class="fw-semibold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end pe-4 py-4">
                                <span class="fw-bold text-dark">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-5 text-center">
                    <div class="empty-cart-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <h5 class="fw-bold">Keranjangmu Kosong</h5>
                    <p class="text-muted">Sepertinya kamu belum memilih produk menarik untuk dibeli.</p>
                    <a href="{{ route('home') }}" class="btn btn-maroon px-4 mt-3">Mulai Belanja</a>
                </div>
                @endif
            </div>

            @if($items->items->count() > 0)
            <div class="mt-4">
                <a href="{{ route('home') }}" class="text-decoration-none text-muted fw-bold small">
                    <i class="fa fa-arrow-left me-1"></i> LANJUTKAN BELANJA
                </a>
            </div>
            @endif
        </div>

        {{-- RIGHT: ORDER SUMMARY --}}
        @if($items->items->count() > 0)
        <div class="col-lg-4">
            <div class="card summary-card p-4 position-sticky" style="top: 20px;">
                <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>
                
                {{-- VOUCHER SECTION --}}
                <div class="mb-4">
                    <label class="fw-bold small text-muted mb-2">Punya Kode Voucher?</label>
                    @if($items->voucher)
                        <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded border border-success">
                            <div>
                                <small class="text-success fw-bold d-block">Voucher Terpasang:</small>
                                <span class="fw-bold text-dark">{{ $items->voucher->code }}</span>
                            </div>
                            <form action="{{ route('cart.voucher.remove') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger p-0"><i class="fa fa-times-circle"></i></button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.voucher') }}" method="POST" class="d-flex">
                            @csrf
                            <input type="text" name="code" class="form-control form-control-sm me-2 shadow-none" placeholder="Masukkan kode...">
                            <button type="submit" class="btn btn-sm btn-dark px-3">Gunakan</button>
                        </form>
                    @endif
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Harga ({{ $items->items->count() }} barang)</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Biaya Layanan (5%)</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($admin_fee ?? 0, 0, ',', '.') }}</span>
                </div>

                @if($discount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Diskon Voucher</span>
                    <span class="fw-bold text-success">- Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Biaya Pengiriman</span>
                    <span class="text-success fw-bold small">GRATIS COD</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-5">Total Harga</span>
                    <span class="fw-bold fs-4 text-maroon" style="color: #9F1521;">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-maroon w-100 py-3 shadow-sm mb-3">
                    Lanjut ke Pembayaran <i class="fa fa-arrow-right ms-2 small"></i>
                </a>

                <div class="p-3 bg-light rounded-3 text-center">
                    <small class="text-muted" style="font-size: 11px;">
                        <i class="fa fa-shield-check me-1 text-success"></i> Transaksi di Telcopedia terjamin aman & terverifikasi NIM Mahasiswa.
                    </small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
