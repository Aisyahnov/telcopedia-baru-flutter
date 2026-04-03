<!-- FOOTER -->
<footer class="bg-dark text-light pt-4 pb-3 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h6 class="fw-bold mb-3">Layanan Mahasiswa</h6>
        <ul class="list-unstyled small">
          <li><a href="{{ route('about') }}" class="text-light text-decoration-none hover-white">About Us / Tentang Telcopedia</a></li>
          <li><a href="{{ route('category.index') }}" class="text-light text-decoration-none hover-white">Kategori Produk</a></li>
          <li><a href="{{ route('favorites.index') }}" class="text-light text-decoration-none hover-white">Daftar Wishlist</a></li>
          <li><a href="{{ route('vouchers.index') }}" class="text-light text-decoration-none hover-white">Katalog Voucher Promo 🎫</a></li>
          <li><a href="{{ route('terms') }}" class="text-light text-decoration-none hover-white border-top pt-2 mt-2 d-block">Syarat & Ketentuan ⚖️</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold mb-3">Customer Service / Bantuan</h6>
        <p class="small mb-1"><a href="{{ route('contact') }}" class="text-light text-decoration-none hover-white">Help & Support (FAQ)</a> • <a href="{{ route('contact') }}" class="text-light text-decoration-none hover-white">Lapor Kendala</a></p>
        <p class="small mb-2"><a href="{{ route('privacy') }}" class="text-light text-decoration-none hover-white">Privacy Policy 🛡️</a> • <a href="{{ route('contact') }}" class="text-light text-decoration-none hover-white">Hubungi Admin</a></p>
        <p class="small pt-2 mt-2 border-top">WA: +62 812 3456 7890<br>Email: cs@telcopedia.id</p>
      </div>
      <div class="col-md-4 text-end">
        <h6>Follow Us</h6>
        <a class="text-light me-2" href="#"><i class="fab fa-instagram fa-lg"></i></a>
        <a class="text-light me-2" href="#"><i class="fab fa-facebook fa-lg"></i></a>
        <a class="text-light" href="#"><i class="fab fa-twitter fa-lg"></i></a>
      </div>
    </div>

    <div class="text-center mt-3 small">
      &copy; 2025 Telcopedia • Built by Students
    </div>
  </div>
</footer>

<script>
  // Category filter (client-side)
  document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.category-btn').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.cat;
      document.querySelectorAll('.product-item').forEach(card => {
        if (cat === 'All' || card.dataset.category === cat) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // default show All
  document.querySelectorAll('.category-btn').forEach(b=>{
    if (b.dataset.cat === 'All') b.classList.add('active');
  });
</script>

<style>
/* CSS Injection Untuk Memaksa Warna Maroon Telkom di Kelas Bootstrap Normal */
:root {
  --telkom-red: #9F1521;
  --telkom-hover: #7c111b;
}
.text-danger { color: var(--telkom-red) !important; }
.bg-danger { background-color: var(--telkom-red) !important; }
.btn-danger { 
    background-color: var(--telkom-red) !important; 
    border-color: var(--telkom-red) !important; 
}
.btn-danger:hover {
    background-color: var(--telkom-hover) !important;
    border-color: var(--telkom-hover) !important;
}
.btn-outline-danger {
    color: var(--telkom-red) !important;
    border-color: var(--telkom-red) !important;
}
.btn-outline-danger:hover {
    background-color: var(--telkom-red) !important;
    color: #fff !important;
}
.hover-white { transition: 0.2s; }
.hover-white:hover { color: #fff !important; opacity: 1 !important; transform: translateX(5px); }
</style>
