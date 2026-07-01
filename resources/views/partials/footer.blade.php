<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3">
                    @if(isset($siteSettings->company_logo) && $siteSettings->company_logo)
                        <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" class="me-2 rounded-circle" style="width: 30px; height: 30px;">
                    @else
                        <i class="fas fa-briefcase me-2 text-primary"></i>
                    @endif
                    {{ $siteSettings->company_name ?? 'JobPortal' }}
                </h5>
                <p class="text-muted small">{{ Str::limit($siteSettings->company_description ?? 'Membangun masa depan, satu pekerjaan sekaligus.', 120) }}</p>
                
                @if(isset($siteSettings->company_profile_url) && $siteSettings->company_profile_url)
                    <a href="{{ $siteSettings->company_profile_url }}" target="_blank" class="btn btn-outline-light btn-sm mt-2">
                        <i class="fas fa-building me-2"></i> Profil Perusahaan
                    </a>
                @endif

                <div class="d-flex gap-3 mt-3">
                    @if(isset($siteSettings->facebook) && $siteSettings->facebook)
                        <a href="{{ $siteSettings->facebook }}" target="_blank" class="text-white-50 hover-text-white"><i class="fab fa-facebook fa-lg"></i></a>
                    @endif
                    @if(isset($siteSettings->twitter) && $siteSettings->twitter)
                        <a href="{{ $siteSettings->twitter }}" target="_blank" class="text-white-50 hover-text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    @endif
                    @if(isset($siteSettings->linkedin) && $siteSettings->linkedin)
                        <a href="{{ $siteSettings->linkedin }}" target="_blank" class="text-white-50 hover-text-white"><i class="fab fa-linkedin fa-lg"></i></a>
                    @endif
                    @if(isset($siteSettings->instagram) && $siteSettings->instagram)
                        <a href="{{ $siteSettings->instagram }}" target="_blank" class="text-white-50 hover-text-white"><i class="fab fa-instagram fa-lg"></i></a>
                    @endif
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold text-primary">Tautan Cepat</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="{{ route('public.jobs.index') }}" class="text-decoration-none text-muted hover-text-white">Cari Lowongan</a></li>
                    @auth
                        <li class="mb-2"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted hover-text-white">Dashboard</a></li>
                    @else
                        <li class="mb-2"><a href="{{ route('login') }}" class="text-decoration-none text-muted hover-text-white">Masuk</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}" class="text-decoration-none text-muted hover-text-white">Daftar</a></li>
                    @endauth
                </ul>
            </div>
            
            <!-- For Employers -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold text-primary">Untuk Perusahaan</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-decoration-none text-muted hover-text-white">Pasang Lowongan</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-decoration-none text-muted hover-text-white">Login Perusahaan</a></li>
                </ul>
            </div>
            
            <!-- Contact/Newsletter -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-uppercase mb-3 fw-bold text-primary">Tetap Terhubung</h6>
                <p class="small text-muted">Berlangganan untuk mendapatkan pembaruan lowongan kerja terbaru dan tips karir.</p>
                <form class="input-group mb-3">
                    <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="Masukkan email Anda">
                    <button class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
        
        <hr class="my-4 border-secondary">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start small text-muted">
                &copy; {{ date('Y') }} {{ $siteSettings->company_name ?? 'JobPortal' }}. Hak Cipta Dilindungi.
            </div>
            <div class="col-md-6 text-center text-md-end small">
                <a href="#" class="text-muted text-decoration-none hover-text-white me-3">Kebijakan Privasi</a>
                <a href="#" class="text-muted text-decoration-none hover-text-white">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-text-white:hover {
        color: #fff !important;
        transition: color 0.2s;
    }
</style>
