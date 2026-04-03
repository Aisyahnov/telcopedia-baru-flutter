@extends('layouts.app')
@section('title', 'Edit Produk - Telcopedia')

@section('content')
<div class="container my-5 max-w-700" style="max-width: 700px;">
    
    <div class="mb-4 d-flex align-items-center">
        <a href="{{ route('seller.products.index') }}" class="btn btn-light rounded-circle me-3 text-muted"><i class="fa fa-arrow-left"></i></a>
        <h4 class="fw-bold mb-0">Sunting Detail Barang</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <h6 class="fw-bold text-danger mb-4 border-bottom pb-2">Informasi Dasar</h6>
                
                <div class="mb-4">
                    <label class="form-label">Nama / Judul Barang</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="category_id" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->id == $product->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sisa Stok (Pcs)</label>
                        <input type="number" class="form-control" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Harga Barang (Rupiah)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 fw-bold">Rp</span>
                        <input type="number" class="form-control border-start-0 ps-0" name="price" value="{{ old('price', $product->price) }}" min="500" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi & Kondisi Barang</label>
                    <textarea class="form-control" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="form-label">Link/Nama File Gambar Produk (Sementara)</label>
                    <input type="text" class="form-control" name="image" value="{{ old('image', $product->image) }}">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-danger btn-lg rounded-pill shadow-sm">Simpan Perubahan <i class="fa fa-pencil-alt ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
