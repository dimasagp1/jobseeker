@extends('layouts.public')

@section('title', $job->title . ' - ' . ($siteSettings->company_name ?? 'JobPortal'))

@section('content')
<div class="container py-5" style="margin-top: 60px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.jobs.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $job->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Job Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        @if($job->company->company_logo)
                            <img src="{{ asset('storage/' . $job->company->company_logo) }}" alt="Logo" class="rounded border p-1 me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="rounded border p-1 me-3 bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-building fa-2x text-secondary"></i>
                            </div>
                        @endif
                        <div>
                            <h2 class="fw-bold mb-1">{{ $job->title }}</h2>
                            <p class="text-muted mb-0"><i class="fas fa-building me-1"></i> {{ $job->company->company_name }} &bull; <i class="fas fa-map-marker-alt mx-1"></i> {{ $job->location->name ?? 'Remote' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="badge bg-primary me-2 px-3 py-2">{{ $job->job_type == 'full_time' ? 'Penuh Waktu' : ($job->job_type == 'part_time' ? 'Paruh Waktu' : ($job->job_type == 'contract' ? 'Kontrak' : ($job->job_type == 'internship' ? 'Magang' : ucfirst($job->job_type)))) }}</span>
                        <span class="badge bg-success me-2 px-3 py-2">{{ $job->salary_formatted }}</span>
                        <span class="badge bg-info text-dark px-3 py-2">{{ $job->vacancy }} Lowongan</span>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h4 class="fw-bold mb-3">Deskripsi Pekerjaan</h4>
                        <div class="text-secondary" style="white-space: pre-line;">
                            {{ $job->description }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="fw-bold mb-3">Persyaratan</h4>
                        <div class="text-secondary" style="white-space: pre-line;">
                            {{ $job->requirements }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Ringkasan Pekerjaan</h5>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-calendar-alt me-2 text-primary"></i> Dipasang: <strong>{{ $job->created_at->diffForHumans() }}</strong></li>
                        <li class="mb-2"><i class="fas fa-hourglass-end me-2 text-primary"></i> Batas Waktu: <strong>{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : 'Tanpa Batas Waktu' }}</strong></li>
                        <li class="mb-2"><i class="fas fa-graduation-cap me-2 text-primary"></i> Pendidikan: <strong>{{ ucfirst($job->education_level ?? 'Semua Level') }}</strong></li>
                        <li class="mb-2"><i class="fas fa-briefcase me-2 text-primary"></i> Pengalaman: <strong>{{ $job->formatted_experience_level }}</strong></li>
                    </ul>

                    <div class="d-grid gap-2">
                        @auth
                            @if(auth()->user()->role == 'seeker')
                                {{-- Check if already applied logic could be added here --}}
                                <a href="#" class="btn btn-primary btn-lg">Lamar Sekarang</a>
                            @else
                                <div class="alert alert-info small">Masuk sebagai Pencari Kerja untuk melamar.</div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Masuk untuk Melamar</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">Daftar</a>
                        @endauth
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="small text-muted mb-2">Bagikan lowongan ini:</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-primary rounded-circle"><i class="fab fa-facebook-f"></i></button>
                            <button class="btn btn-sm btn-outline-info rounded-circle"><i class="fab fa-twitter"></i></button>
                            <button class="btn btn-sm btn-outline-primary rounded-circle"><i class="fab fa-linkedin-in"></i></button>
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="copyToClipboard('{{ route('public.jobs.show', $job->id) }}')" title="Salin Tautan"><i class="fas fa-link"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
