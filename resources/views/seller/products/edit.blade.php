@extends('layouts.app')
@section('title', 'Sunting Detail Barang - Telcopedia')

@push('styles')
<style>
    .upload-card { border-radius: 24px; border: none; overflow: hidden; }
    .section-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1.5px; color: #9F1521; font-weight: 800; display: flex; align-items: center; margin-bottom: 25px; }
    .section-title::after { content: ""; flex: 1; height: 1px; background: #eee; margin-left: 15px; }
    
    .form-label { font-weight: 700; color: #444; font-size: 0.85rem; margin-bottom: 8px; }
    .form-control, .form-select { 
        border-radius: 12px; 
        padding: 12px 18px; 
        border: 1px solid #EAEAEA; 
        background-color: #FDFDFD; 
        transition: 0.3s; 
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus { 
        background-color: #fff; 
        border-color: #9F1521; 
        box-shadow: 0 0 0 4px rgba(159, 21, 33, 0.05); 
    }
    
    .input-group-text { border-radius: 12px 0 0 12px; border: 1px solid #EAEAEA; background: #F8F9FA; color: #9F1521; font-weight: 700; border-right: none; }
    .input-group .form-control { border-radius: 0 12px 12px 0; border-left: none; }
    
    .image-preview-box { 
        border: 2px dashed #DEDEDE; 
        border-radius: 20px; 
        padding: 10px; 
        text-align: center; 
        cursor: pointer; 
        transition: 0.3s; 
        background: #F9F9F9; 
        position: relative;
    }
    .image-preview-box:hover { border-color: #9F1521; background: #FFF5F5; }
    
    .btn-save { border-radius: 100px; padding: 16px; font-weight: 800; letter-spacing: 0.5px; transition: 0.3s; background: #9F1521; border: none; }
    .btn-save:hover { background: #7c111b; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(159, 21, 33, 0.2); }
</style>
@endpush

@section('hero_title', 'Sunting Detail Barang')
@section('hero_subtitle', 'Perbarui informasi produk agar tetap relevan bagi pembeli.')
@section('hero_emoji', '')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card upload-card shadow-lg border-0">
            <div class="card-body p-4 p-md-5 bg-white">
                <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-5">
                        <!-- Kolom Kiri: Informasi Barang -->
                        <div class="col-lg-7">
                            <div class="mb-5">
                                <h6 class="section-title">Informasi Dasar</h6>
                                
                                <div class="mb-4">
                                    <label class="form-label">Nama Barang / Judul Iklan</label>
                                    <input type="text" class="form-control shadow-sm border-0 bg-light" name="name" value="{{ old('name', $product->name) }}" required>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Utama</label>
                                        <select class="form-select border-0 bg-light shadow-sm" id="parent_category" required>
                                            <option value="">Pilih Kategori Utama</option>
                                            @foreach($categories as $cat)
                                                @if(!$cat->parent_id)
                                                    <option value="{{ $cat->id }}" {{ $cat->id == ($product->category->parent_id ?? $product->category_id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sub-Kategori</label>
                                        <select class="form-select border-0 bg-light shadow-sm" name="category_id" id="sub_category" required>
                                            <option value="{{ $product->category_id }}">{{ $product->category->name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kondisi Barang</label>
                                        <select class="form-select border-0 bg-light shadow-sm" name="condition" required>
                                            <option value="New" {{ $product->condition == 'New' ? 'selected' : '' }}>Brand New (Segel)</option>
                                            <option value="Like New" {{ $product->condition == 'Like New' ? 'selected' : '' }}>Like New (Mulus 99%)</option>
                                            <option value="Very Good" {{ $product->condition == 'Very Good' ? 'selected' : '' }}>Very Good (Mulus 95%)</option>
                                            <option value="Good" {{ $product->condition == 'Good' ? 'selected' : '' }}>Good (Lecet Pemakaian)</option>
                                            <option value="Pre-Loved" {{ $product->condition == 'Pre-Loved' ? 'selected' : '' }}>Pre-Loved (Apa adanya)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Jual</label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text border-0 bg-light text-muted">Rp</span>
                                            <input type="number" class="form-control border-0 bg-light" name="price" value="{{ old('price', $product->price) }}" min="500" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Stok Barang (Pcs)</label>
                                        <input type="number" class="form-control border-0 bg-light shadow-sm" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <h6 class="section-title">Deskripsi Produk</h6>
                                <label class="form-label">Deskripsi Lengkap</label>
                                <textarea class="form-control border-0 bg-light shadow-sm" name="description" rows="8" required style="resize: none;">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Media/Foto -->
                        <div class="col-lg-5">
                            <div class="mb-5">
                                <h6 class="section-title">Media & Visual</h6>
                                
                                <div class="mb-4">
                                    <label class="form-label">Foto Utama (Thumbnail)</label>
                                    <div class="image-preview-box shadow-sm" onclick="document.getElementById('main_image').click()">
                                        <img src="{{ $product->image_url }}" id="img_preview" class="img-fluid rounded-4 shadow" style="max-height: 250px; width: 100%; object-fit: cover;">
                                        <div class="mt-3 text-maroon small fw-bold">
                                            <i class="fa fa-sync me-1"></i> Klik untuk ganti foto
                                        </div>
                                        <input type="file" id="main_image" name="image" accept="image/*" class="d-none" onchange="previewMain(this)">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Galeri Tambahan</label>
                                    @if($product->images->count() > 0)
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @foreach($product->images as $img)
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $img->image_url) }}" class="rounded-3 border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="bg-light p-3 rounded-4 border border-dashed text-center">
                                        <input type="file" class="form-control form-control-sm border-0 bg-transparent" name="gallery[]" accept="image/*" multiple>
                                        <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">Tambah foto baru ke galeri.</small>
                                    </div>
                                </div>

                                <div class="d-grid gap-3">
                                    <button type="submit" class="btn btn-danger btn-save shadow">
                                        Simpan Perubahan <i class="fa fa-save ms-2"></i>
                                    </button>
                                    <a href="{{ route('seller.products.index') }}" class="btn btn-light rounded-pill py-3 fw-bold text-muted">
                                        Batal dan Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const categories = @json($categories);
    const parentSelect = document.getElementById('parent_category');
    const subSelect = document.getElementById('sub_category');
    const currentCategoryId = {{ $product->category_id }};

    function updateSubCategories(parentId, selectedId = null) {
        subSelect.innerHTML = '<option value="">Pilih Sub-Kategori</option>';
        if (parentId) {
            const parent = categories.find(c => c.id == parentId);
            const subcats = parent ? (parent.subcategories || parent.sub_categories || []) : [];

            if (subcats.length > 0) {
                subSelect.disabled = false;
                subcats.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.name;
                    if (selectedId && sub.id == selectedId) opt.selected = true;
                    subSelect.appendChild(opt);
                });
            } else {
                const opt = document.createElement('option');
                opt.value = parentId;
                opt.textContent = 'Gunakan Kategori Utama (Tanpa Sub)';
                if (selectedId && parentId == selectedId) opt.selected = true;
                subSelect.appendChild(opt);
                subSelect.disabled = false;
            }
        } else {
            subSelect.disabled = true;
            subSelect.innerHTML = '<option value="">Pilih Kategori Utama Dahulu</option>';
        }
    }

    parentSelect.addEventListener('change', function() {
        updateSubCategories(this.value);
    });

    // Initialize on load
    if (parentSelect.value) {
        updateSubCategories(parentSelect.value, currentCategoryId);
    }

    function previewMain(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img_preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
