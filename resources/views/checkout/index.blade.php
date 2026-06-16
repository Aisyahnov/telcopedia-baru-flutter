@extends('layouts.app')
@section('title', 'Checkout - Telcopedia')

@push('styles')
<style>
    .checkout-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
    .address-box { border-left: 4px solid #9F1521; background: #fffcfc; }
    .summary-card { border-radius: 15px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.08); background: #fff; }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 12px; font-weight: 600; padding: 12px; transition: 0.3s; border: none; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: translateY(-2px); }
    .text-maroon { color: #9F1521; }
    .form-control:focus { border-color: #9F1521; box-shadow: 0 0 0 0.2rem rgba(159, 21, 33, 0.1); }
    .payment-option { cursor: pointer; transition: 0.2s; }
    .payment-option:hover { background-color: #fff9f9; border-color: #9F1521 !important; }
    .payment-option input:checked + div { color: #9F1521; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="text-center mb-5 mt-4">
        <h2 class="fw-900" style="letter-spacing: -1px;">Konfirmasi <span class="text-maroon">Checkout</span></h2>
        <p class="text-muted">Selesaikan pesananmu dan pilih metode pengiriman yang nyaman.</p>
    </div>

    <form action="{{ route('checkout.save') }}" method="POST" id="checkout-form">
        @csrf
        @if(isset($buyNowProductId))
            <input type="hidden" name="buy_now_product_id" value="{{ $buyNowProductId }}">
        @endif
        <input type="hidden" name="cart_item_ids" value="{{ $cartItemIds ?? '' }}">
    </form>

    <div class="row g-4">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">
            {{-- SHIPPING ADDRESS / LOCATION --}}
            <div class="card checkout-card p-4 mb-4" style="border-top: 5px solid #9F1521;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-location-dot text-maroon fs-5 me-2"></i>
                        <h6 class="fw-bold mb-0">Alamat Pengiriman</h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 {{ !$userAddress ? 'd-none' : '' }}" id="btnChangeAddress">
                        <i class="fa fa-pencil-alt me-1 small"></i> Ubah
                    </button>
                </div>
                
                <div id="addressDisplay" class="{{ !$userAddress ? 'd-none' : '' }}">
                    <div class="fw-bold mb-1">{{ auth()->user()->name }} | {{ auth()->user()->phone ?? 'No. HP Belum Diatur' }}</div>
                    <div class="text-muted small mb-0" id="addressText">
                        {{ $userAddress ?? 'Alamat belum disetting di profil. Silakan isi alamat pengiriman di bawah.' }}
                    </div>
                </div>

                <div id="addressInput" class="{{ $userAddress ? 'd-none' : '' }} mt-3">
                    <label class="form-label small text-muted">Masukkan Alamat Baru / Detail Lokasi COD</label>
                    <textarea name="shipping_address" form="checkout-form" id="shipping_address_input" class="form-control border-0 bg-light p-3" rows="3" placeholder="Contoh: Gedung GKU Lt. 2, Depan Asrama, atau No. Kamar..." required>{{ old('shipping_address', $userAddress) }}</textarea>
                    <div class="mt-2 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted" id="btnCancelChange">Batal</button>
                        <button type="button" class="btn btn-sm btn-maroon rounded-pill px-3 py-1" id="btnSaveAddress">Simpan Alamat</button>
                    </div>
                </div>

                @error('shipping_address')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            @push('scripts')
            <script>
                document.getElementById('btnChangeAddress').addEventListener('click', function() {
                    document.getElementById('addressDisplay').classList.add('d-none');
                    document.getElementById('addressInput').classList.remove('d-none');
                    this.classList.add('d-none');
                });

                document.getElementById('btnSaveAddress').addEventListener('click', function() {
                    const addressInput = document.getElementById('shipping_address_input');
                    if(addressInput.value.trim() === '') {
                        addressInput.reportValidity();
                        return;
                    }
                    document.getElementById('addressText').innerText = addressInput.value;
                    document.getElementById('addressDisplay').classList.remove('d-none');
                    document.getElementById('addressInput').classList.add('d-none');
                    document.getElementById('btnChangeAddress').classList.remove('d-none');
                });

                document.getElementById('btnCancelChange').addEventListener('click', function() {
                    document.getElementById('addressDisplay').classList.remove('d-none');
                    document.getElementById('addressInput').classList.add('d-none');
                    document.getElementById('btnChangeAddress').classList.remove('d-none');
                    
                    // Reset input to what's currently displayed
                    document.getElementById('shipping_address_input').value = document.getElementById('addressText').innerText.trim();
                });
            </script>
            @endpush

            {{-- PRODUCT LIST --}}
            <div class="card checkout-card overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Rincian Produk</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted">
                                    <th class="ps-4">PRODUK</th>
                                    <th class="text-center">KUANTITAS</th>
                                    <th class="text-end pe-4">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->image_url }}" class="product-img me-3">
                                            <div>
                                                <span class="fw-bold d-block">{{ $item->product->name }}</span>
                                                <small class="text-muted">Rp {{ number_format($item->product->price, 0, ',', '.') }} / item</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PAYMENT METHOD --}}
            <div class="card checkout-card p-4 mb-4">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-credit-card text-maroon me-2"></i>Pilih Metode Pembayaran</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="payment-option border rounded p-3 d-block cursor-pointer position-relative">
                            <input type="radio" name="payment_method" form="checkout-form" value="transfer" class="form-check-input position-absolute top-50 end-0 translate-middle-y me-3" checked>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="fa-solid fa-building-columns text-maroon fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">Transfer Bank</div>
                                    <div class="text-muted" style="font-size: 10px;">Verifikasi manual oleh Seller</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="payment-option border rounded p-3 d-block cursor-pointer position-relative">
                            <input type="radio" name="payment_method" form="checkout-form" value="cod" class="form-check-input position-absolute top-50 end-0 translate-middle-y me-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-2 me-3">
                                    <i class="fa-solid fa-hand-holding-dollar text-maroon fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small">COD (Bayar di Tempat)</div>
                                    <div class="text-muted" style="font-size: 10px;">Ketemuan langsung di area kampus</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: SUMMARY --}}
        <div class="col-lg-4">
            <div class="card summary-card p-4 position-sticky" style="top: 20px;">
                <h5 class="fw-bold mb-4">Ringkasan Pembayaran</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Harga ({{ $items->count() }} barang)</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Biaya Layanan (5%)</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($admin_fee ?? 0, 0, ',', '.') }}</span>
                </div>

                @if(session('success'))
                    <div class="alert alert-success small py-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger small py-2">{{ session('error') }}</div>
                @endif

                <!-- VOUCHER FORM -->
                <div class="mb-3">
                    <label class="small text-muted mb-1">Makin hemat pakai promo!</label>
                    <form action="{{ route('cart.voucher') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="code" class="form-control form-control-sm" placeholder="Masukkan Kode Voucher" required>
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3">Gunakan</button>
                    </form>
                </div>

                @if($discount > 0)
                <div class="d-flex justify-content-between mb-2 align-items-center">
                    <span class="text-muted small">Diskon Voucher</span>
                    <div class="text-end">
                        <span class="fw-bold text-success d-block">- Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</span>
                        <form action="{{ route('cart.voucher.remove') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-danger small text-decoration-none" style="font-size: 11px;">Hapus Voucher</button>
                        </form>
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Biaya Pengiriman</span>
                    <span class="text-success fw-bold small">GRATIS COD</span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-5">Total Tagihan</span>
                    <span class="fw-bold fs-4 text-maroon">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>

                <button type="submit" form="checkout-form" class="btn btn-maroon w-100 shadow-sm mb-3 d-flex justify-content-center align-items-center">
                    Konfirmasi & Buat Pesanan <i class="fa fa-arrow-right ms-2 small"></i>
                </button>

                <div class="p-3 bg-light rounded-3 text-center">
                    <small class="text-muted" style="font-size: 11px;">
                        <i class="fa fa-shield-check me-1 text-success"></i> Dengan menekan tombol di atas, Anda menyetujui transaksi COD/Transfer yang aman melalui Telcopedia.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
