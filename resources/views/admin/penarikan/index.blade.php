@extends('layouts.app')
@section('title', 'Persetujuan Dana - Admin')

@section('hero_title', 'Persetujuan Dana')
@section('hero_subtitle', 'Tinjau dan proses permintaan penarikan dana dari para seller.')
@section('hero_emoji', '')

@push('styles')
<style>
    .x-small { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0 bg-white">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">SELLER</th>
                        <th class="py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">JUMLAH</th>
                        <th class="py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">REKENING TUJUAN</th>
                        <th class="text-center py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">STATUS</th>
                        <th class="text-end pe-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ $w->user->name }}</div>
                                <div class="x-small text-muted">{{ $w->user->email }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-danger">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                                <div class="x-small text-muted">{{ $w->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3">
                                <div class="small fw-bold">{{ $w->bank_name }}</div>
                                <div class="small text-muted">{{ $w->account_number }} a/n {{ $w->account_name }}</div>
                            </td>
                            <td class="text-center">
                                @if($w->status === 'pending')
                                    <span class="badge bg-warning text-dark x-small">PENDING</span>
                                @elseif($w->status === 'approved')
                                    <span class="badge bg-success text-white x-small">DICAIRKAN</span>
                                @else
                                    <span class="badge bg-danger text-white x-small">DITOLAK</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($w->status === 'pending')
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('penarikan.approve', $w->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold">Approve</button>
                                        </form>
                                        <form action="{{ route('penarikan.reject', $w->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada permintaan penarikan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
