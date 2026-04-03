<footer class="bg-dark text-light pt-5 pb-4 mt-auto">
  <div class="container">
    <div class="row gy-4">
      <div class="col-md-4">
        <h5 class="text-danger fw-bold mb-3">Telcopedia</h5>
        <h6 class="text-light">Platform jual-beli Telkom University</h6>
        <p class="small text-muted mt-2">Menyediakan barang bekas berkualitas untuk mahasiswa Telkom University dengan harga terjangkau.</p>
      </div>
      <div class="col-md-4">
        <h6 class="mb-3 fw-bold">Informasi Pelanggan</h6>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="{{ route('about') }}" class="text-muted text-decoration-none hover-white">Tentang Telcopedia 🔴</a></li>
          <li class="mb-2"><a href="{{ route('contact') }}" class="text-muted text-decoration-none hover-white">Pusat Bantuan (FAQ) 🎧</a></li>
          <li class="mb-2"><a href="{{ route('privacy') }}" class="text-muted text-decoration-none hover-white">Kebijakan Privasi 🛡️</a></li>
          <li class="mb-2"><a href="{{ route('terms') }}" class="text-muted text-decoration-none hover-white">Syarat & Ketentuan ⚖️</a></li>
        </ul>
      </div>
      <div class="col-md-4 text-md-end">
        <h6 class="mb-3 fw-bold">Layanan Helpdesk 📩</h6>
        <p class="small text-muted mb-1">WhatsApp Resmi Bantuan Mahasiswa:</p>
        <p class="small text-white fw-bold mb-3">+62 812-3456-7890</p>
        <a href="{{ route('contact') }}" class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-bold" style="font-size: 0.7rem;">Kirim Aduan Masalah</a>
      </div>
        
        <div class="mt-3">
          <a class="text-light me-3 text-decoration-none" href="#"><i class="fab fa-instagram fa-lg"></i></a>
          <a class="text-light me-3 text-decoration-none" href="#"><i class="fab fa-twitter fa-lg"></i></a>
        </div>
      </div>
    </div>
    
    <hr class="border-secondary mt-4 mb-3">
    
    <div class="text-center small text-muted">
      &copy; {{ date('Y') }} Telcopedia • Built by Students
    </div>
  </div>
</footer>
