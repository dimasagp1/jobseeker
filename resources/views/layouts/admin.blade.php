<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>@yield('title', 'Admin Dashboard') - {{ $siteSettings->company_name ?? config('app.name', 'Job Portal') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}"> -->
    
    @if(isset($siteSettings) && $siteSettings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $siteSettings->favicon) }}" type="image/x-icon"/>
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
    @endif

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 84px;
            --sidebar-transition: 220ms ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR MODERN */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            min-height: 100vh;
            background-color: #0f172a; /* Deep Slate Blue */
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition);
        }

        .brand-link {
            padding: 1.25rem 1.2rem;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #f8fafc;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1rem;
        }
        .brand-link:hover { color: #ffffff; }

        .brand-link span {
            transition: opacity var(--sidebar-transition), width var(--sidebar-transition), margin var(--sidebar-transition);
        }

        .sidebar-menu {
            padding: 1rem 0.5rem;
            flex-grow: 1;
            overflow-y: auto;
        }
        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 5px; }

        .nav-item { margin-bottom: 4px; }

        .nav-link {
            color: #94a3b8;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: #f8fafc;
            transform: translateX(3px);
        }

        .nav-link.active {
            background-color: #0d6efd;
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.3);
        }

        .nav-link span {
            transition: opacity var(--sidebar-transition), width var(--sidebar-transition), margin var(--sidebar-transition);
        }
        
        .nav-header {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 1.5rem 1rem 0.5rem 1rem;
        }

        /* MAIN CONTENT AREA */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8fafc;
            transition: margin-left var(--sidebar-transition);
        }

        /* TOP NAVBAR MODERN */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1030;
            position: sticky;
            top: 0;
        }

        .sidebar-toggle-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sidebar-toggle-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: opacity var(--sidebar-transition), visibility var(--sidebar-transition);
        }

        .content-area {
            padding: 2rem 1.5rem;
            flex-grow: 1;
        }

        /* USER DROPDOWN PILL */
        .user-dropdown-toggle {
            background: #f8fafc;
            border-radius: 20px;
            padding: 4px 16px 4px 4px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .user-dropdown-toggle:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px;
        }
        .dropdown-item { border-radius: 8px; padding: 8px 16px; font-weight: 500; transition: all 0.2s; }
        .dropdown-item:hover { background-color: #f1f5f9; color: #0d6efd; }

        /* BREADCRUMB CUSTOM */
        .breadcrumb-item a { color: #64748b; text-decoration: none; font-weight: 500; }
        .breadcrumb-item a:hover { color: #0d6efd; }
        .breadcrumb-item.active { color: #0d6efd; font-weight: 600; }

        .footer-wrapper {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            padding: 1rem 1.5rem;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
        }

        body.sidebar-collapsed .sidebar-wrapper {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .brand-link {
            padding-inline: 0.9rem;
            justify-content: center;
        }

        body.sidebar-collapsed .brand-link img,
        body.sidebar-collapsed .brand-link > div {
            margin-right: 0 !important;
        }

        body.sidebar-collapsed .brand-link span,
        body.sidebar-collapsed .nav-header,
        body.sidebar-collapsed .nav-link span {
            opacity: 0;
            width: 0;
            margin: 0;
            overflow: hidden;
            pointer-events: none;
        }

        body.sidebar-collapsed .nav-link {
            justify-content: center;
            padding-inline: 0.6rem;
        }

        body.sidebar-collapsed .nav-link i {
            margin-right: 0;
        }

        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                width: min(82vw, 320px);
                transform: translateX(-100%);
            }

            .main-wrapper,
            body.sidebar-collapsed .main-wrapper {
                margin-left: 0;
            }

            body.sidebar-mobile-open .sidebar-wrapper {
                transform: translateX(0);
            }

            body.sidebar-mobile-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="sidebar-wrapper">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            @if(isset($siteSettings) && $siteSettings->company_logo)
                <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" class="rounded-circle shadow-sm" style="width: 35px; height: 35px; object-fit: contain; margin-right: 12px; background: white; padding: 2px;">
            @else
                <div class="rounded-circle shadow-sm bg-primary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; margin-right: 12px;">
                    <i class="fas fa-briefcase text-white" style="font-size: 0.9rem;"></i>
                </div>
            @endif
            <span>{{ Str::limit($siteSettings->company_name ?? 'HerbaTech Admin', 18) }}</span>
        </a>

        <div class="sidebar-menu">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="fas fa-border-all"></i> <span>Dashboard</span>
                </a>
            </div>
            
            <div class="nav-header">PENGELOLAAN DATA</div>

            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="Manajemen Pengguna">
                    <i class="fas fa-users"></i> <span>Manajemen Pengguna</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" title="Daftar Lowongan">
                    <i class="fas fa-briefcase"></i> <span>Daftar Lowongan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" title="Data Lamaran Masuk">
                    <i class="fas fa-file-signature"></i> <span>Data Lamaran Masuk</span>
                </a>
            </div>

            <div class="nav-header">PENGATURAN PORTAL</div>

            <div class="nav-item">
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Identitas dan Branding">
                    <i class="fas fa-building"></i> <span>Identitas & Branding</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" title="Kategori Pekerjaan">
                    <i class="fas fa-tags"></i> <span>Kategori Pekerjaan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" title="Lokasi Penempatan">
                    <i class="fas fa-map-marker-alt"></i> <span>Lokasi Penempatan</span>
                </a>
            </div>

            <div class="nav-header">ANALITIK & LAPORAN</div>

            <div class="nav-item">
                <a href="{{ route('admin.reports.jobs') }}" class="nav-link {{ request()->routeIs('admin.reports.jobs') ? 'active' : '' }}" title="Laporan Lowongan">
                    <i class="fas fa-chart-pie"></i> <span>Laporan Lowongan</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.reports.applications') }}" class="nav-link {{ request()->routeIs('admin.reports.applications') ? 'active' : '' }}" title="Tren Lamaran">
                    <i class="fas fa-chart-line"></i> <span>Tren Lamaran</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button type="button" id="sidebarToggle" class="sidebar-toggle-btn me-2" aria-label="Toggle sidebar" aria-expanded="true">
                    <i class="fas fa-angles-left"></i>
                </button>
                <h4 class="mb-0 fw-bold" style="color: #1e293b; letter-spacing: -0.5px;">@yield('title')</h4>
            </div>
            
            <div class="dropdown">
                <a href="#" class="user-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; font-weight: bold;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span>{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2" style="min-width: 220px;">
                    <li>
                        <div class="dropdown-header text-dark pb-0">
                            <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
                            <p class="text-muted small mb-2">{{ Auth::user()->email }}</p>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider mb-2"></li>
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item py-2">
                            <i class="fas fa-user-cog me-2 text-muted"></i> Pengaturan Profil
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-bold py-2">
                                <i class="fas fa-sign-out-alt me-2"></i> Keluar Sistem
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="content-area">
            
            <div class="d-flex justify-content-end mb-3">
                <ol class="breadcrumb bg-transparent p-0 m-0 small">
                    @yield('breadcrumb')
                </ol>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="footer-wrapper mt-auto">
            <div>Hak Cipta &copy; {{ date('Y') }} <span class="text-primary fw-bold">{{ $siteSettings->company_name ?? config('app.name', 'JobPortal') }}</span>.</div>
            <div class="fw-medium">Versi 1.0.0</div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Auto-hide alert function
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const sidebarLinks = document.querySelectorAll('.sidebar-menu .nav-link');
            const collapseStorageKey = 'admin.sidebar.collapsed';
            const desktopBreakpoint = 992;

            function isDesktop() {
                return window.innerWidth >= desktopBreakpoint;
            }

            function syncToggleButton() {
                if (!sidebarToggle) {
                    return;
                }

                const icon = sidebarToggle.querySelector('i');
                const isExpanded = isDesktop()
                    ? !body.classList.contains('sidebar-collapsed')
                    : body.classList.contains('sidebar-mobile-open');

                sidebarToggle.setAttribute('aria-expanded', String(isExpanded));

                if (icon) {
                    if (isDesktop()) {
                        icon.className = isExpanded ? 'fas fa-angles-left' : 'fas fa-angles-right';
                    } else {
                        icon.className = isExpanded ? 'fas fa-xmark' : 'fas fa-bars';
                    }
                }
            }

            function applyResponsiveSidebarState() {
                if (isDesktop()) {
                    body.classList.remove('sidebar-mobile-open');
                    body.classList.toggle('sidebar-collapsed', localStorage.getItem(collapseStorageKey) === '1');
                } else {
                    body.classList.remove('sidebar-collapsed');
                    body.classList.remove('sidebar-mobile-open');
                }

                syncToggleButton();
            }

            applyResponsiveSidebarState();

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (isDesktop()) {
                        const nextCollapsed = !body.classList.contains('sidebar-collapsed');
                        body.classList.toggle('sidebar-collapsed', nextCollapsed);
                        localStorage.setItem(collapseStorageKey, nextCollapsed ? '1' : '0');
                    } else {
                        body.classList.toggle('sidebar-mobile-open');
                    }

                    syncToggleButton();
                });
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function() {
                    body.classList.remove('sidebar-mobile-open');
                    syncToggleButton();
                });
            }

            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (!isDesktop()) {
                        body.classList.remove('sidebar-mobile-open');
                        syncToggleButton();
                    }
                });
            });

            window.addEventListener('resize', applyResponsiveSidebarState);

            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert) {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(() => { alert.remove(); }, 600); 
                    }
                }, 5000);
            });
        });

        // Fungsi Copy Clipboard Global
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    alert('Tautan lowongan telah disalin ke papan klip!');
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
                if(successful) {
                    alert('Tautan lowongan telah disalin ke papan klip!');
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