<x-guest-layout>
    <div class="mb-5">
        <h2 class="fw-bold text-dark mb-2">{{ $siteSettings?->register_title ?? 'Mulai Karir Anda 🚀' }}</h2>
        <p class="text-secondary">{{ $siteSettings?->register_description ?? 'Buat akun baru untuk melamar pekerjaan atau merekrut talenta.' }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-medium text-secondary small">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user text-secondary"></i></span>
                <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-medium text-secondary small">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-secondary"></i></span>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@company.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-medium text-secondary small">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
                <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                <button type="button" class="input-group-text bg-white border-start-0 text-secondary" onclick="togglePasswordVisibility('password', this)">
                    <i class="fas fa-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-medium text-secondary small">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-secondary"></i></span>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
                <button type="button" class="input-group-text bg-white border-start-0 text-secondary" onclick="togglePasswordVisibility('password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="d-grid gap-3">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                Daftar Sekarang <i class="fas fa-arrow-right ms-2 small"></i>
            </button>

            <div class="position-relative text-center my-2">
                <hr class="text-secondary opacity-25">
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">atau</span>
            </div>

            <div class="text-center">
                <p class="text-secondary small mb-0">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">Masuk</a>
                </p>
            </div>
        </div>
    </form>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</x-guest-layout>
