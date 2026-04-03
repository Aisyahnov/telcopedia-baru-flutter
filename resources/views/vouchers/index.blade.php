@extends('layouts.app')
@section('title', 'Katalog Promo - Telcopedia')

@push('styles')
<style>
    .voucher-container { max-width: 900px; margin: 0 auto; }
    .ticket { display: flex; background: #fff; border-radius: 15px; overflow: hidden; position: relative; border: 1px solid #eee; transition: 0.3s; height: 160px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .ticket:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-color: #9F1521; }
    
    /* Gerigi Ticket Effect */
    .ticket::before, .ticket::after { content: ''; position: absolute; left: 24.5%; width: 24px; height: 24px; background: #f8f9fa; border-radius: 50%; z-index: 2; border: 1px solid #eee; }
    .ticket::before { top: -13px; }
    .ticket::after { bottom: -13px; }

    .ticket-left { width: 25%; background: #9F1521; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #fff; border-right: 2px dashed #eee; position: relative; }
    .ticket-left i { font-size: 2.5rem; opacity: 0.8; margin-bottom: 5px; }
    .ticket-left .type { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

    .ticket-body { width: 75%; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; }
    .voucher-info h4 { font-weight: 800; color: #9F1521; margin-bottom: 3px; }
    .voucher-info p { margin-bottom: 0; color: #6c757d; font-size: 0.85rem; }
    .expiry { font-size: 0.75rem; color: #999; margin-top: 8px; }

    .copy-btn { border: 2px dashed #9F1521; background: #fff5f5; color: #9F1521; padding: 10px 20px; border-radius: 10px; font-weight: 800; font-family: 'Monaco', 'Consolas', monospace; transition: 0.3s; cursor: pointer; }
    .copy-btn:hover { background: #9F1521; color: #fff; border-style: solid; }
    .copy-btn:active { transform: scale(0.95); }

    .claimed-indicator { font-size: 0.7rem; font-weight: 700; color: #198754; background: #e8f5e9; padding: 4px 10px; border-radius: 20px; display: inline-block; margin-top: 10px; }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Hujan Promo Telcopedia! 🎫</h2>
        <p class="text-muted">Gunakan kode voucher di bawah ini saat checkout untuk mendapatkan potongan harga spesial mahasiswa.</p>
    </div>

    <div class="voucher-container">
        @forelse($vouchers as $v)
            <div class="ticket mb-4">
                <div class="ticket-left">
                    <i class="fa-solid fa-ticket-simple"></i>
                    <span class="type">DISCOUNT</span>
                </div>
                <div class="ticket-body">
                    <div class="voucher-info">
                        <h4>Potongan Rp {{ number_format($v->discount_amount, 0, ',', '.') }}</h4>
                        <p>Minimal belanja: <span class="fw-bold text-dark">Rp {{ number_format($v->min_spend, 0, ',', '.') }}</span></p>
                        <div class="expiry">
                            <i class="fa-regular fa-clock me-1"></i> Berakhir: {{ $v->valid_until ? \Carbon\Carbon::parse($v->valid_until)->format('d M Y') : 'Selamanya' }}
                        </div>
                    </div>
                    <div>
                        <button class="copy-btn" onclick="copyVoucher('{{ $v->code }}', this)">
                            {{ $v->code }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fa-solid fa-face-frown fa-4x text-muted opacity-25 mb-3"></i>
                <h5 class="fw-bold">Yah, belum ada promo...</h5>
                <p class="text-muted">Cek kembali dalam beberapa hari untuk promo menarik lainnya dari BEM Telkom.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function copyVoucher(code, element) {
        navigator.clipboard.writeText(code).then(() => {
            const originalText = element.innerText;
            element.innerText = 'TERSALIN!';
            element.style.background = '#198754';
            element.style.color = '#fff';
            element.style.borderColor = '#198754';
            element.style.borderStyle = 'solid';

            setTimeout(() => {
                element.innerText = originalText;
                element.style.background = '';
                element.style.color = '';
                element.style.borderColor = '';
                element.style.borderStyle = '';
            }, 2000);
        });
    }
</script>
@endpush
@endsection
