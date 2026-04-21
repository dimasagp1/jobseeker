<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $company->company_name ?? 'JobPortal' }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <!-- Styles / Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <link href="{{ asset('css/futuristic.css') }}" rel="stylesheet">
        @if(isset($company->favicon) && $company->favicon)
            <link rel="icon" href="{{ asset('storage/' . $company->favicon) }}" type="image/x-icon"/>
        @endif
    </head>
    <body>
        <!-- Navigation -->
        @include('partials.navbar')

        <!-- Hero Section -->
        <div class="container col-xxl-8 px-4 py-5" style="margin-top: 80px;">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle" style="width: 300px; height: 300px; background: var(--primary-color); opacity: 0.2; filter: blur(80px); z-index: -1; border-radius: 50%;"></div>
                        @if(isset($company->hero_image) && $company->hero_image)
                            <img src="{{ asset('storage/' . $company->hero_image) }}" class="d-block mx-lg-auto img-fluid rounded-4 shadow-lg border border-secondary" alt="Hero" width="700" height="500" loading="lazy">
                        @else
                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=800" class="d-block mx-lg-auto img-fluid rounded-4 shadow-lg border border-secondary" alt="Team" width="700" height="500" loading="lazy">
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold lh-1 mb-3">
                        @if(isset($company->hero_title) && $company->hero_title)
                            {!! preg_replace('/\*\*(.*?)\*\*/', '<span class="text-gradient">$1</span>', e($company->hero_title)) !!}
                        @else
                            Bangun <span class="text-gradient">Masa Depan</span> Bersama Kami
                        @endif
                    </h1>
                    <p class="lead text-muted mb-4">
                        {{ $company->hero_description ?? "Bergabunglah dengan tim visioner dan kreator. Kami mencari individu yang bersemangat untuk mendefinisikan kembali apa yang mungkin terjadi di " . ($company->company_name ?? 'JobPortal') . "." }}
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        @auth
                             <a href="{{ route('seeker.jobs.index') }}" class="btn btn-primary btn-lg px-5 py-3 me-md-2">Lihat Lowongan</a>
                        @else
                             <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3 me-md-2">
                                 {{ $company->hero_cta_text ?? 'Mulai Perjalanan Anda' }}
                             </a>
                             <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3">Masuk</a>
                        @endauth
                    </div>
                </div>
            </div>
            
            @if(isset($jobs) && $jobs->count() > 0)
            <div class="row pt-5" id="jobs">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3"><span class="text-gradient">Lowongan</span> Terbaru</h2>
                    <p class="text-muted lead">Temukan langkah karir Anda berikutnya dengan lowongan pilihan kami.</p>
                </div>
                @foreach($jobs as $job)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 job-card position-relative overflow-hidden">
                        @if($job->is_featured)
                            <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 fw-bold small rounded-bottom-start shadow-sm">
                                <i class="fas fa-star me-1"></i> Unggulan
                            </div>
                        @endif
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center mb-4">
                                @if($job->company->company_logo)
                                    <img src="{{ asset('storage/' . $job->company->company_logo) }}" alt="Logo" class="rounded-3 shadow-sm me-3 bg-white p-1" style="width: 55px; height: 55px; object-fit: contain;">
                                @else
                                    <div class="rounded-3 shadow-sm me-3 bg-light d-flex align-items-center justify-content-center text-primary" style="width: 55px; height: 55px;">
                                        <i class="fas fa-building fa-lg"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="card-title fw-bold mb-1 text-dark">{{ $job->title }}</h5>
                                    <p class="card-text text-muted small mb-0 fw-medium">{{ $job->company->company_name }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-normal">
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $job->location->name ?? 'Remote' }}
                                    </span>
                                    <span class="badge bg-light text-success border border-success-subtle rounded-pill px-3 py-2 fw-normal">
                                        <i class="fas fa-briefcase me-1"></i>
                                        {{
                                            $job->job_type == 'full_time' ? 'Penuh Waktu' :
                                            ($job->job_type == 'part_time' ? 'Paruh Waktu' :
                                            ($job->job_type == 'contract' ? 'Kontrak' :
                                            ($job->job_type == 'internship' ? 'Magang' :
                                            ($job->job_type == 'harian_lepas' ? 'Harian Lepas' : ucwords(str_replace('_', ' ', $job->job_type))))))
                                        }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-1">Gaji:</h6>
                                <p class="text-primary fw-bold mb-0">
                                    {{ $job->salary_formatted }}
                                </p>
                            </div>

                            <p class="card-text text-muted small flex-grow-1 border-top pt-3 mt-auto">
                                {{ Str::limit($job->description, 90) }}
                            </p>
                            
                            <div class="d-grid mt-3">
                                <a href="{{ route('public.jobs.show', $job->id) }}" class="btn btn-outline-primary fw-bold py-2 rounded-3 hover-filled">
                                    Lihat Detail <i class="fas fa-arrow-right ms-2 small"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 d-flex justify-content-between align-items-center small text-muted">
                            <span><i class="far fa-clock me-1"></i> {{ $job->created_at->diffForHumans() }}</span>
                            <span>{{ $job->applications_count ?? 0 }} Pelamar</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="row">
                <div class="col-12 d-flex justify-content-center mt-4">
                    {{ $jobs->fragment('jobs')->links() }}
                </div>
            </div>
            @endif

            <style>
                .job-card {
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    background: #fff;
                }
                .job-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
                }
                .hover-filled:hover {
                    background-color: var(--bs-primary);
                    color: white;
                }
                .text-gradient {
                    background: linear-gradient(45deg, #0d6efd, #0dcaf0);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
            </style>
        </div>

        <!-- Footer -->
        @include('partials.footer')
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

