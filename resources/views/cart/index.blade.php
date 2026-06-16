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
    .btn-maroon:disabled { background-color: #ccc; border-color: #ccc; transform: none; cursor: not-allowed; }
    .empty-cart-icon { font-size: 5rem; color: #dee2e6; margin-bottom: 20px; }
    
    /* Checkbox Custom Styling */
    .form-check-input:checked { background-color: #9F1521; border-color: #9F1521; }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="text-center mb-5 mt-4">
        <h2 class="fw-900" style="letter-spacing: -1px;">Keranjang <span class="text-maroon">Belanja</span></h2>
        <p class="text-muted mb-0"><span id="selected-count">0 Produk terpilih</span> siap dicheckout.</p>
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
                            <th class="ps-4 py-3" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th class="py-3">PRODUK</th>
                            <th class="text-center py-3">KUANTITAS</th>
                            <th class="text-end pe-4 py-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items->items as $item)
                        @if($item->product)
                        <tr class="cart-row" data-id="{{ $item->id }}" data-price="{{ $item->product->price }}" data-qty="{{ $item->quantity }}">
                            <td class="ps-4 py-4">
                                <input type="checkbox" class="form-check-input item-checkbox" name="selected_items[]" value="{{ $item->id }}">
                            </td>
                            <td class="py-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->product->image_url }}" class="cart-item-img me-3">
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
                                <small class="text-muted d-block mt-1" style="font-size: 10px;">Rp {{ number_format($item->product->price, 0, ',', '.') }} / pcs</small>
                            </td>
                            <td class="text-end pe-4 py-4">
                                <span class="fw-bold text-dark">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @endif
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
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total Harga (<span id="summary-item-count">0</span> barang)</span>
                    <span class="fw-semibold text-dark" id="summary-subtotal">Rp 0</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Biaya Layanan (5%)</span>
                    <span class="fw-semibold text-dark" id="summary-admin-fee">Rp 0</span>
                </div>

                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted small">Biaya Pengiriman</span>
                    <span class="text-success fw-bold small">GRATIS COD</span>
                </div>

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
                        <span class="fw-bold text-success d-block">- Rp <span id="summary-discount">{{ number_format($discount ?? 0, 0, ',', '.') }}</span></span>
                        <form action="{{ route('cart.voucher.remove') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 text-danger small text-decoration-none" style="font-size: 11px;">Hapus Voucher</button>
                        </form>
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                    <span class="fw-bold fs-5">Total Bayar</span>
                    <span class="fw-bold fs-4 text-maroon" id="summary-total" data-discount="{{ $discount }}">Rp 0</span>
                </div>

                <form action="{{ route('checkout.index') }}" method="GET" id="checkout-form">
                    <input type="hidden" name="cart_item_ids" id="selected-ids-input">
                    <button type="submit" id="btn-checkout" class="btn btn-maroon w-100 py-3 shadow-sm mb-3 justify-content-center" disabled>
                        Lanjut ke Pembayaran <i class="fa fa-arrow-right ms-2 small"></i>
                    </button>
                </form>

                <div class="p-3 bg-light rounded-3 text-center">
                    <small class="text-muted" style="font-size: 11px;">
                        <i class="fa fa-shield-check me-1 text-success"></i> Pilih produk yang ingin di-checkout terlebih dahulu.
                    </small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkoutBtn = document.getElementById('btn-checkout');
        const selectedIdsInput = document.getElementById('selected-ids-input');
        
        // UI Elements for Summary
        const summaryItemCount = document.getElementById('summary-item-count');
        const summarySubtotal = document.getElementById('summary-subtotal');
        const summaryAdminFee = document.getElementById('summary-admin-fee');
        const summaryTotal = document.getElementById('summary-total');
        const selectedCountLabel = document.getElementById('selected-count');

        function updateSummary() {
            let subtotal = 0;
            let count = 0;
            let selectedIds = [];

            itemCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const row = checkbox.closest('.cart-row');
                    const price = parseFloat(row.dataset.price);
                    const qty = parseInt(row.dataset.qty);
                    subtotal += price * qty;
                    count += qty;
                    selectedIds.push(row.dataset.id);
                }
            });

            const adminFee = subtotal * 0.05;
            const discount = parseFloat(summaryTotal.dataset.discount || 0);
            const total = Math.max(0, subtotal + adminFee - discount);

            // Update UI
            summaryItemCount.innerText = count;
            summarySubtotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            summaryAdminFee.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(adminFee);
            summaryTotal.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            selectedCountLabel.innerText = selectedIds.length + ' Produk terpilih';
            
            // Update Form
            selectedIdsInput.value = selectedIds.join(',');
            checkoutBtn.disabled = selectedIds.length === 0;
        }

        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSummary();
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateSummary();
                // Update select all state
                selectAll.checked = Array.from(itemCheckboxes).every(i => i.checked);
            });
        });

        // Initial update
        updateSummary();
    });
</script>
@endpush
@endsection
