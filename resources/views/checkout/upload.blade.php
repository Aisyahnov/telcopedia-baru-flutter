@extends('layouts.app')
@section('title', 'Pembayaran Pesanan - Telcopedia')

@push('styles')
<style>
    .payment-card { border-radius: 20px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.06); }
    .bank-item { background: #f8f9fa; border-radius: 12px; border: 1px solid #eee; transition: 0.3s; }
    .bank-item:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #9F1521; }
    .btn-maroon { background-color: #9F1521; color: white; border-radius: 12px; font-weight: 600; padding: 12px; transition: 0.3s; border: none; }
    .btn-maroon:hover { background-color: #7c111b; color: white; transform: translateY(-2px); }
    .upload-area { border: 2px dashed #dee2e6; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: 0.3s; }
    .upload-area:hover { border-color: #9F1521; background: #fffcfc; }
    .text-maroon { color: #9F1521; }
</style>
@endpush

@section('content')
<div class="container my-5 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card payment-card overflow-hidden">
                <div class="row g-0">
                    {{-- LEFT: INSTRUCTIONS --}}
                    <div class="col-md-6 border-end p-5 bg-light bg-opacity-50">
                        <div class="mb-4 text-center">
                            <span class="badge bg-maroon mb-2">Langkah 1: Pembayaran</span>
                            <h4 class="fw-bold">Metode Pembayaran Manual</h4>
                            <p class="text-muted small">Silakan pilih salah satu rekening di bawah ini untuk menyelesaikan pesanan <strong>#ORD-{{ $order->id }}</strong>.</p>
                        </div>

                        <div class="mb-3 bank-item p-3 d-flex align-items-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" width="60" class="me-3">
                            <div>
                                <small class="text-muted d-block">Bank Mandiri</small>
                                <span class="fw-bold fs-5">131-00-1234-5678</span>
                                <small class="d-block text-muted">a/n Telcopedia Mahasiswa</small>
                            </div>
                        </div>

                        <div class="mb-4 bank-item p-3 d-flex align-items-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" width="60" class="me-3">
                            <div>
                                <small class="text-muted d-block">E-Wallet DANA</small>
                                <span class="fw-bold fs-5">0812-3456-7890</span>
                                <small class="d-block text-muted">a/n Telcopedia Mahasiswa</small>
                            </div>
                        </div>

                        <div class="bg-maroon-subtle border-0 rounded-3 py-3 px-4">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fa fa-info-circle me-2"></i>
                                <span class="fw-bold">Nominal yang ditransfer:</span>
                            </div>
                            <h3 class="fw-bold text-maroon mb-0">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h3>
                        </div>
                    </div>

                    {{-- RIGHT: UPLOAD FORM --}}
                    <div class="col-md-6 p-5 bg-white">
                        <div class="mb-5 text-center">
                            <span class="badge bg-maroon mb-2">Langkah 2: Konfirmasi</span>
                            <h4 class="fw-bold text-center">Upload Bukti Transfer</h4>
                            <p class="text-muted small">Lampirkan struk atau screenshot bukti bayar Anda.</p>
                        </div>

                        <form method="POST" enctype="multipart/form-data" action="{{ route('checkout.upload_bukti', $order->id) }}">
                            @csrf
                            
                            @if(session('success'))
                                <div class="alert alert-success border-0 small shadow-sm">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger border-0 small shadow-sm">{{ session('error') }}</div>
                            @endif

                            <div class="mb-4">
                                <div class="upload-area" id="upload-clickable">
                                    <i class="fa-solid fa-cloud-arrow-up text-muted fs-1 mb-3"></i>
                                    <h6 class="fw-bold mb-1">Klik untuk memilih file</h6>
                                    <p class="text-muted xsmall mb-0" style="font-size: 11px;">Maksimal ukuran file 10MB (JPG, PNG)</p>
                                    <input type="file" name="payment_proof" id="file-input" class="d-none" required accept="image/*">
                                </div>
                                <div id="file-name" class="text-center mt-2 small text-success fw-bold d-none"></div>
                            </div>

                            <button type="submit" class="btn btn-maroon w-100 py-3 shadow-sm">
                                <i class="fa-solid fa-check-circle me-2"></i> Kirim Konfirmasi
                            </button>

                            <div class="text-center mt-4">
                                <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted small fw-bold">
                                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Riwayat
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('upload-clickable').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function() {
        const fileName = this.files[0]?.name;
        if (fileName) {
            const fileNameDiv = document.getElementById('file-name');
            fileNameDiv.textContent = 'Terpilih: ' + fileName;
            fileNameDiv.classList.remove('d-none');
            document.querySelector('.upload-area i').classList.remove('text-muted');
            document.querySelector('.upload-area i').classList.add('text-success');
        }
    });
</script>
@endsection
