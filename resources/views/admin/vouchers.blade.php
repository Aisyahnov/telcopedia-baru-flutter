@extends('layouts.app')
@section('title', 'Manajemen Voucher - Admin')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0"><i class="fa fa-ticket-simple me-2 text-danger"></i> Manajemen Voucher</h4>
            <p class="text-white-50 mb-0 small">Terbitkan dan pantau kode promo diskon untuk mahasiswa.</p>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.admin_sidebar')
        </div>

        <!-- MAIN TABLE & FORM -->
        <div class="col-lg-9">
            
            <div class="row g-4">
                <!-- FORM BUAT VOUCHER BARU -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-light rounded-3 p-2 me-3">
                                    <i class="fa fa-plus text-danger"></i>
                                </div>
                                <h5 class="fw-bold mb-0">Terbitkan Voucher</h5>
                            </div>
                            
                            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold small uppercase-label">Kode Promo Eksklusif</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-tag opacity-50"></i></span>
                                        <input type="text" class="form-control text-uppercase border-start-0 ps-0 fw-bold" name="code" placeholder="TELKO50K" required style="letter-spacing: 1px;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold small uppercase-label">Potongan Belanja (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 fw-bold fw-mono">Rp</span>
                                        <input type="number" class="form-control border-start-0 ps-0 fw-bold" name="discount_amount" placeholder="50.000" min="1000" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold small uppercase-label">Batas Waktu (Expired)</label>
                                    <input type="date" class="form-control" name="valid_until">
                                </div>
                                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm border-2">
                                    <i class="fa fa-print me-1"></i> Terbitkan Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TABEL VOUCHER AKTIF -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                        <div class="table-responsive h-100">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4 py-3" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">KODE VOUCHER</th>
                                        <th class="py-3 text-center" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">DISCOUNT</th>
                                        <th class="py-3 text-center" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">BERLAKU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vouchers as $v)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning-subtle text-warning border border-warning rounded px-2 py-1 me-2 shadow-sm" style="font-family: monospace; font-weight: 800; font-size: 0.95rem;">
                                                    {{ $v->code }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="fw-bold text-danger">Rp {{ number_format($v->discount_amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="py-3 text-center small text-muted">
                                            @if($v->valid_until)
                                                <i class="fa fa-calendar-alt opacity-50 me-1"></i> {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
                                            @else
                                                <span class="badge bg-light text-secondary border px-3 rounded-pill fw-bold" style="font-size: 0.6rem;">FOREVER</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($vouchers->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="opacity-25 py-4">
                                                    <i class="fa fa-ticket-simple fa-4x mb-3"></i>
                                                    <p class="mb-0 fw-bold">Belum ada promo aktif.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .uppercase-label { text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.65rem !important; }
</style>
@endsection
