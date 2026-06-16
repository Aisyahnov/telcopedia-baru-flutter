@extends('layouts.app')
@section('title', 'Katalog Promo Eksklusif - Telcopedia')

@push('styles')
<style>
    /* Voucher Cards */
    .voucher-container { 
        max-width: 100%; 
        margin: 0 auto; 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 24px; 
    }
    
    .ticket-card {
        display: flex;
        background: #fff;
        border-radius: 24px;
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        overflow: hidden;
        text-decoration: none;
    }
    
    .ticket-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(159, 21, 33, 0.12);
        border-color: rgba(159, 21, 33, 0.2);
    }

    /* Shine effect on hover */
    .ticket-card::before {
        content: '';
        position: absolute;
        top: 0; left: -100%;
        width: 50%; height: 100%;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-25deg);
        transition: all 0.75s;
        z-index: 10;
        pointer-events: none;
    }
    .ticket-card:hover::before {
        left: 200%;
    }

    /* The visual cutouts for the ticket */
    .ticket-card::after, .ticket-card .cutout-top {
        content: '';
        position: absolute;
        left: 28%; /* Matches left-panel width approximately */
        width: 24px; height: 24px;
        background: #F8F9FA; /* Matches body background */
        border-radius: 50%;
        z-index: 5;
        box-shadow: inset 0 3px 6px rgba(0,0,0,0.02);
    }
    .ticket-card .cutout-top {
        top: -12px;
    }
    .ticket-card::after {
        bottom: -12px;
        box-shadow: inset 0 -3px 6px rgba(0,0,0,0.02);
    }

    /* Left Panel - The visual hook */
    .ticket-left {
        width: 28%;
        background: linear-gradient(135deg, #9F1521 0%, #7c111b 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #fff;
        position: relative;
        overflow: hidden;
        border-right: 2px dashed rgba(255,255,255,0.3);
    }
    /* Abstract background pattern in the left panel */
    .ticket-left .bg-pattern {
        position: absolute;
        font-size: 6rem;
        opacity: 0.05;
        transform: rotate(-15deg);
        right: -15px;
        bottom: -20px;
    }
    .ticket-left i.main-icon {
        font-size: 2.2rem;
        margin-bottom: 10px;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
        transition: 0.3s;
    }
    .ticket-card:hover .ticket-left i.main-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .ticket-left .type {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 2px;
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        backdrop-filter: blur(4px);
    }

    /* Right Panel - Information */
    .ticket-right {
        width: 72%;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }
    
    .voucher-info h3 {
        font-weight: 900;
        color: #1a1a1a;
        font-size: 1.4rem;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    .voucher-info .highlight {
        color: #9F1521;
    }
    .voucher-info .min-spend {
        display: inline-block;
        font-size: 0.85rem;
        color: #666;
        background: #f8f9fa;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 500;
        margin-bottom: 15px;
    }
    .voucher-info .expiry {
        font-size: 0.8rem;
        color: #888;
        display: flex;
        align-items: center;
        font-weight: 600;
    }
    .voucher-info .expiry i {
        color: #dc3545;
        margin-right: 6px;
    }

    /* Copy Button */
    .copy-action {
        text-align: center;
    }
    .code-box {
        background: #fff5f5;
        border: 2px dashed #ffcdd2;
        color: #9F1521;
        font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
        font-weight: 800;
        font-size: 1.1rem;
        padding: 12px 24px;
        border-radius: 12px;
        margin-bottom: 10px;
        letter-spacing: 1px;
        transition: 0.3s;
    }
    .ticket-card:hover .code-box {
        border-color: #9F1521;
        background: white;
        box-shadow: 0 5px 15px rgba(159, 21, 33, 0.1);
    }
    .btn-copy {
        background: #1a1a1a;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
    }
    .btn-copy:hover {
        background: #9F1521;
        transform: translateY(-2px);
    }
    .btn-copy:active {
        transform: translateY(0);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .voucher-container { grid-template-columns: 1fr; max-width: 750px; }
    }
    @media (max-width: 768px) {
        .ticket-card { flex-direction: column; }
        .ticket-left { width: 100%; padding: 25px; border-right: none; border-bottom: 2px dashed rgba(255,255,255,0.3); }
        .ticket-right { width: 100%; flex-direction: column; text-align: center; gap: 15px; padding: 20px; }
        .ticket-card::after, .ticket-card .cutout-top {
            left: 50%; transform: translateX(-50%);
        }
        .ticket-card .cutout-top { top: 20%; }
        .ticket-card::after { bottom: 75%; box-shadow: none; }
        .voucher-info .min-spend { margin: 10px auto; }
        .voucher-info .expiry { justify-content: center; }
        .copy-action { width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    
    <div class="text-center mb-5 mt-4">
        <h2 class="fw-900" style="letter-spacing: -1px;">Katalog <span class="text-maroon">Voucher & Promo</span></h2>
        <p class="text-muted">Gunakan kode voucher di bawah ini untuk mendapatkan potongan harga spesial.</p>
    </div>

    <div class="voucher-container">
        @forelse($vouchers as $v)
            <div class="ticket-card">
                <div class="cutout-top"></div>
                
                <div class="ticket-left">
                    <i class="fa-solid fa-gift bg-pattern"></i>
                    <i class="fa-solid fa-tags main-icon"></i>
                    <div class="type">DISCOUNT</div>
                </div>
                
                <div class="ticket-right">
                    <div class="voucher-info">
                        <h3><span class="highlight">Rp {{ number_format($v->discount_amount, 0, ',', '.') }}</span> OFF</h3>
                        <div class="min-spend">
                            Min. Belanja: <strong class="text-dark">Rp {{ number_format($v->min_spend, 0, ',', '.') }}</strong>
                        </div>
                        <div class="expiry">
                            <i class="fa-solid fa-hourglass-half"></i> 
                            Berlaku hingga: <span class="text-dark ms-1">{{ $v->valid_until ? \Carbon\Carbon::parse($v->valid_until)->translatedFormat('d F Y') : 'Tanpa Batas Waktu' }}</span>
                        </div>
                    </div>
                    <div class="copy-action">
                        <div class="code-box" id="code-{{ $v->id }}">
                            {{ strtoupper($v->code) }}
                        </div>
                        <button class="btn-copy" onclick="copyVoucher('{{ $v->code }}', 'code-{{ $v->id }}', this)">
                            <i class="fa-regular fa-copy"></i> Salin Kode
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border mt-4">
                <div class="mb-4 position-relative d-inline-block">
                    <i class="fa-solid fa-ticket fa-4x text-muted opacity-25"></i>
                    <i class="fa-solid fa-xmark position-absolute text-danger" style="font-size: 2rem; right: -10px; bottom: -5px;"></i>
                </div>
                <h4 class="fw-bold text-dark">Wah, belum ada promo aktif</h4>
                <p class="text-muted mb-0">Pantau terus halaman ini, Telcopedia akan segera membagikan voucher menarik lainnya!</p>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-5">
        {{ $vouchers->links('pagination::bootstrap-5') }}
    </div>
</div>

@push('scripts')
<script>
    function copyVoucher(code, boxId, btnElement) {
        navigator.clipboard.writeText(code).then(() => {
            const box = document.getElementById(boxId);
            const originalBtnHtml = btnElement.innerHTML;
            
            // Effect on the box
            box.style.background = '#198754';
            box.style.color = 'white';
            box.style.borderColor = '#198754';
            
            // Effect on the button
            btnElement.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
            btnElement.style.background = '#198754';
            
            setTimeout(() => {
                box.style.background = '';
                box.style.color = '';
                box.style.borderColor = '';
                
                btnElement.innerHTML = originalBtnHtml;
                btnElement.style.background = '';
            }, 2500);
        });
    }
</script>
@endpush
@endsection
