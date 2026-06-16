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

    /* Scanning Animation */
    .scanner-line {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: #9F1521;
        box-shadow: 0 0 15px #9F1521;
        animation: scan 2s linear infinite;
        z-index: 10;
    }
    @keyframes scan {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }
</style>
@endpush

@if(Auth::user()->role !== 'buyer')
    @section('hero_title', 'Pengaturan Akun & Lapak')
    @section('hero_subtitle', 'Kelola informasi profil Anda untuk profil publik yang lebih menarik.')
    @section('hero_emoji', '⚙️')
@endif

@section('content')

<div class="container py-5">
    <div class="row g-4">
        {{-- PROFILE SIDEBAR --}}
        <div class="col-lg-4">
            <div class="card profile-sidebar p-4 mb-4 text-center">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    @method('PUT')
                    <div class="avatar-wrapper mb-3">
                        <img id="avatarPreview" src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=9F1521&color=fff&size=200&bold=true' }}">
                        <label for="photoInput" class="avatar-edit shadow" title="Upload Foto">
                            <i class="fa fa-upload small"></i>
                            <input type="file" id="photoInput" name="photo" class="d-none" onchange="this.form.submit()" accept="image/*">
                        </label>
                    </div>
                    <input type="hidden" name="photo_base64" id="photoBase64">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#webcamModal">
                        <i class="fa fa-camera me-1"></i> Buka Kamera
                    </button>
                </form>

                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <div class="d-flex justify-content-center mb-4">
                    <span class="badge bg-secondary bg-opacity-25 text-dark border border-secondary-subtle px-3 py-2 rounded-pill font-monospace" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-id-card me-1 text-maroon"></i> <span class="fw-bold text-dark">NIM: {{ $user->nim }}</span>
                    </span>
                </div>

                <div class="mb-4 mt-2">
                    @if($user->is_verified)
                        <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 w-100">
                            <i class="fa fa-check-circle me-1"></i> Identitas Terverifikasi
                        </div>
                    @else
                        <button type="button" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold shadow-sm py-2" id="btnShowVerifyModal">
                            <i class="fa fa-shield-alt me-1"></i> Verifikasi Identitas
                        </button>
                    @endif
                </div>

                <div class="row g-2 mb-2">
                    @if(Auth::user()->role === 'seller')
                    <div class="col-6">
                        <div class="stat-box">
                            <i class="fa-solid fa-box text-maroon mb-1"></i>
                            <h6 class="fw-bold mb-0">{{ $stats['total_products'] ?? 0 }}</h6>
                            <small class="text-muted" style="font-size: 10px;">PRODUK</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <i class="fa-solid fa-receipt text-maroon mb-1"></i>
                            <h6 class="fw-bold mb-0">{{ $stats['seller_orders'] ?? 0 }}</h6>
                            <small class="text-muted" style="font-size: 10px;">PESANAN TOKO</small>
                        </div>
                    </div>
                    @else
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
                    @endif
                </div>
            </div>

            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                <button class="nav-link {{ $errors->has('current_password') || $errors->has('password') ? '' : 'active' }} text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-info" type="button">
                    <i class="fa-solid fa-user-pen me-2"></i> Informasi Dasar
                </button>
                <button class="nav-link {{ $errors->has('current_password') || $errors->has('password') ? 'active' : '' }} text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-security" type="button">
                    <i class="fa-solid fa-shield-halved me-2"></i> Keamanan Akun
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="tab-content" id="v-pills-tabContent">
                {{-- TAB: INFORMASI DASAR --}}
                <div class="tab-pane fade {{ $errors->has('current_password') || $errors->has('password') ? '' : 'show active' }}" id="tab-info">
                    <div class="card card-management p-4 p-md-5 border-0 bg-white">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-maroon-soft text-maroon rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-id-card-clip fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Informasi Dasar</h5>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="bg-light p-4 rounded-24 mb-5 border border-dashed">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative me-4">
                                                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=9F1521&color=fff&size=100&bold=true' }}" class="rounded-circle border border-4 border-white shadow-sm" width="100" height="100" style="object-fit: cover;">
                                                <label for="photoInputMain" class="position-absolute bottom-0 end-0 bg-maroon text-white rounded-circle d-flex align-items-center justify-content-center border border-2 border-white cursor-pointer shadow-sm" style="width: 32px; height: 32px;">
                                                    <i class="fa fa-camera small"></i>
                                                    <input type="file" id="photoInputMain" name="photo" class="d-none" accept="image/*">
                                                </label>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold">Foto Profil</h6>
                                                <p class="small text-muted mb-0">Format: JPG, PNG, JPEG. Maks 2MB.</p>
                                                @error('photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                <span class="badge bg-maroon-soft text-maroon rounded-pill mt-2 fw-bold" style="font-size: 0.65rem;">MEMBER SINCE {{ $user->created_at->format('Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="x-small text-muted fw-bold mb-2 text-uppercase">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control rounded-pill px-4 py-3 border-0 shadow-sm @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <small class="text-danger mt-1">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="x-small text-muted fw-bold mb-2 text-uppercase">Nomor WhatsApp</label>
                                        <input type="text" name="phone" class="form-control rounded-pill px-4 py-3 border-0 shadow-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxx">
                                        @error('phone') <small class="text-danger mt-1">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="x-small text-muted fw-bold mb-2 text-uppercase">Alamat Lengkap</label>
                                        <textarea name="address" class="form-control rounded-20 px-4 py-3 border-0 shadow-sm @error('address') is-invalid @enderror" rows="3" placeholder="Detail alamat untuk memudahkan transaksi...">{{ old('address', $user->address) }}</textarea>
                                        @error('address') <small class="text-danger mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-3">
                                    <h6 class="fw-bold mb-0">Verifikasi Identitas (Selfie dengan KTM)</h6>
                                    <hr class="flex-grow-1 ms-3 opacity-10">
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        @if($user->ktm)
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $user->ktm) }}" class="rounded-20 border shadow-sm w-100" style="height: 160px; object-fit: cover;" alt="KTM">
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-success shadow-sm rounded-pill"><i class="fa fa-check me-1"></i> Terupload</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-light border rounded-20 d-flex flex-column align-items-center justify-content-center text-muted" style="height: 160px; border-style: dashed !important;">
                                                <i class="fa fa-id-card fa-3x opacity-25 mb-2"></i>
                                                <span class="x-small fw-bold">KTM BELUM DIUPLOAD</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-7">
                                        <p class="text-muted small mb-2">Upload foto <strong>Selfie sambil memegang KTM Fisik</strong> Anda untuk mendapatkan lencana <strong class="text-success"><i class="fa fa-check-circle"></i> Terverifikasi</strong>.</p>
                                        <p class="text-danger fw-bold" style="font-size: 0.7rem;"><em><i class="fa fa-exclamation-triangle"></i> WAJIB menggunakan KTM Fisik asli. KTM digital di layar HP akan otomatis ditolak oleh sistem keamanan KYC.</em></p>
                                        <input type="file" name="ktm" class="form-control rounded-pill px-4 py-2 border-0 bg-light shadow-sm" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-maroon px-5 py-3 shadow-lg">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB: KEAMANAN --}}
                <div class="tab-pane fade {{ $errors->has('current_password') || $errors->has('password') ? 'show active' : '' }}" id="tab-security">
                    <div class="card card-management p-4 p-md-5 border-0 bg-white">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-lock fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Keamanan Akun</h5>
                        </div>

                        <p class="text-muted small mb-4">Ganti kata sandi secara berkala untuk menjaga keamanan akun Telcopedia Anda.</p>
                        
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="bg-light p-4 rounded-24 mb-4 border">
                                <div class="mb-4">
                                    <label class="x-small text-muted fw-bold mb-2 text-uppercase">Kata Sandi Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control rounded-pill px-4 py-3 border-0 shadow-sm" required>
                                    @error('current_password') <small class="text-danger mt-1 ms-3">{{ $message }}</small> @enderror
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="x-small text-muted fw-bold mb-2 text-uppercase">Kata Sandi Baru</label>
                                        <input type="password" name="password" class="form-control rounded-pill px-4 py-3 border-0 shadow-sm" required>
                                        @error('password') <small class="text-danger mt-1 ms-3">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="x-small text-muted fw-bold mb-2 text-uppercase">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control rounded-pill px-4 py-3 border-0 shadow-sm" required>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-dark px-5 py-3 rounded-pill fw-bold shadow-lg">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Webcam Modal -->
