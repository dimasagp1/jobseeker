@extends('layouts.admin')


@section('content')
<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --text-muted: #64748b;
        --text-heading: #1e293b;
    }
    .dashboard-card {
        border-radius: 12px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        background: #fff;
    }
    .table thead th {
        background: var(--slate-50);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        padding: 12px 20px;
        border-top: none;
        border-bottom: 1px solid var(--slate-200);
    }
    .table td { 
        padding: 16px 20px; 
        vertical-align: middle;
        border-bottom: 1px solid var(--slate-50); 
    }
    .status-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 20px;
    }
    .bg-soft-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .bg-soft-warning { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .bg-soft-danger { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .bg-soft-info { background: #f0f9ff; color: #0ea5e9; border: 1px solid #bae6fd; }
    .btn-action { 
        border-radius: 8px; width: 32px; height: 32px; padding: 0; 
        display: inline-flex; align-items: center; justify-content: center; 
    }
</style>

<div class="container-fluid pb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color: var(--text-heading);"><i class="fas fa-file-import text-danger mr-2"></i> Lamaran Masuk</h3>
            <p class="text-muted small mb-0 mt-1">Pantau dan kelola semua lamaran yang masuk dari kandidat.</p>
        </div>
        
        <a href="{{ route('admin.applications.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 20px; font-weight: 600;">
            <i class="fas fa-plus mr-1"></i> Input Manual
        </a>
    </div>

    <div class="dashboard-card p-3 mb-4">
        <form action="{{ route('admin.applications.index') }}" method="GET" class="row g-3 align-items-center m-0">
            <div class="col-md-5">
                <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-briefcase mr-1 text-primary"></i> Pilih Nama Lowongan Pekerjaan</label>
                <select name="job_id" class="form-control shadow-none font-weight-bold" onchange="this.form.submit()" style="border-radius: 8px;">
                    <option value="">-- Semua Lowongan Pekerjaan --</option>
                    @foreach($jobs as $job)
                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                            {{ $job->title }} ({{ $job->company->company_name ?? 'Perusahaan' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-search mr-1 text-primary"></i> Cari Kandidat / Posisi</label>
                <input type="text" name="search" class="form-control shadow-none" placeholder="Cari nama pelamar..." value="{{ request('search') }}" style="border-radius: 8px;">
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end">
                <button type="submit" class="btn btn-primary px-3 font-weight-bold flex-grow-1" style="border-radius: 8px;"><i class="fas fa-filter mr-1"></i> Filter</button>
                @if(request()->hasAny(['job_id', 'search']))
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-light border" style="border-radius: 8px;" title="Reset Filter"><i class="fas fa-redo"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid var(--slate-100);">
            <h6 class="mb-0 font-weight-bold text-dark">
                @if(request('job_id'))
                    Daftar Pelamar untuk Lowongan Ini
                @else
                    Semua Data Lamaran Masuk
                @endif
            </h6>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Kandidat</th>
                        <th>Melamar Posisi</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Progres Tes</th>
                        <th>Waktu Melamar</th>
                        <th class="text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                    <tr>
                        <td class="pl-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $application->user->avatar ? asset('storage/' . $application->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($application->user->name).'&background=f1f5f9&color=334155' }}" 
                                     alt="Avatar" class="rounded-circle mr-3 border shadow-sm" width="42" height="42" style="object-fit: cover;">
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                        {{ $application->user->name ?? 'Pelamar Dihapus' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $application->user->email ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.jobs.show', $application->job_id) }}" class="text-decoration-none font-weight-bold" style="color: var(--brand-primary);">
                                {{ Str::limit($application->job->title ?? 'Pekerjaan Dihapus', 30) }}
                            </a>
                            <div class="text-muted small">
                                {{ $application->job->company->company_name ?? 'HerbaTech' }}
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $statusClass = match($application->status) {
                                    'pending' => 'warning',
                                    'reviewed' => 'secondary',
                                    'shortlisted' => 'info',
                                    'test_invited' => 'primary',
                                    'test_in_progress' => 'warning',
                                    'test_completed' => 'success',
                                    'interview' => 'dark',
                                    'accepted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                                $statusText = match($application->status) {
                                    'pending' => 'Menunggu',
                                    'reviewed' => 'Sedang Ditinjau',
                                    'shortlisted' => 'Lolos Berkas',
                                    'test_invited' => 'Undangan Tes',
                                    'test_in_progress' => 'Mengerjakan Tes',
                                    'test_completed' => 'Tes Selesai',
                                    'interview' => 'Wawancara',
                                    'accepted' => 'Diterima',
                                    'rejected' => 'Ditolak',
                                    default => ucfirst($application->status)
                                };
                            @endphp
                            <span class="status-pill bg-soft-{{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($application->kraepelin_id)
                                <span class="badge bg-success text-white" style="border-radius: 8px;"><i class="fas fa-check-circle mr-1"></i> Selesai</span>
                            @else
                                <span class="badge bg-light text-muted border" style="border-radius: 8px;">Belum Mengerjakan</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $application->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $application->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-right pr-4">
                            <form action="{{ route('admin.applications.destroy', $application->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen data lamaran ini?')">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('admin.applications.show', $application->id) }}" class="btn btn-outline-primary btn-action" title="Review Lamaran">
                                    <i class="fas fa-search-plus"></i>
                                </a>
                                <a href="{{ route('admin.applications.edit', $application->id) }}" class="btn btn-outline-warning btn-action mx-1" title="Ubah Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                <h5>Belum ada lamaran masuk</h5>
                                <p>Saat ini belum ada kandidat yang melamar pekerjaan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($applications->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-3" style="border-radius: 0 0 12px 12px;">
            <div class="d-flex justify-content-center">
                {{ $applications->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection