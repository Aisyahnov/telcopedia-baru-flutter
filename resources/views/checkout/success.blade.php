@extends('layouts.app')
@section('title', 'Pesanan Berhasil - Telcopedia')

@section('content')
<div class="container my-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="mb-4">
                <i class="fa-solid fa-circle-check text-success" style="font-size: 80px;"></i>
            </div>
            <h2 class="fw-bold mb-3">Pesanan Berhasil Dibuat!</h2>
            <p class="text-muted mb-4">
                Pesanan dengan metode <strong>{{ strtoupper($order->payment_method) }}</strong> telah berhasil dicatat. 
                @if($order->payment_method == 'cod')
                    Silakan hubungi seller sekarang untuk menyepakati titik temu dan waktu transaksi.
                @else
                    Silakan selesaikan pembayaran agar pesanan segera diproses.
                @endif
            </p>

            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $order->items->first()->product->image_url }}" class="rounded-3 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <div class="text-start">
                        <div class="fw-bold text-truncate" style="max-width: 250px;">{{ $order->items->first()->product->name }}</div>
                        <small class="text-muted">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</small>
                    </div>
                </div>
                
                @php
                    $seller = $order->items->first()->product->seller;
                @endphp

                <a href="{{ route('chat.show', ['user' => $seller->id]) }}?product_id={{ $order->items->first()->product_id }}" class="btn btn-maroon w-100 py-2 rounded-pill mb-2">
                    <i class="fa-solid fa-comments me-2"></i> Chat Seller Sekarang
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-pill">
                    Lihat Riwayat Pesanan
                </a>
            </div>

            <a href="{{ route('home') }}" class="text-maroon text-decoration-none small">
                <i class="fa fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-maroon { background-color: #9F1521; color: white; border: none; }
    .btn-maroon:hover { background-color: #7c111b; color: white; }
    .text-maroon { color: #9F1521; }
</style>
@endpush
