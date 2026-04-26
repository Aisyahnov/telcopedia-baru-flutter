@extends('layouts.app')
@section('title', 'Kelola Pembayaran - Admin')

@section('hero_title', 'Monitoring Pembayaran')
@section('hero_subtitle', 'Pantau arus kas, metode pembayaran, dan pendapatan admin fee (5%).')
@section('hero_emoji', '')

@push('styles')
<style>
    .x-small { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .border-dashed { border-style: dashed !important; }
</style>
@endpush

@section('content')
    <!-- STATS SUMMARY -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">TOTAL TRANSAKSI</div>
                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-maroon border-4">
                <div class="text-muted small fw-bold mb-1 text-maroon">TOTAL BIAYA ADMIN (5%)</div>
                <h4 class="fw-bold mb-0 text-maroon">Rp {{ number_format($orders->sum('admin_fee'), 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">JUMLAH PESANAN</div>
                <h4 class="fw-bold mb-0 text-dark">{{ $orders->count() }} Pesanan</h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0 bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">ID / PEMBELI</th>
                        <th class="text-center py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">METODE</th>
                        <th class="text-end py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">NOMINAL</th>
                        <th class="text-end py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">ADMIN FEE</th>
                        <th class="text-end pe-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="fw-bold text-dark mb-1">#TPD-{{ $order->id }}</div>
                                <div class="small text-muted"><i class="fa fa-user me-1"></i> {{ $order->user->name }}</div>
                                <div class="x-small text-muted mt-1">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="text-center">
                                @if($order->payment_method == 'cod')
                                    <span class="badge bg-info-subtle text-info border border-info rounded-pill px-3">COD</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary rounded-pill px-3">TRANSFER</span>
                                    @if($order->payment_proof)
                                        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="ms-1 text-muted"><i class="fa fa-image"></i></a>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end fw-bold">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-maroon fw-bold">
                                Rp {{ number_format($order->admin_fee, 0, ',', '.') }}
                            </td>
                            <td class="text-end pe-4">
                                @if($order->status == 'pending_payment')
                                    <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3">Pending Bayar</span>
                                @elseif($order->status == 'paid_verifying')
                                    <span class="badge bg-primary-subtle text-primary border border-primary rounded-pill px-3">Menunggu Seller</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3">Diproses</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-success text-white rounded-pill px-3">Selesai</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-3">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-5">
                                    <i class="fa fa-receipt fa-4x text-muted opacity-25 mb-4 d-block mx-auto"></i>
                                    <h6 class="fw-bold mb-0">Belum ada transaksi di Telcopedia.</h6>
                                    <p class="text-muted small">Semua pergerakan uang akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 p-4 bg-light rounded-4 border border-warning border-dashed text-center">
        <p class="mb-0 text-muted x-small fw-bold"><i class="fa fa-info-circle text-warning me-1"></i> MONITORING SAJA: Admin memantau transaksi, verifikasi pembayaran dilakukan secara otomatis atau oleh seller sesuai alur sistem.</p>
    </div>
@endsection
