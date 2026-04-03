@extends('layouts.app')
@section('title', 'Kelola Pembayaran - Admin')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0">💰 Kelola Pembayaran</h4>
            <p class="text-white-50 mb-0 small">Verifikasi bukti transfer dari pembeli Telcopedia.</p>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.admin_sidebar')
        </div>

        <!-- MAIN TABLE -->
        <div class="col-lg-9">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-sm">
                <div class="card-body p-0 bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">ID ORDER / PEMBELI</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">TOTAL NOMINAL</th>
                                <th class="text-center py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">BUKTI BAYAR</th>
                                <th class="text-end pe-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">AKSI VERIFIKASI</th>
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
                                        <span class="fw-bold text-danger" style="font-size: 1.1rem;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($order->payment_proof)
                                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#proofModal{{ $order->id }}">
                                                <i class="fa fa-image me-1"></i> Lihat Bukti
                                            </button>
                                            
                                            <!-- MODAL BUKTI BAYAR -->
                                            <div class="modal fade" id="proofModal{{ $order->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0">
                                                        <div class="modal-header border-bottom-0 p-4">
                                                            <h5 class="modal-title fw-bold">Bukti Pembayaran #TPD-{{ $order->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-0 text-center bg-light">
                                                            <img src="{{ asset('storage/' . $order->payment_proof) }}" class="img-fluid p-2" style="max-height: 80vh;">
                                                        </div>
                                                        <div class="modal-footer border-top-0 p-4">
                                                            <div class="d-flex w-100 gap-2">
                                                                <form action="{{ route('admin.payments.reject', $order->id) }}" method="POST" class="flex-grow-1">
                                                                    @csrf
                                                                    <button class="btn btn-outline-danger w-100 fw-bold rounded-pill">Tolak Pembayaran</button>
                                                                </form>
                                                                <form action="{{ route('admin.payments.approve', $order->id) }}" method="POST" class="flex-grow-1">
                                                                    @csrf
                                                                    <button class="btn btn-success w-100 fw-bold rounded-pill">Verifikasi & Terima</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border py-2 px-3">Tanpa Bukti</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.payments.approve', $order->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-success rounded-pill px-3 shadow-none fw-bold"><i class="fa fa-check me-1"></i> Terima</button>
                                            </form>
                                            <form action="{{ route('admin.payments.reject', $order->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-none fw-bold">Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="fa fa-money-bill-transfer fa-4x text-muted opacity-25 mb-4 d-block mx-auto"></i>
                                            <h6 class="fw-bold mb-0">Tidak ada pengajuan pembayaran pending.</h6>
                                            <p class="text-muted small">Semua bukti pembayaran telah diverifikasi seimbang.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-4 bg-light rounded-4 border border-warning border-dashed text-center">
                <p class="mb-0 text-muted x-small fw-bold"><i class="fa fa-info-circle text-warning me-1"></i> Hati-hati dalam memverifikasi. Pastikan nominal dan pengirim pada struk sesuai dengan data di sistem.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .border-dashed { border-style: dashed !important; }
</style>
@endsection
