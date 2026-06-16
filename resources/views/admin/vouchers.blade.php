@extends('layouts.app')
@section('title', 'Kelola Voucher - Admin')

@section('hero_title', 'Kelola Voucher')
@section('hero_subtitle', 'Terbitkan dan pantau kode promo diskon untuk meningkatkan daya beli mahasiswa.')
@section('hero_emoji', '')

@push('styles')
<style>
    .uppercase-label { text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.65rem !important; }
</style>
@endpush

@section('content')
    <div class="row g-4">
        <!-- FORM BUAT VOUCHER BARU -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light rounded-3 p-2 me-3">
                            <i class="fa fa-plus text-danger"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Buat Voucher</h5>
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
                                <input type="number" class="form-control border-start-0 ps-0 fw-bold" name="discount_amount" placeholder="50000" min="1000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold small uppercase-label">Minimal Belanja (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold fw-mono">Rp</span>
                                <input type="number" class="form-control border-start-0 ps-0 fw-bold" name="min_spend" placeholder="0" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted fw-bold small uppercase-label">Batas Waktu (Expired)</label>
                            <input type="date" class="form-control" name="valid_until">
                        </div>
                        <button type="submit" class="btn btn-maroon w-100 rounded-pill fw-bold py-3 shadow-sm">
                            <i class="fa fa-print me-1"></i> Unggah Voucher
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
                                <th class="py-3 text-center" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 1px;">AKSI</th>
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
                                    <div class="small text-muted mt-1">Min: Rp {{ number_format($v->min_spend, 0, ',', '.') }}</div>
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
                                <td class="py-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editVoucher{{ $v->id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus voucher ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal Edit Voucher -->
                            <div class="modal fade" id="editVoucher{{ $v->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 rounded-4 shadow">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold mb-0">Edit Voucher</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.vouchers.update', $v->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold small uppercase-label">Kode Promo Eksklusif</label>
                                                    <input type="text" class="form-control fw-bold" name="code" value="{{ $v->code }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold small uppercase-label">Potongan Belanja (Rp)</label>
                                                    <input type="number" class="form-control fw-bold" name="discount_amount" value="{{ $v->discount_amount }}" min="1000" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold small uppercase-label">Minimal Belanja (Rp)</label>
                                                    <input type="number" class="form-control fw-bold" name="min_spend" value="{{ $v->min_spend }}" min="0" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold small uppercase-label">Batas Waktu (Expired)</label>
                                                    <input type="date" class="form-control" name="valid_until" value="{{ $v->valid_until ? \Carbon\Carbon::parse($v->valid_until)->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-maroon rounded-pill px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($vouchers->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-5">
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
                
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $vouchers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
