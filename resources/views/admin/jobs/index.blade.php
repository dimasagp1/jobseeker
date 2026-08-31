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
    .btn-action { border-radius: 8px; width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>

<div class="container-fluid pb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color: var(--text-heading);"><i class="fas fa-briefcase text-primary mr-2"></i> Daftar Lowongan</h3>
            <p class="text-muted small mb-0 mt-1">Kelola pembukaan dan penutupan lowongan pekerjaan di HerbaTech.</p>
        </div>
        
        <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 20px; font-weight: 600;">
            <i class="fas fa-plus mr-1"></i> Tambah Lowongan
        </a>
    </div>

    <div class="dashboard-card">
        <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid var(--slate-100);">
            <h6 class="mb-0 font-weight-bold text-dark">Semua Posisi</h6>
            
            <form action="{{ route('admin.jobs.index') }}" method="GET" class="m-0">
                <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control border-right-0" placeholder="Cari posisi..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px;">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary border-left-0" style="border-radius: 0 20px 20px 0; background: #fff;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Posisi / Judul</th>
                        <th>Kategori</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Pelamar</th>
                        <th>Dipasang</th>
                        <th class="text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    <tr>
                        <td class="pl-4">
                            <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ Str::limit($job->title, 40) }}</div>
                            {{-- Karena ini single company, nama perusahaan bisa disembunyikan atau dijadikan text muted kecil --}}
                            <div class="text-muted small">{{ $job->company->company_name ?? 'HerbaTech' }}</div>
                        </td>
                        <td>
                            <span class="text-secondary"><i class="fas fa-tags mr-1 opacity-50"></i> {{ $job->category->name ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="status-pill bg-soft-info">
                                {{ match($job->job_type) {
                                    'full_time' => 'Penuh Waktu',
                                    'part_time' => 'Paruh Waktu',
                                    'contract' => 'Kontrak',
                                    'freelance' => 'Freelance',
                                    'internship' => 'Magang',
                                    default => ucfirst(str_replace('_', ' ', $job->job_type))
                                } }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($job->status === 'published')
                                <span class="status-pill bg-soft-success"><i class="fas fa-globe mr-1"></i> Tayang</span>
                            @elseif($job->status === 'closed')
                                <span class="status-pill bg-soft-danger"><i class="fas fa-lock mr-1"></i> Ditutup</span>
                            @else
                                <span class="status-pill bg-soft-warning"><i class="fas fa-file-signature mr-1"></i> Draft</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.applications.index', ['job_id' => $job->id]) }}" class="text-decoration-none">
                                <span class="font-weight-bold text-primary" style="font-size: 1.1rem;">{{ $job->applications_count ?? 0 }}</span>
                                <div class="text-muted" style="font-size: 0.7rem;">Lamaran</div>
                            </a>
                        </td>
                        <td>
                            <div class="text-dark">{{ $job->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $job->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-right pr-4">
                            <a href="{{ route('admin.jobs.show', $job->id) }}" class="btn btn-outline-info btn-action" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn btn-outline-warning btn-action mx-1" title="Edit Lowongan">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.jobs.destroy', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus lowongan ini? Semua data lamaran terkait juga mungkin akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-action" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                <h5>Belum ada lowongan pekerjaan</h5>
                                <p>Klik tombol "Tambah Lowongan" di atas untuk mulai mempublikasikan lowongan baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jobs->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-3" style="border-radius: 0 0 12px 12px;">
            <div class="d-flex justify-content-center">
                {{ $jobs->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection