@extends('layouts.app')
@section('title', 'Profil Saya - Telcopedia')

@push('styles')
<style>
    .profile-sidebar { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .profile-card { border-radius: 20px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.08); background: #fff; }
    .avatar-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto; }
    .avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .avatar-edit { position: absolute; bottom: 0; right: 0; background: #9F1521; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid #fff; transition: 0.3s; }
    .avatar-edit:hover { background: #7c111b; transform: scale(1.1); }
    .stat-box { background: #f8f9fa; border-radius: 15px; padding: 15px; text-align: center; border: 1px solid #eee; transition: 0.3s; }
    .stat-box:hover { border-color: #9F1521; transform: translateY(-3px); }
    .nav-pills .nav-link { border-radius: 10px; color: #6c757d; font-weight: 600; padding: 12px 20px; transition: 0.3s; }
    .nav-pills .nav-link.active { background-color: #9F1521; color: white; }
    .nav-pills .nav-link:not(.active):hover { background: #fff5f5; color: #9F1521; }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 12px; font-weight: 600; padding: 12px 30px; transition: 0.3s; border: none; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: translateY(-2px); }
    .text-maroon { color: #9F1521; }
    .form-control:focus { border-color: #9F1521; box-shadow: 0 0 0 0.2rem rgba(159, 21, 33, 0.1); }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row g-4">
        {{-- SIDEBAR --}}
        <div class="col-lg-4">
            <div class="card profile-sidebar p-4 mb-4 text-center">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    @method('PUT')
                    <div class="avatar-wrapper mb-3">
                        <img id="avatarPreview" src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=9F1521&color=fff&size=200&bold=true' }}">
                        <label for="photoInput" class="avatar-edit shadow">
                            <i class="fa fa-camera small"></i>
                            <input type="file" id="photoInput" name="photo" class="d-none" onchange="this.form.submit()">
                        </label>
                    </div>
                </form>

                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <div class="d-flex justify-content-center mb-4">
                    <span class="badge bg-secondary bg-opacity-25 text-dark border border-secondary-subtle px-3 py-2 rounded-pill font-monospace" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-id-card me-1 text-maroon"></i> <span class="fw-bold text-dark">NIM: {{ $user->nim }}</span>
                    </span>
                </div>

                {{-- STATS --}}
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="stat-box">
                            <i class="fa-solid fa-bag-shopping text-maroon mb-1"></i>
                            <h6 class="fw-bold mb-0">{{ $stats['total_orders'] }}</h6>
                            <small class="text-muted" style="font-size: 10px;">PESANAN</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <i class="fa-solid fa-heart text-maroon mb-1"></i>
                            <h6 class="fw-bold mb-0">{{ $stats['total_favorites'] }}</h6>
                            <small class="text-muted" style="font-size: 10px;">WISHLIST</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENU NAVIGATION TABS --}}
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                <button class="nav-link active text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-info" type="button">
                    <i class="fa-solid fa-user-pen me-2"></i> Informasi Dasar
                </button>
                <button class="nav-link text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-security" type="button">
                    <i class="fa-solid fa-shield-halved me-2"></i> Keamanan Akun
                </button>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-lg-8">
            <div class="tab-content" id="v-pills-tabContent">
                {{-- TAB: INFORMASI DASAR --}}
                <div class="tab-pane fade show active" id="tab-info">
                    <div class="card profile-card p-4 p-md-5">
                        <h5 class="fw-bold mb-4">Update Profil Saya</h5>
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control bg-light p-3" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">Nomor WhatsApp</label>
                                    <input type="text" name="phone" class="form-control bg-light p-3" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxx">
                                </div>
                                <div class="col-12 mb-5">
                                    <label class="form-label text-muted small fw-bold">Alamat / Asrama Pengiriman</label>
                                    <textarea name="address" class="form-control bg-light p-3" rows="4" placeholder="Detail alamat untuk memudahkan COD atau pengiriman...">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-maroon px-5 shadow">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB: KEAMANAN --}}
                <div class="tab-pane fade" id="tab-security">
                    <div class="card profile-card p-4 p-md-5">
                        <h5 class="fw-bold mb-4">Kata Sandi Baru</h5>
                        <p class="text-muted small mb-4">Lindungi akun Telcopedia Anda dengan kata sandi yang kuat (min 8 karakter).</p>
                        
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Kata Sandi Saat Ini</label>
                                <input type="password" name="current_password" class="form-control bg-light p-3" required>
                                @error('current_password') <small class="text-danger mt-1">{{ $message }}</small> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="form-control bg-light p-3" required>
                                    @error('password') <small class="text-danger mt-1">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control bg-light p-3" required>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-maroon px-5 shadow">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
