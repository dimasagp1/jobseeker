<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav" style="z-index: 1050;">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
            @if(isset($siteSettings->company_logo) && $siteSettings->company_logo)
                <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" class="me-2 rounded-circle" style="width: 35px; height: 35px;">
            @else
                <i class="fas fa-briefcase me-2"></i>
            @endif
            {{ $siteSettings?->company_name ?? 'JobPortal' }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto gap-3">
                <li class="nav-item"><a class="nav-link" href="{{ route('public.jobs.index') }}">Cari Lowongan</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                @else
                    <li class="nav-item"><a class="btn btn-outline-light px-4" href="{{ route('login') }}">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-primary px-4" href="{{ route('register') }}">Daftar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<script>
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.getElementById('mainNav').classList.add('bg-dark', 'shadow');
        } else {
            document.getElementById('mainNav').classList.remove('bg-dark', 'shadow');
        }
    });
</script>
