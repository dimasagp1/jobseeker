<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteSettings?->company_name ?? 'HerbaTech' }} - Career Portal</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" />

    @stack('styles')

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            /* Warna latar terang standar */
            background-color: #f4f7fa; 
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        main { flex: 1; }

        .navbar {
            background: white !important;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1050; 
            transition: all 0.3s ease;
        }

        /* ========================================= */
        /* OVERRIDE DARK MODE UNTUK CUSTOM WARNA     */
        /* (Memanfaatkan bawaan Bootstrap 5.3)       */
        /* ========================================= */
        
        [data-bs-theme='dark'] body {
            background-color: #0f172a !important; /* Latar belakang body gelap */
        }

        [data-bs-theme='dark'] .navbar,
        [data-bs-theme='dark'] .bg-white {
            background-color: #1e293b !important; /* Ubah elemen bg-white jadi gelap elegan */
            border-color: #334155 !important;
        }

        [data-bs-theme='dark'] .text-dark {
            color: #f8fafc !important; /* Ubah semua teks hitam murni jadi putih */
        }

        [data-bs-theme='dark'] .navbar-brand, 
        [data-bs-theme='dark'] .nav-link,
        [data-bs-theme='dark'] #themeToggle i {
            color: #f8fafc !important;
        }

        [data-bs-theme='dark'] .navbar-toggler-icon {
            filter: invert(1);
        }
        
        [data-bs-theme='dark'] .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.5) !important; /* Pertegas shadow di dark mode */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('seeker.dashboard') }}">
                @if(isset($siteSettings) && $siteSettings && $siteSettings->company_logo)
                    <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" class="me-2 rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                @else
                    <i class="fas fa-briefcase me-2 text-primary"></i>
                @endif
                {{ $siteSettings?->company_name ?? 'Job Portal Herbatech' }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seeker.jobs.*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('seeker.jobs.index') }}">Cari Lowongan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seeker.applications.*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('seeker.applications.index') }}">Lamaran Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('seeker.saved-jobs.*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('seeker.saved-jobs.index') }}">Tersimpan</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-lg-center">
                    {{-- PERBAIKAN: z-index relative dan padding disesuaikan --}}
                    <li class="nav-item me-2 position-relative" style="z-index: 1060;">
                        <button class="theme-toggle btn btn-link text-decoration-none text-secondary" id="themeToggle" title="Toggle Theme" style="font-size: 1.2rem;">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>

                    <li class="nav-item dropdown position-relative" style="z-index: 1060;">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=eef2ff&color=4338ca' }}" 
                                class="rounded-circle me-2 border shadow-sm bg-white" 
                                width="32" height="32" style="object-fit: cover;">
                            <span class="fw-semibold text-dark">{{ Auth::user()->name }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item py-2" href="{{ route('seeker.dashboard') }}"><i class="fas fa-home fa-fw text-muted me-2"></i> Dashboard</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('seeker.profile.edit') }}"><i class="fas fa-user-edit fa-fw text-muted me-2"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-bold">
                                        <i class="fas fa-sign-out-alt fa-fw me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0 text-center text-md-start">
                    <h5 class="fw-bold mb-1">{{ $siteSettings?->company_name ?? 'Job Portal Herbatech' }}</h5>
                    <p class="small text-white-50 mb-0">{{ Str::limit($siteSettings?->company_description ?? 'Sistem rekrutmen terintegrasi.', 100) }}</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} {{ $siteSettings?->company_name ?? 'Job Portal Herbatech' }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Theme Toggle Logic (Memicu Native Bootstrap 5 Dark Mode)
        const toggleBtn = document.getElementById('themeToggle');
        const icon = toggleBtn.querySelector('i');
        const html = document.documentElement;

        // 1. Cek mode saat halaman dimuat
        if (localStorage.getItem('theme') === 'dark') {
            html.setAttribute('data-bs-theme', 'dark'); // Gunakan atribut BS5
            icon.classList.replace('fa-moon', 'fa-sun');
        } else {
            html.setAttribute('data-bs-theme', 'light');
        }

        // 2. Aksi saat tombol diklik
        toggleBtn.addEventListener('click', () => {
            if (html.getAttribute('data-bs-theme') === 'dark') {
                // Ke Mode Terang
                html.setAttribute('data-bs-theme', 'light');
                localStorage.setItem('theme', 'light');
                icon.classList.replace('fa-sun', 'fa-moon');
            } else {
                // Ke Mode Gelap
                html.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                icon.classList.replace('fa-moon', 'fa-sun');
            }
        });

        // Copy Clipboard Helper
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    alert('Tautan lowongan telah disalin ke clipboard!');
                }, function(err) {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    alert('Tautan lowongan telah disalin ke clipboard!');
                } else {
                    alert('Gagal menyalin tautan.');
                }
            } catch (err) {
                alert('Gagal menyalin tautan.');
            }
            document.body.removeChild(textArea);
        }
    </script>
    
    @stack('scripts')
</body>

</html>