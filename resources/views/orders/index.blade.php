@extends('layouts.app')
@section('title', 'Riwayat Pesanan - Telcopedia')

@push('styles')
<style>
    .shopee-star-container {
        display: inline-flex;
        gap: 6px;
    }
    .shopee-star {
        cursor: pointer;
        font-size: 2.5rem;
        color: #e0e0e0;
        transition: 0.15s all ease-in-out;
    }
    .shopee-star.active, .shopee-star.hover {
        color: #ffc107;
        transform: scale(1.15);
    }
    .shopee-star:active {
        transform: scale(0.9);
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    
    <div class="text-center mb-5">
        <h2 class="fw-900">Riwayat <span class="text-maroon">Pesanan Saya</span></h2>
        <p class="text-muted">Pantau status pesanan dan lihat riwayat belanjamu di sini.</p>
    </div>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center rounded-4">
            <div class="card-body py-5">
                <i class="fa fa-shopping-bag fa-4x text-muted opacity-25 mb-4"></i>
                <h5 class="fw-bold mb-3">Anda belum pernah berbelanja.</h5>
                <p class="text-muted mb-4">Ayo mulai cari barang kebutuhan kuliah yang menarik di katalog Telcopedia.</p>
                <a href="{{ route('home') }}" class="btn btn-maroon px-5 rounded-pill shadow-sm">Mulai Belanja</a>
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
                                <span class="fw-bold me-3">Belanja <span class="text-maroon">#ORD-{{ $order->id }}</span></span>
                                <small class="text-muted"><i class="fa fa-calendar-alt me-1"></i> {{ $order->created_at->format('d M Y') }}</small>
                            </div>
                            <div>
                            <div>
                                <span class="badge-status 
                                    @if($order->status == 'pending_payment') bg-warning-subtle
                                    @elseif($order->status == 'paid_verifying') bg-info-subtle
                                    @elseif($order->status == 'processing') bg-primary-subtle
                                    @elseif($order->status == 'shipped') bg-info-subtle
                                    @elseif($order->status == 'completed') bg-success-subtle
                                    @else bg-secondary-subtle @endif">
                                    @if($order->status == 'pending_payment') MENUNGGU PEMBAYARAN
                                    @elseif($order->status == 'paid_verifying') MENUNGGU VERIFIKASI
                                    @elseif($order->status == 'processing') DIPROSES
                                    @elseif($order->status == 'shipped') DIKIRIM / COD
                                    @elseif($order->status == 'completed') SELESAI
                                    @else {{ strtoupper($order->status) }} @endif
                                </span>
                            </div>
                            </div>
                        </div>

                        <!-- Order Items & Tracking -->
                        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <div class="mb-3 mb-md-0">
                                @foreach($order->items as $item)
                                    <div class="mb-3 border-bottom pb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-3 border" width="50" height="50" style="object-fit: cover;">
                                            <div>
                                                <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                                                <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                        
                                        @if($order->status == 'completed' || $order->status == 'shipped')
                                            @php
                                                $hasReviewed = \App\Models\Review::where('order_id', $order->id)
                                                    ->where('product_id', $item->product_id)
                                                    ->where('user_id', Auth::id())
                                                    ->exists();
                                                
                                                $productReturn = \App\Models\ProductReturn::where('order_id', $order->id)
                                                    ->where('product_id', $item->product_id)
                                                    ->where('user_id', Auth::id())
                                                    ->first();
                                            @endphp
                                            
                                            <div class="d-flex justify-content-end gap-2 mt-2">
                                                <!-- RETURN BUTTON -->
                                                @if($productReturn)
                                                    <span class="badge bg-light text-secondary border py-2 px-3
                                                        @if($productReturn->status == 'pending') border-warning text-warning
                                                        @elseif($productReturn->status == 'approved') border-success text-success
                                                        @elseif($productReturn->status == 'rejected') border-danger text-danger
                                                        @endif">
                                                        <i class="fa fa-info-circle me-1"></i> Retur: {{ ucfirst($productReturn->status) }}
                                                    </span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#returnModal-{{ $order->id }}-{{ $item->product_id }}">
                                                        Ajukan Retur
                                                    </button>
                                                @endif

                                                <!-- REVIEW BUTTON -->
                                                @if($order->status == 'completed')
                                                    @if($hasReviewed)
                                                        <span class="badge bg-light text-success border border-success py-2 px-3"><i class="fa fa-check-circle me-1"></i> Telah Diulas</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-maroon shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#reviewModal-{{ $order->id }}-{{ $item->product_id }}">
                                                            Beri Ulasan
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- Return Modal -->
                                            @if(!$productReturn)
                                            <div class="modal fade" id="returnModal-{{ $order->id }}-{{ $item->product_id }}" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow rounded-4">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold" id="returnModalLabel">Ajukan Pengembalian Barang</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body pt-2 text-start">
                                                            <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                                
                                                                <div class="d-flex align-items-center mb-4 mt-2">
                                                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-3 border" width="60" height="60" style="object-fit: cover;">
                                                                    <div>
                                                                        <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                                                        <span class="text-muted small">Pesanan #ORD-{{ $order->id }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-4">
                                                                    <label class="form-label fw-bold small">Alasan Pengembalian</label>
                                                                    <textarea class="form-control bg-light border-0" name="reason" rows="3" placeholder="Contoh: Barang cacat, tidak sesuai deskripsi, dll." required maxlength="1000"></textarea>
                                                                    <div class="form-text text-muted small">Mohon jelaskan komplain Anda secara detail agar mempermudah penjual memproses.</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Bukti Foto / Video (Opsional)</label>
                                                                    <input type="file" name="media" class="form-control bg-light border-0" accept="image/*,video/*">
                                                                </div>

                                                                <button type="submit" class="btn btn-secondary w-100 rounded-pill fw-bold pt-2 pb-2">Kirim Pengajuan Retur</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Review Modal -->
                                            @if(!$hasReviewed)
                                            <div class="modal fade" id="reviewModal-{{ $order->id }}-{{ $item->product_id }}" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow rounded-4">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold" id="reviewModalLabel">Beri Ulasan Produk</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body pt-2 text-start">
                                                            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                                
                                                                <div class="d-flex align-items-center mb-4 mt-2">
                                                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-3 border" width="60" height="60" style="object-fit: cover;">
                                                                    <div>
                                                                        <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                                                        <span class="text-muted small">Pesanan #ORD-{{ $order->id }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-4 text-center">
                                                                    <label class="form-label fw-bold d-block mb-3">Berapa bintang untuk produk ini?</label>
                                                                    <div class="shopee-star-container" data-id="{{ $order->id }}-{{ $item->product_id }}">
                                                                        @for($i=1; $i<=5; $i++)
                                                                            <i class="fa-solid fa-star shopee-star active" data-val="{{ $i }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <input type="hidden" name="rating" id="rating-input-{{ $order->id }}-{{ $item->product_id }}" value="5" required>
                                                                    <div class="rating-label-text mt-2 fw-bold text-warning" id="rating-text-{{ $order->id }}-{{ $item->product_id }}">Sangat Baik</div>
                                                                </div>

                                                                <div class="mb-4">
                                                                    <label class="form-label fw-bold small">Tuliskan pengalaman Anda</label>
                                                                    <textarea class="form-control bg-light border-0" name="comment" rows="3" placeholder="Sangat bagus, sesuai gambar! (Opsional)" maxlength="500"></textarea>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Foto / Video Produk (Opsional)</label>
                                                                    <input type="file" name="media" class="form-control bg-light border-0" accept="image/*,video/*">
                                                                </div>

                                                                <button type="submit" class="btn btn-maroon w-100 rounded-pill fw-bold pt-2 pb-2">Kirim Ulasan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($order->shipping_address)
                                    <div class="mt-3 bg-light p-3 rounded-3 border-start border-4 border-maroon">
                                        <span class="small fw-bold text-dark d-block mb-1"><i class="fa-solid fa-location-dot me-1 text-maroon"></i> Lokasi </span>
                                        <p class="mb-0 small text-muted">{{ $order->shipping_address }}</p>
                                    </div>
                                @endif

                                @if($order->tracking_number)
                                    <div class="mt-2 bg-light p-2 rounded border border-warning d-inline-block">
                                        <span class="small fw-bold text-dark"><i class="fa fa-truck me-1"></i> Resi Paket (Jika ada):</span>
                                        <code class="ms-1 fs-6 text-maroon">{{ $order->tracking_number }}</code>
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
                                    <h4 class="fw-bold text-maroon mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h4>
                                </div>
                                
                                <!-- Bukti Pembayaran & Status (Modificator) -->
                                <div class="mb-2 d-flex justify-content-md-end gap-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-credit-card me-1"></i> {{ strtoupper($order->payment_method ?? 'TRANSFER') }}
                                    </span>
                                </div>
                                
                                @if($order->status == 'pending_payment' && $order->payment_method != 'cod')
                                    @if($order->payment_proof)
                                        <div class="alert alert-success border-0 small mb-0 py-2">
                                            <i class="fa fa-clock me-1"></i> Bukti Terkirim. Tunggu Seller memverifikasi.
                                        </div>
                                    @else
                                        <!-- Form Upload Bukti (Direct Upload / Trivial Modal) -->
                                        <form action="{{ route('checkout.upload_bukti', $order->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center justify-content-md-end">
                                            @csrf
                                            <input type="file" name="payment_proof" class="form-control form-control-sm w-auto me-2" required accept="image/*">
                                            <button type="submit" class="btn btn-sm btn-outline-maroon shadow-none">Upload Bukti</button>
                                        </form>
                                        <small class="text-muted mt-1 d-block">Mohon lampirkan struk transfer Anda.</small>
                                    @endif
                                @elseif($order->status == 'paid_verifying' && $order->payment_method == 'cod')
                                        <div class="alert alert-success border-0 small mb-0 py-2">
                                            <i class="fa fa-handshake me-1"></i> COD diajukan. Tunggu Seller mengkonfirmasi pesanan.
                                        </div>
                                @elseif($order->status == 'paid_verifying' && $order->payment_method != 'cod')
                                        <div class="alert alert-success border-0 small mb-0 py-2">
                                            <i class="fa fa-clock me-1"></i> Bukti Terkirim. Tunggu Seller memverifikasi.
                                        </div>
                                @endif

                                @if($order->status == 'shipped' || $order->status == 'processing')
                                    <div class="mt-3">
                                        <form id="complete-form-{{ $order->id }}" action="{{ route('orders.complete', $order->id) }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                        <button type="button" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm btn-complete-order" data-order-id="{{ $order->id }}">
                                            <i class="fa fa-check-circle me-1"></i> Pesanan Diterima
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.querySelectorAll('.btn-complete-order').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const form = document.getElementById('complete-form-' + orderId);
            
            Swal.fire({
                title: 'Konfirmasi Pesanan',
                text: "Apakah Anda yakin sudah menerima pesanan ini dengan baik? Pastikan kondisi barang sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Sudah Diterima',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Shopee Style Interactive Stars
    const ratingLabels = {
        '1': 'Buruk Sekali',
        '2': 'Buruk',
        '3': 'Cukup',
        '4': 'Baik',
        '5': 'Sangat Baik'
    };

    document.querySelectorAll('.shopee-star-container').forEach(container => {
        const stars = container.querySelectorAll('.shopee-star');
        const targetId = container.getAttribute('data-id');
        const inputField = document.getElementById('rating-input-' + targetId);
        const labelText = document.getElementById('rating-text-' + targetId);
        
        let currentRating = 5;

        const updateStars = (val, isHover = false) => {
            stars.forEach(star => {
                const starVal = parseInt(star.getAttribute('data-val'));
                if (isHover) {
                    star.classList.toggle('hover', starVal <= val);
                    star.classList.remove('active');
                } else {
                    star.classList.toggle('active', starVal <= val);
                    star.classList.remove('hover');
                }
            });
            if (labelText) {
                labelText.textContent = ratingLabels[val];
            }
        };

        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const hoverVal = parseInt(this.getAttribute('data-val'));
                updateStars(hoverVal, true);
            });

            star.addEventListener('mouseout', function() {
                updateStars(currentRating, false);
            });

            star.addEventListener('click', function() {
                currentRating = parseInt(this.getAttribute('data-val'));
                inputField.value = currentRating;
                updateStars(currentRating, false);
                
                // Add tiny click animation effect
                this.style.transform = 'scale(1.3)';
                setTimeout(() => { this.style.transform = ''; }, 150);
            });
        });
    });
</script>
@endpush
@endsection