<div class="modal fade" id="webcamModal" tabindex="-1" aria-labelledby="webcamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="webcamModalLabel">Ambil Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeWebcamBtn"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="bg-dark rounded-3 overflow-hidden position-relative mb-3 mx-auto" style="width: 300px; height: 300px;">
                    <video id="webcamVideo" width="300" height="300" autoplay playsinline style="object-fit: cover; transform: scaleX(-1);"></video>
                    <canvas id="webcamCanvas" width="300" height="300" class="d-none position-absolute top-0 start-0"></canvas>
                </div>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="captureBtn">
                    <i class="fa fa-camera me-2"></i>Ambil Foto
                </button>
                <div id="webcamActions" class="d-none mt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 me-2" id="retakeBtn">Ulangi</button>
                    <button type="button" class="btn btn-maroon rounded-pill px-4" id="saveWebcamBtn">Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Identity Modal (Simulated) -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Verifikasi Identitas Kampus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnVerifyClose"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div id="verifyStep1">
                    <p class="text-muted small mb-4">Sistem AI Telcopedia akan memindai wajah Anda dan mencocokkannya dengan data pada KTM yang Anda pegang untuk memastikan liveness dan keaslian dokumen.</p>
                    <div class="d-flex justify-content-center mb-4">
                        <div class="text-center">
                            <img src="{{ $user->ktm ? asset('storage/' . $user->ktm) : 'https://placehold.co/150x150?text=No+Selfie' }}" class="rounded-3 border shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="small text-muted mt-2 fw-bold">Selfie dengan KTM</div>
                        </div>
                    </div>
                    @if(!$user->ktm)
                        <div class="alert alert-danger small py-2">
                            Upload foto Selfie dengan KTM Anda sebelum melakukan verifikasi.
                        </div>
                        <button class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    @else
                        <button class="btn btn-maroon w-100 rounded-pill py-2 shadow" id="btnStartVerify">Mulai Identifikasi Keaslian Wajah</button>
                    @endif
                </div>

                <div id="verifyStep2" class="d-none py-4">
                    <div class="position-relative mx-auto mb-4" style="width: 150px; height: 150px;">
                        <img src="{{ $user->ktm ? asset('storage/' . $user->ktm) : '' }}" class="rounded-3 border border-4 border-light shadow" style="width: 150px; height: 150px; object-fit: cover;">
                        <div class="scanner-line"></div>
                    </div>
                    <h6 class="fw-bold mb-1">Menganalisis Liveness & Dokumen...</h6>
                    <p class="text-muted small mb-0" id="verifyStatusText">Mengekstrak data wajah dari foto selfie...</p>
                    <div class="progress mt-4 rounded-pill" style="height: 6px;">
                        <div id="verifyProgress" class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <div id="verifyStep3" class="d-none py-4">
                    <div class="mb-3">
                        <i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Verifikasi Berhasil!</h5>
                    <p class="text-muted small">Wajah Anda 98.5% cocok dengan KTM. Akun Anda kini berstatus Terverifikasi.</p>
                    <button class="btn btn-success w-100 rounded-pill mt-3 py-2 shadow-sm" onclick="location.reload()">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const webcamModal = document.getElementById('webcamModal');
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('webcamCanvas');
    const captureBtn = document.getElementById('captureBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    const saveWebcamBtn = document.getElementById('saveWebcamBtn');
    const webcamActions = document.getElementById('webcamActions');
    const photoBase64Input = document.getElementById('photoBase64');
    const photoForm = document.getElementById('photoForm');
    let stream;
    let myModalInstance;

    document.addEventListener("DOMContentLoaded", function() {
        @if(!$user->photo)
            myModalInstance = new bootstrap.Modal(document.getElementById('webcamModal'));
            myModalInstance.show();
        @endif
    });

    webcamModal.addEventListener('show.bs.modal', function () {
        navigator.mediaDevices.getUserMedia({ video: { width: 300, height: 300 } })
            .then(function(s) {
                stream = s;
                video.srcObject = stream;
                video.classList.remove('d-none');
                canvas.classList.add('d-none');
                captureBtn.classList.remove('d-none');
                webcamActions.classList.add('d-none');
            })
            .catch(function(err) {
                console.error("Error accessing webcam: ", err);
                alert("Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin.");
            });
    });

    webcamModal.addEventListener('hide.bs.modal', function () {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });

    captureBtn.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        context.clearRect(0, 0, 300, 300);
        context.translate(300, 0);
        context.scale(-1, 1);
        context.drawImage(video, 0, 0, 300, 300);
        context.setTransform(1, 0, 0, 1, 0, 0);
        video.classList.add('d-none');
        canvas.classList.remove('d-none');
        captureBtn.classList.add('d-none');
        webcamActions.classList.remove('d-none');
    });

    retakeBtn.addEventListener('click', function() {
        video.classList.remove('d-none');
        canvas.classList.add('d-none');
        captureBtn.classList.remove('d-none');
        webcamActions.classList.add('d-none');
    });

    saveWebcamBtn.addEventListener('click', function() {
        const dataURL = canvas.toDataURL('image/png');
        photoBase64Input.value = dataURL;
        
        // Tutup stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Submit form
        photoForm.submit();
    });

    // Verification Logic (Simulated)
    const btnShowVerifyModal = document.getElementById('btnShowVerifyModal');
    if (btnShowVerifyModal) {
        const verifyModal = new bootstrap.Modal(document.getElementById('verifyModal'));
        const btnStartVerify = document.getElementById('btnStartVerify');
        const verifyStep1 = document.getElementById('verifyStep1');
        const verifyStep2 = document.getElementById('verifyStep2');
        const verifyStep3 = document.getElementById('verifyStep3');
        const verifyProgress = document.getElementById('verifyProgress');
        const verifyStatusText = document.getElementById('verifyStatusText');

        btnShowVerifyModal.addEventListener('click', () => verifyModal.show());

        btnStartVerify.addEventListener('click', function() {
            verifyStep1.classList.add('d-none');
            verifyStep2.classList.remove('d-none');
            document.getElementById('btnVerifyClose').classList.add('d-none');

            let progress = 0;
            const statuses = [
                'Mendeteksi fitur wajah...',
                'Mengekstrak data biometrik...',
                'Membandingkan dengan foto KTM...',
                'Memvalidasi keaslian dokumen...',
                'Menyelesaikan verifikasi...'
            ];

            const interval = setInterval(() => {
                progress += 2;
                verifyProgress.style.width = progress + '%';
                
                if (progress % 20 === 0) {
                    verifyStatusText.innerText = statuses[Math.floor(progress / 20) - 1] || statuses[4];
                }

                if (progress >= 100) {
                    clearInterval(interval);
                    
                    // Call backend to update status
                    fetch('{{ route("profile.verify") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            verifyStep2.classList.add('d-none');
                            verifyStep3.classList.remove('d-none');
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat verifikasi.');
                        location.reload();
                    });
                }
            }, 50); // ~5 seconds total
        });
    }
</script>
@endpush
@endsection
