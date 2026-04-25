@extends('layouts.app')
@section('title', 'Kelola Produk - Telcopedia')

@section('hero_title', 'Daftar Produk Lapak')
@section('hero_subtitle', 'Kelola stok, harga, dan informasi barang dagangan Anda.')
@section('hero_emoji', '')

@push('styles')
<style>
    .product-img {
        width: 55px;
        height: 55px;
        object-fit: cover;
        transition: 0.3s transform ease;
    }
    .product-img:hover {
        transform: scale(1.1);
    }
    .table-management tbody tr {
        transition: 0.2s background-color ease;
    }
    .table-management tbody tr:hover {
        background-color: rgba(0,0,0,0.01);
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Produk</h5>
            <p class="text-muted small mb-0">Total {{ count($products) }} produk terdaftar di lapak Anda.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->is_banned_from_posting)
                <button class="btn btn-secondary rounded-pill px-4 shadow-sm" disabled>
                    <i class="fa fa-ban me-1"></i> Diblokir
                </button>
            @else
                <a href="{{ route('seller.products.create') }}" class="btn btn-maroon rounded-pill px-4 shadow-sm">
                    <i class="fa fa-plus-circle me-1"></i> Tambah Produk
                </a>
            @endif
        </div>
    </div>

    @error('error')
        <div class="alert alert-danger border-0 shadow-sm rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3 text-danger">
                <i class="fa fa-exclamation-triangle fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Akses Ditolak!</div>
                <small class="text-muted">{{ $message }}</small>
            </div>
        </div>
    @enderror

    @if(Auth::user()->is_banned_from_posting)
        <div class="alert alert-danger border-0 shadow-sm rounded-20 p-4 mb-4 d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3 text-danger">
                <i class="fa fa-ban fs-4"></i>
            </div>
            <div>
                <div class="fw-bold text-dark">Lapak Dibekukan Sebagian</div>
                <small class="text-muted">Anda tidak dapat menambah produk baru karena telah menerima <b>{{ Auth::user()->penalty_points }} retur disetujui</b>. Silakan perbaiki kualitas pelayanan Anda.</small>
            </div>
        </div>
    @endif

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

    <div class="card card-management">
        <div class="card-body p-0">
            <table class="table table-management table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">DETAIL PRODUK</th>
                        <th>KATEGORI</th>
                        <th class="text-center">HARGA</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <img src="{{ $product->image_url }}" class="product-img me-3 rounded-15 border shadow-sm">
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $product->name }}</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-maroon-soft text-maroon x-small">{{ strtoupper($product->condition) }}</span>
                                            @if($product->status === 'pending')
                                                <span class="text-warning x-small fw-bold"><i class="fa fa-clock me-1"></i> MENUNGGU VERIFIKASI</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ strtoupper($product->category->name) }}</span>
                                <div class="x-small text-muted mt-1 fw-normal">Stok: {{ $product->stock }}</div>
                            </td>
                            <td class="text-center py-4">
                                <div class="fw-bold text-dark mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="text-end pe-4 py-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-sm btn-light text-primary rounded-circle border shadow-sm" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" title="Edit">
                                        <i class="fa fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light text-danger rounded-circle border shadow-sm btn-delete" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada produk yang dijual.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus Produk?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9F1521',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    border: 'none',
                    borderRadius: '20px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
