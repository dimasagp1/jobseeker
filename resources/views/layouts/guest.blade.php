<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteSettings?->company_name ?? config('app.name', 'JobPortal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="{{ asset('css/futuristic.css') }}" rel="stylesheet">
    
    @if(isset($siteSettings->favicon) && $siteSettings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $siteSettings->favicon) }}" type="image/x-icon"/>
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            overflow-x: hidden;
        }
        .auth-split-screen .auth-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            position: relative;
            overflow: hidden;
        }
        .auth-split-screen .auth-banner::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
        }
        .auth-split-screen .auth-banner::after {
            content: '';
            position: absolute;
            bottom: -50px; right: -50px;
            width: 300px; height: 300px;
            background: rgba(99, 102, 241, 0.1);
            filter: blur(80px);
            border-radius: 50%;
        }
        @if(isset($siteSettings->guest_banner_image) && $siteSettings->guest_banner_image)
        .auth-split-screen .auth-banner {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.9) 100%), url('{{ asset('storage/' . $siteSettings->guest_banner_image) }}') center/cover no-repeat !important;
        }
        @endif
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        .input-group-text {
            border-color: #e2e8f0;
            background-color: #f8fafc;
        }
        .btn-primary {
            padding: 0.8rem 1.5rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.025em;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body class="bg-light">
    <div class="row g-0 min-vh-100 auth-split-screen">
        <!-- Left Side - Banner -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center text-white auth-banner p-5">
            <div class="position-relative z-1 text-center" style="max-width: 500px;">
                @if(isset($siteSettings->company_logo) && $siteSettings->company_logo)
                    <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" class="mb-4 rounded-3 shadow-lg" style="height: 80px; width: auto;">
                @else
                    <div class="mb-4 d-inline-block p-3 rounded-circle bg-white bg-opacity-10 backdrop-blur">
                        <i class="fas fa-cube fa-3x text-white"></i>
                    </div>
                @endif
                
                <h1 class="display-4 fw-bold mb-3">{{ $siteSettings?->guest_banner_title ?? ($siteSettings?->company_name ?? 'JobPortal') }}</h1>
                <p class="lead text-white-50 mb-4">{{ $siteSettings?->guest_banner_description ?? 'Platform pencarian kerja masa depan. Temukan karir impian Anda atau rekrut talenta terbaik bersama kami.' }}</p>
                

            </div>
            
            <div class="position-absolute bottom-0 start-0 p-4 w-100 text-center">
                <small class="text-white-50">&copy; {{ date('Y') }} {{ $siteSettings?->company_name ?? 'JobPortal' }}. All rights reserved.</small>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center bg-white p-4 p-md-5">
            <div class="w-100" style="max-width: 480px;">
                <!-- Mobile Logo -->
                <div class="d-lg-none text-center mb-4">
                    <a href="/" class="d-inline-block text-decoration-none">
                         @if(isset($siteSettings->company_logo) && $siteSettings->company_logo)
                            <img src="{{ asset('storage/' . $siteSettings->company_logo) }}" alt="Logo" style="height: 50px;">
                        @else
                            <i class="fas fa-briefcase fa-2x text-primary"></i>
                        @endif
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
