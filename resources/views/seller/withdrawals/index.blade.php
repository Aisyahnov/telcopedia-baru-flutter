@extends('layouts.app')
@section('title', 'Saldo & Penarikan Dana - Seller')

@section('hero_title', 'Pusat Saldo & Penarikan')
@section('hero_subtitle', 'Pantau pendapatan dari hasil penjualan dan tarik dana Anda dengan aman.')
@section('hero_emoji', '')

@push('styles')
<style>
    .x-small { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Manajemen Saldo</h5>
            <p class="text-muted small mb-0">Kelola pendapatan dan riwayat pencairan dana Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3 text-success">
                <i class="fa fa-check-circle fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Berhasil!</div>
                <small class="text-muted">{{ session('success') }}</small>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3 text-danger">
                <i class="fa fa-exclamation-circle fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Gagal!</div>
                <small class="text-muted">{{ session('error') }}</small>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card card-management shadow-sm p-4 h-100 border-start border-maroon border-5 bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small fw-bold mb-1 text-uppercase">SALDO TERSEDIA</p>
                        <h2 class="fw-bold text-dark mb-0">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</h2>
                        <p class="text-muted small mb-0 mt-2">Dapat dicairkan kapan saja.</p>
                    </div>
                    <div class="bg-maroon-soft p-3 rounded-circle text-maroon">
                        <i class="fa fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-management shadow-sm p-4 h-100 bg-white border-0 border-end border-maroon border-5">
                <div class="mb-3">
                    <h6 class="fw-bold text-dark">Proses Keuangan</h6>
                    <p class="text-muted small">Tarik saldo Anda langsung ke rekening bank terdaftar.</p>
                </div>
                <button type="button" class="btn btn-maroon w-100 py-3 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fa fa-paper-plane me-2"></i> Ajukan Penarikan Dana
                </button>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">RIWAYAT PENARIKAN DANA</h6>
    <div class="card card-management mb-5">
        <div class="card-body p-0">
            <table class="table table-management mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">JUMLAH</th>
                        <th>BANK / REKENING</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end pe-4">TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr>
                            <td class="ps-4 py-3 fw-bold text-danger">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <div class="small fw-bold text-dark">{{ $w->bank_name }}</div>
                                <div class="x-small text-muted">{{ $w->account_number }} a/n {{ $w->account_name }}</div>
                            </td>
                            <td class="text-center">
                                @if($w->status === 'pending')
                                    <span class="badge-status bg-warning-subtle text-warning border border-warning">PENDING</span>
                                @elseif($w->status === 'approved')
                                    <span class="badge-status bg-success-subtle text-success border border-success">BERHASIL</span>
                                @else
                                    <span class="badge-status bg-danger-subtle text-danger border border-danger">DITOLAK</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">{{ $w->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small">Belum ada riwayat penarikan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-24 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-maroon">Tarik Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    <form action="{{ route('seller.withdrawals.store') }}" method="POST">
                        @csrf
                        
                        <div class="bg-maroon-soft rounded-20 p-4 mb-4 text-center border border-maroon border-opacity-10">
                            <span class="x-small text-maroon fw-bold d-block mb-1">SALDO ANDA SAAT INI</span>
                            <h3 class="fw-bold text-maroon mb-0">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</h3>
                        </div>

                        <div class="mb-4">
                            <label class="x-small text-muted fw-bold mb-2">JUMLAH PENARIKAN (MIN RP 10.000)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light fw-bold ps-4" style="border-radius: 30px 0 0 30px;">Rp</span>
                                <input type="number" name="amount" class="form-control border-0 bg-light py-3 pe-4" style="border-radius: 0 30px 30px 0;" required min="10000" max="{{ Auth::user()->balance }}" placeholder="0">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="x-small text-muted fw-bold mb-2">BANK / E-WALLET</label>
                                <input type="text" name="bank_name" class="form-control rounded-pill px-4 py-3 border-0 bg-light shadow-sm" required placeholder="BCA, BNI, GoPay, OVO...">
                            </div>
                            <div class="col-md-6">
                                <label class="x-small text-muted fw-bold mb-2">NOMOR REKENING</label>
                                <input type="text" name="account_number" class="form-control rounded-pill px-4 py-3 border-0 bg-light shadow-sm" required placeholder="000111222">
                            </div>
                            <div class="col-md-6">
                                <label class="x-small text-muted fw-bold mb-2">ATAS NAMA</label>
                                <input type="text" name="account_name" class="form-control rounded-pill px-4 py-3 border-0 bg-light shadow-sm" required placeholder="Nama sesuai buku tabungan">
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 rounded-20 p-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-info-circle me-3 fs-4 opacity-50"></i>
                                <div class="x-small fw-bold lh-base">PENARIKAN AKAN DIPROSES DALAM 1-2 HARI KERJA SETELAH DISETUJUI ADMIN.</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-maroon w-100 py-3 shadow-lg">
                            <i class="fa fa-paper-plane me-2"></i> Kirim Permintaan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
