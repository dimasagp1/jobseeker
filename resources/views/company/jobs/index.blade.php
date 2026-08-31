@extends('layouts.company')

@section('title', 'Manajemen Lowongan')

@section('content')
<style>
    /* Desain Card & Tabel yang lebih Premium */
    .job-card { 
        border-radius: 16px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        overflow: hidden;
    }

    .table thead th { 
        background-color: #f8fafc; 
        text-transform: uppercase; 
        font-size: 0.7rem; 
        font-weight: 700;
        letter-spacing: 0.05em; 
        color: #64748b; 
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Row Interaction - Full Clickable */
    .job-row { 
        position: relative; 
        transition: background-color 0.2s ease;
    }
    .job-row:hover { 
        background-color: #f1f5f9; 
    }

    /* Judul & Detail */
    .job-title { 
        color: #1e293b; 
        font-weight: 700; 
        font-size: 1rem; 
        margin-bottom: 2px;
        display: block;
    }
    
    /* Soft Badges */
    .status-badge { 
        font-weight: 600; 
        padding: 6px 12px; 
        border-radius: 8px; 
        font-size: 0.75rem; 
        display: inline-flex; 
        align-items: center; 
    }
    .badge-aktif { background: #dcfce7; color: #166534; }
    .badge-selesai { background: #f1f5f9; color: #475569; }
    .badge-draft { background: #fef3c7; color: #92400e; }

    /* Tombol Aksi - Elevasi saat hover */
    .btn-action { 
        position: relative; /* Supaya tetap di atas stretched-link */
        z-index: 2; 
        border-radius: 10px; 
        width: 36px; 
        height: 36px; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        transition: all 0.2s;
    }
    .btn-action:hover { 
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        color: #4f46e5;
        border-color: #4f46e5;
    }
    .btn-delete:hover { color: #ef4444; border-color: #ef4444; background: #fef2f2; }

    /* Applicant Link - Z-index agar bisa diklik di atas baris */
    .applicant-link { 
        position: relative;
        z-index: 2;
        background: #eff6ff; 
        color: #1d4ed8; 
        font-weight: 700; 
        font-size: 0.8rem; 
        padding: 6px 14px; 
        border-radius: 8px; 
        text-decoration: none; 
    }
    .applicant-link:hover { background: #dbeafe; }
</style>

@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #10b981; background: white; border-radius: 12px;">
        <div class="d-flex align-items-center py-1">
            <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
            <div class="text-dark fw-medium">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #0f172a; letter-spacing: -0.02em;">Manajemen Lowongan</h2>
        <p class="text-muted mb-0 small">Overview posisi pekerjaan dan status rekrutmen HerbaTech.</p>
    </div>
    <a href="{{ route('company.jobs.create') }}" class="btn btn-primary px-4 py-2 shadow-sm fw-bold" style="border-radius: 12px; background: #4f46e5; border: none; transition: 0.3s;">
        <i class="fas fa-plus me-2"></i> Tambah Posisi
    </a>
</div>

<div class="card job-card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Informasi Pekerjaan</th>
                        <th>Kategori</th>
                        <th class="text-center">Kandidat</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr class="job-row">
                        <td class="ps-4 py-4">
                            <div class="d-flex flex-column">
                                <a href="{{ route('company.jobs.show', $job->id) }}" class="job-title stretched-link text-decoration-none">
                                    {{ $job->title }}
                                </a>
                                <div class="d-flex align-items-center text-muted small mt-1">
                                    <span class="me-3"><i class="far fa-building me-1"></i> {{ $job->department ?? 'General' }}</span>
                                    <span><i class="far fa-clock me-1"></i> {{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="py-4">
                            <span class="text-slate-700 fw-medium" style="font-size: 0.9rem;">{{ $job->category->name }}</span>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ ucfirst(str_replace('_', ' ', $job->job_type)) }}</div>
                        </td>

                        <td class="py-4 text-center">
                            <a href="{{ route('company.applications.index', ['job_id' => $job->id]) }}" class="applicant-link">
                                {{ $job->applications_count ?? 0 }} Pelamar
                            </a>
                        </td>

                        <td class="py-4">
                            @if($job->status === 'published')
                                <span class="status-badge badge-aktif">Aktif</span>
                            @elseif($job->status === 'closed')
                                <span class="status-badge badge-selesai">Selesai</span>
                            @else
                                <span class="status-badge badge-draft">Draft</span>
                            @endif
                        </td>

                        <td class="py-4 text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('company.jobs.edit', $job->id) }}" class="btn-action" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>

                                @if($job->status === 'published')
                                <form action="{{ route('company.jobs.close', $job->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-action" title="Tutup" onclick="event.stopPropagation(); return confirm('Tutup lowongan ini?')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('company.jobs.publish', $job->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-action text-success" title="Tayangkan">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('company.jobs.destroy', $job->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus" onclick="event.stopPropagation(); return confirm('Hapus permanen?')">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted">Belum ada lowongan pekerjaan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-4 px-4 d-flex justify-content-center">
        {{ $jobs->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
    // Auto close alert
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
</script>
@endsection