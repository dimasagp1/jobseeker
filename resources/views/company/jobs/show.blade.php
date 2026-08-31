@extends('layouts.company')

@section('title', 'Detail Lowongan')

@section('content')
<style>
    /* Menggunakan font-family yang konsisten di seluruh elemen */
    body, .full-container, button, input, table {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    }

    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        /* Warna teks diperbaiki ke Deep Slate (lebih nyaman dibanding hitam pekat) */
        --text-main: #334155; 
        --text-muted: #64748b;
        --text-heading: #1e293b;
        /* Indigo yang lebih deep dan kalem (Royal Blueish) */
        --brand-color: #3730a3; 
    }

    .full-container { width: 100%; max-width: 100%; padding: 0 15px; }
    
    .detail-card { 
        border-radius: 16px; 
        border: 1px solid var(--slate-200); 
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .job-header-full { 
        padding: 40px;
        border-bottom: 1px solid var(--slate-100);
        background: linear-gradient(to right, #ffffff, #fafafa);
    }

    .status-badge-mini {
        display: inline-flex; align-items: center; font-size: 0.7rem; 
        font-weight: 700; color: var(--text-heading);
        letter-spacing: 0.05em; background: var(--slate-100);
        padding: 5px 12px; border-radius: 6px; text-transform: uppercase;
    }
    .dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 10px; }

    .job-title-display { 
        color: var(--text-heading); 
        font-size: 2rem; 
        font-weight: 800; 
        letter-spacing: -0.02em;
        margin-top: 12px;
    }
    
    .section-label-display {
        font-size: 0.85rem; 
        font-weight: 700; 
        color: var(--brand-color);
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .section-label-display::after {
        content: ""; flex: 1; height: 1px; background: var(--slate-200); margin-left: 20px;
    }

    .rich-content-display { 
        color: var(--text-main); 
        line-height: 1.8; 
        font-size: 1rem; 
    }

    .stat-row { display: flex; gap: 20px; margin-bottom: 35px; }
    
    .stat-card-mini { 
        flex: 1; padding: 20px; 
        background: white; border: 1px solid var(--slate-200);
        border-radius: 14px; transition: all 0.2s;
    }
    .stat-card-mini:hover { border-color: var(--brand-color); }

    .stat-label-display { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; }
    .stat-value-display { font-size: 1.4rem; font-weight: 700; color: var(--text-heading); }

    .tech-summary { background: #fcfcfd; border: 1px solid var(--slate-200); border-radius: 14px; padding: 25px; }
</style>

<div class="full-container">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('company.jobs.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-chevron-left me-2"></i> KEMBALI KE MANAJEMEN
        </a>
        <a href="{{ route('company.jobs.edit', $job->id) }}" class="btn btn-dark px-4 shadow-sm" style="border-radius: 10px; font-weight: 600;">
            <i class="fas fa-edit me-2"></i> Edit Lowongan
        </a>
    </div>

    <div class="card detail-card border-0">
        <div class="job-header-full">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="status-badge-mini">
                        <span class="dot {{ $job->status === 'published' ? 'bg-success' : 'bg-warning' }}"></span>
                        {{ $job->status }}
                    </div>
                    <h1 class="job-title-display mb-3">{{ $job->title }}</h1>
                    <div class="d-flex align-items-center text-muted fw-medium" style="font-size: 0.95rem;">
                        <span class="me-4"><i class="far fa-building me-2" style="color: var(--brand-color)"></i> {{ $job->department ?? 'General' }}</span>
                        <span class="me-4"><i class="fas fa-map-marker-alt me-2 text-danger opacity-75"></i> {{ $job->location->name }}</span>
                        <span><i class="far fa-calendar-alt me-2"></i> Dibuat {{ $job->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <div class="p-4 rounded-4" style="background: #f5f7ff; border: 1px solid #e5e9f5; display: inline-block; min-width: 280px;">
                        <div class="stat-label-display text-center mb-1">Estimasi Gaji</div>
                        <div class="fw-bold text-center" style="color: var(--brand-color); font-size: 1.15rem; letter-spacing: -0.01em;">{{ $job->salary_formatted }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="stat-row">
                <div class="stat-card-mini text-center">
                    <div class="stat-label-display">Kandidat Pelamar</div>
                    <a href="{{ route('company.applications.index', ['job_id' => $job->id]) }}" class="stat-value-display text-decoration-none" style="color: var(--dark-color);">
                        {{ $job->applications_count ?? 0 }} </a>
                </div>
                <div class="stat-card-mini text-center">
                    <div class="stat-label-display">Total Dilihat</div>
                    <div class="stat-value-display">{{ number_format($job->views) }}</div>
                </div>
                <div class="stat-card-mini text-center">
                    <div class="stat-label-display">Batas Akhir</div>
                    <div class="stat-value-display {{ $job->isExpired() ? 'text-danger' : '' }}">
                        {{ $job->deadline ? $job->deadline->format('d M Y') : 'Ongoing' }}
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="section-label-display">Deskripsi Pekerjaan</div>
                    <div class="rich-content-display mb-5">
                        {!! nl2br(e($job->description)) !!}
                    </div>

                    <div class="section-label-display">Syarat & Kualifikasi</div>
                    <div class="rich-content-display">
                        {!! nl2br(e($job->requirements)) !!}
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="tech-summary">
                        <div class="section-label-display">Ringkasan Teknis</div>
                        <table class="table table-borderless table-sm mb-0">
                            <tr class="border-bottom">
                                <td class="py-3 text-muted fw-medium" style="font-size: 0.9rem;">Tipe Kontrak</td>
                                <td class="py-3 fw-bold text-end text-dark">{{ ucfirst(str_replace('_', ' ', $job->job_type)) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="py-3 text-muted fw-medium" style="font-size: 0.9rem;">Sistem Kerja</td>
                                <td class="py-3 fw-bold text-end text-dark">{{ $job->work_setting_label }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="py-3 text-muted fw-medium" style="font-size: 0.9rem;">Level Pengalaman</td>
                                <td class="py-3 fw-bold text-end text-dark">{{ $job->formatted_experience_level }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-muted fw-medium" style="font-size: 0.9rem;">Kebutuhan Personel</td>
                                <td class="py-3 fw-bold text-end text-dark">{{ $job->vacancy }} Orang</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light px-5 py-4 border-top d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-medium">STATUS PENAYANGAN: </span>
                <span class="badge bg-white border text-dark px-3 py-2 fw-bold" style="font-size: 0.75rem;">{{ strtoupper($job->status) }}</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if($job->status === 'published')
                    <form action="{{ route('company.jobs.close', $job->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger px-4 fw-bold" style="border-radius: 8px; font-size: 0.85rem;" onclick="return confirm('Tutup lowongan ini?')">
                            <i class="fas fa-times-circle me-2"></i> TUTUP LOWONGAN
                        </button>
                    </form>
                @else
                    <form action="{{ route('company.jobs.publish', $job->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 fw-bold" style="border-radius: 8px; font-size: 0.85rem;">
                            <i class="fas fa-paper-plane me-2"></i> TAYANGKAN
                        </button>
                    </form>
                @endif

                <div class="vr mx-2"></div>

                <form action="{{ route('company.jobs.destroy', $job->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger text-decoration-none fw-bold small" onclick="return confirm('Hapus permanen?')">
                        HAPUS DATA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection