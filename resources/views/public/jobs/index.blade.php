@extends('layouts.public')

@section('title', 'Find Jobs - ' . ($siteSettings->company_name ?? 'JobPortal'))

@section('content')
<div class="container py-5" style="margin-top: 60px;">
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold">Temukan Pekerjaan Impian Anda</h1>
            <p class="lead text-muted">Telusuri ribuan lowongan pekerjaan dari perusahaan ternama.</p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <form action="{{ route('public.jobs.index') }}" method="GET" class="card shadow-sm p-3">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="keyword" class="form-control form-control-lg border-0 bg-light" placeholder="Judul pekerjaan, kata kunci, atau perusahaan" value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select form-select-lg border-0 bg-light">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                         <select name="location" class="form-select form-select-lg border-0 bg-light">
                            <option value="">Lokasi</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Job List -->
    <div class="row">
        @forelse($jobs as $job)
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 hover-lift">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($job->company->company_logo)
                            <img src="{{ asset('storage/' . $job->company->company_logo) }}" alt="Logo" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-building text-primary fa-lg"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-primary">{{ $job->title }}</h5>
                            <p class="card-text text-muted small mb-0">{{ $job->company->company_name }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-light text-dark border me-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $job->location->name ?? 'Remote' }}</span>
                        <span class="badge bg-light text-dark border me-1"><i class="fas fa-briefcase me-1 text-success"></i> {{ $job->job_type == 'full_time' ? 'Penuh Waktu' : ($job->job_type == 'part_time' ? 'Paruh Waktu' : ($job->job_type == 'contract' ? 'Kontrak' : ($job->job_type == 'internship' ? 'Magang' : ucfirst($job->job_type)))) }}</span>
                        <span class="badge bg-light text-dark border"><i class="fas fa-money-bill-wave me-1 text-warning"></i> {{ $job->salary_formatted }}</span>
                    </div>

                    <p class="card-text text-muted">{{ Str::limit($job->description, 120) }}</p>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center pb-3">
                    <small class="text-muted">Dipasang {{ $job->created_at->diffForHumans() }}</small>
                    <a href="{{ route('public.jobs.show', $job->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Lihat Detail</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png?f=webp" alt="Tidak ada lowongan ditemukan" style="max-width: 300px; opacity: 0.7;">
            <h3 class="mt-3 text-muted">Tidak ada lowongan ditemukan</h3>
            <p class="text-muted">Coba sesuaikan kriteria pencarian Anda.</p>
            <a href="{{ route('public.jobs.index') }}" class="btn btn-primary mt-2">Hapus Pencarian</a>
        </div>
        @endforelse
    </div>

    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $jobs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-5px); }
</style>
@endpush
