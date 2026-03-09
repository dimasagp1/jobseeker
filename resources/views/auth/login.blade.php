<x-guest-layout>
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-2">{{ $siteSettings?->register_title ?? 'Selamat Datang Kembali! 👋' }}</h2>
        <p class="text-secondary">{{ $siteSettings?->register_description ?? 'Silakan masuk untuk melanjutkan ke dashboard Anda.' }}</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success mb-4 d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label fw-medium text-secondary small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-secondary"></i></span>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@company.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-medium text-secondary small">Password</label>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="Masukan password Anda">
                <button type="button" class="input-group-text bg-white border-start-0 text-secondary" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label text-secondary small">Ingat saya</label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small text-primary fw-semibold" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <div class="d-grid gap-3">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                Masuk Sekarang <i class="fas fa-arrow-right ms-2 small"></i>
            </button>
            
            <div class="position-relative text-center my-2">
                <hr class="text-secondary opacity-25">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">atau</span>
            </div>

            <div class="text-center">
                <p class="text-secondary small mb-0">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-primary">Buat Akun Baru</a>
                </p>
            </div>
        </div>
    </form>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
</x-guest-layout>
