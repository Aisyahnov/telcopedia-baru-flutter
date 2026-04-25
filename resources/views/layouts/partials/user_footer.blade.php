<!-- FOOTER -->
<footer class="footer-premium pt-5 pb-4 mt-5" style="background-color: #2d2d2d;">
    <div class="container">
        <div class="row g-4">
            <!-- BRAND INFO -->
            <div class="col-lg-6 col-md-12">
                <p class="text-light opacity-75 small pe-lg-5 mb-4" style="line-height: 1.8;">
                    Telcopedia adalah platform jual-beli barang bekas (preloved) eksklusif bagi mahasiswa Telkom
                    University. Temukan barang berkualitas dengan harga mahasiswa dengan aman dan terpercaya.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="social-icon bg-white bg-opacity-10 text-white shadow-sm"><i
                            class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon bg-white bg-opacity-10 text-white shadow-sm"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon bg-white bg-opacity-10 text-white shadow-sm"><i
                            class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon bg-white bg-opacity-10 text-white shadow-sm"><i
                            class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- QUICK LINKS -->
            <div class="col-lg-3 col-md-4">
                <h6 class="fw-bold text-white mb-4 footer-title">Telcopedia</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('category.index') }}">Kategori Produk</a></li>
                    <li><a href="{{ route('vouchers.index') }}">Voucher Promo</a></li>
                    <li><a href="{{ route('home') }}">Produk Terbaru</a></li>
                </ul>
            </div>

            <!-- BANTUAN -->
            <div class="col-lg-3 col-md-4">
                <h6 class="fw-bold text-white mb-4 footer-title">Bantuan</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('contact') }}">Hubungi Kami</a></li>
                    <li><a href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('terms') }}">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('contact') }}">FAQ / Bantuan</a></li>
                </ul>
            </div>


        </div>

        <hr class="my-5 opacity-5">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white opacity-50 small mb-0">&copy; {{ date('Y') }} <strong>Telcopedia</strong>. All
                    rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <p class="text-white opacity-50 small mb-0">Built with <i class="fa fa-heart text-maroon mx-1"></i> by
                    Mahasiswa Telkom</p>
            </div>
        </div>
    </div>
</footer>

<style>
    /* PREMIUM FOOTER STYLES */
    .footer-premium {
        background-color: #2d2d2d;
        color: #ffffff;
    }

    .footer-title {
        position: relative;
        padding-bottom: 10px;
        font-size: 0.95rem;
        color: #ffffff !important;
    }

    .footer-title::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 30px;
        height: 3px;
        background-color: var(--telco-maroon);
        border-radius: 10px;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        font-size: 0.88rem;
        transition: 0.2s all;
        display: inline-block;
    }

    .footer-links a:hover {
        color: #ffffff;
        transform: translateX(5px);
    }

    .social-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.3s all;
    }

    .social-icon:hover {
        background-color: var(--telco-maroon) !important;
        color: white !important;
        transform: translateY(-3px);
    }

    .payment-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 8px 12px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-box img {
        max-height: 18px;
        filter: grayscale(100%);
        transition: 0.3s;
        opacity: 0.6;
    }

    .payment-box:hover img {
        filter: grayscale(0%);
        opacity: 1;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .w-15px {
        width: 18px;
        text-align: center;
    }

    /* CSS Global Overrides */
    :root {
        --telkom-red: #9F1521;
    }

    .text-maroon {
        color: var(--telkom-red) !important;
    }

    .bg-maroon {
        background-color: var(--telkom-red) !important;
    }
</style>