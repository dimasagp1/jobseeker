@extends('layouts.admin')

@section('content')
<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --text-heading: #1e293b;
        --brand-primary: #0d6efd;
    }
    .detail-card {
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }
    .status-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .bg-soft-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .bg-soft-warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .bg-soft-danger { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    
    .info-list-item {
        display: flex;
        align-items: flex-start;
        padding: 12px 0;
        border-bottom: 1px solid var(--slate-100);
    }
    .info-list-item:last-child { border-bottom: none; }
    .info-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--slate-50);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 1.1rem;
    }
    .content-section h5 {
        font-weight: 700;
        color: var(--text-heading);
        margin-top: 24px;
        margin-bottom: 16px;
        font-size: 1.1rem;
        border-bottom: 2px solid var(--slate-100);
        padding-bottom: 8px;
    }
    .content-section p {
        color: #475569;
        line-height: 1.7;
    }
</style>

<div class="container-fluid pb-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.jobs.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
        <div>
            <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                <i class="fas fa-edit mr-1"></i> Edit Lowongan
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI (Konten Utama) --}}
        <div class="col-lg-8">
            <div class="detail-card mb-4">
                <div class="p-4 p-md-5 border-bottom" style="background: linear-gradient(to right, #ffffff, var(--slate-50));">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary text-white mb-2 px-3 py-2" style="border-radius: 8px;">
                                {{ $job->category->name ?? 'Tanpa Kategori' }}
                            </span>
                            @if($job->is_featured)
                                <span class="badge bg-warning text-dark mb-2 px-3 py-2 ml-1" style="border-radius: 8px;">
                                    <i class="fas fa-star text-dark mr-1"></i> Unggulan
                                </span>
                            @endif
                        </div>
                        
                        {{-- Status Pill --}}
                        <span class="status-pill bg-soft-{{ $job->status === 'published' ? 'success' : ($job->status === 'closed' ? 'danger' : 'warning') }}">
                            {{ $job->status === 'published' ? 'TAYANG' : ($job->status === 'closed' ? 'DITUTUP' : 'DRAFT') }}
                        </span>
                    </div>
                    
                    <h3 class="fw-bold text-dark mb-1">{{ $job->title }}</h3>
                    <p class="text-muted mb-0">
                        <i class="fas fa-building mr-1"></i> {{ $job->company->company_name ?? 'HerbaTech' }} &nbsp;|&nbsp;
                        <i class="fas fa-map-marker-alt mr-1 ml-2"></i> {{ $job->location->name ?? 'Tidak ada lokasi' }} 
                        @if($job->is_remote) <span class="text-success fw-bold">(Remote)</span> @endif
                    </p>
                </div>

                <div class="p-4 p-md-5 content-section">
                    <h5>Deskripsi Pekerjaan</h5>
                    <p>{!! nl2br(e($job->description)) !!}</p>

                    @if($job->requirements)
                        <h5>Persyaratan Khusus</h5>
                        <p>{!! nl2br(e($job->requirements)) !!}</p>
                    @endif

                    @if($job->responsibilities)
                        <h5>Tanggung Jawab Utama</h5>
                        <p>{!! nl2br(e($job->responsibilities)) !!}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (Sidebar Informasi) --}}
        <div class="col-lg-4">
            {{-- Statistik Lamaran --}}
            <a href="{{ route('admin.applications.index', ['job_id' => $job->id]) }}" class="detail-card d-block text-decoration-none p-4 mb-4 text-center" style="transition: transform 0.2s;">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-users"></i>
                </div>
                <h2 class="fw-bold text-dark mb-0">{{ $job->applications()->count() }}</h2>
                <p class="text-muted small mb-0 fw-bold text-uppercase mt-1">Total Pelamar</p>
            </a>

            {{-- Ringkasan Lowongan --}}
            <div class="detail-card p-4 mb-4">
                <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom">Ringkasan Pekerjaan</h6>
                
                <div class="info-list-item">
                    <div class="info-icon"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Tipe Pekerjaan</small>
                        <span class="text-dark fw-medium">
                            {{ match($job->job_type) {
                                'full_time' => 'Penuh Waktu (Full Time)',
                                'part_time' => 'Paruh Waktu (Part Time)',
                                'contract' => 'Kontrak',
                                'freelance' => 'Freelance',
                                'internship' => 'Magang',
                                default => ucfirst(str_replace('_', ' ', $job->job_type))
                            } }}
                        </span>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="info-icon"><i class="fas fa-money-bill-wave text-success"></i></div>
                    <div>
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Kompensasi</small>
                        <span class="text-dark fw-medium">
                            @if($job->salary_min || $job->salary_max)
                                Rp {{ number_format($job->salary_min ?? 0, 0, ',', '.') }} - Rp {{ number_format($job->salary_max ?? 0, 0, ',', '.') }}
                            @else
                                <span class="text-muted font-italic">Dirahasiakan</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="info-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Pendidikan</small>
                        <span class="text-dark fw-medium">{{ strtoupper($job->education_level ?? 'Semua Jenjang') }}</span>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="info-icon"><i class="fas fa-user-tie"></i></div>
                    <div>
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Pengalaman</small>
                        <span class="text-dark fw-medium">{{ $job->formatted_experience_level }}</span>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="info-icon"><i class="fas fa-calendar-alt text-danger"></i></div>
                    <div>
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 0.7rem;">Batas Lamaran</small>
                        <span class="text-dark fw-medium">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d F Y') : 'Tidak ada batas' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Bagikan Tautan --}}
            <div class="detail-card p-4 mb-4 text-center bg-light">
                <h6 class="fw-bold text-dark mb-2">Bagikan Lowongan</h6>
                <p class="text-muted small mb-3">Salin tautan atau bagikan ke kandidat:</p>
                <div class="d-flex justify-content-center" style="gap: 10px;">
                    <button class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" onclick="copyLink('{{ route('public.jobs.show', $job->slug ?? $job->id) }}')" title="Salin Tautan">
                        <i class="fas fa-link text-secondary"></i>
                    </button>
                    <a href="https://wa.me/?text={{ urlencode('Lowongan Kerja ' . $job->title . ' di ' . ($job->company->company_name ?? 'HerbaTech') . '. Lamar sekarang di: ' . route('public.jobs.show', $job->slug ?? $job->id)) }}" target="_blank" class="btn btn-success shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" title="Bagikan ke WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            {{-- Aksi Admin (Approve/Reject/Close) --}}
            <div class="detail-card p-4">
                <h6 class="fw-bold text-dark mb-3">Aksi Administrator</h6>
                
                @if($job->status === 'draft')
                    <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 font-weight-bold" style="border-radius: 12px;">
                            <i class="fas fa-check-circle mr-1"></i> Tayangkan Sekarang
                        </button>
                    </form>
                @endif
                
                @if($job->status === 'published')
                     <form action="{{ route('admin.jobs.reject', $job->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 font-weight-bold" style="border-radius: 12px;" onclick="return confirm('Apakah Anda yakin ingin menutup lowongan ini? Pelamar tidak akan bisa melamar lagi.')">
                            <i class="fas fa-times-circle mr-1"></i> Tutup Lowongan
                        </button>
                    </form>
                @endif
                
                @if($job->status === 'closed')
                    <p class="text-danger small fw-bold text-center mb-0 mt-2"><i class="fas fa-lock mr-1"></i> Lowongan ini telah ditutup.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function copyLink(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Tautan lowongan berhasil disalin!');
        }, function(err) {
            console.error('Gagal menyalin teks: ', err);
            alert('Gagal menyalin tautan. Silakan salin manual dari browser.');
        });
    }
</script>
@endsection