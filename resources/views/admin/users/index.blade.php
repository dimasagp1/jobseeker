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
    .bg-soft-primary { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .bg-soft-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .bg-soft-danger { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .bg-soft-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    
    .btn-action { 
        border-radius: 8px; width: 32px; height: 32px; padding: 0; 
        display: inline-flex; align-items: center; justify-content: center; 
    }
</style>

<div class="container-fluid pb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0" style="color: var(--text-heading);"><i class="fas fa-users-cog text-primary mr-2"></i> Manajemen Pengguna</h3>
            <p class="text-muted small mb-0 mt-1">Pantau, aktifkan, atau kelola hak akses seluruh pengguna HerbaTech.</p>
        </div>
        
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 20px; font-weight: 600;">
            <i class="fas fa-plus mr-1"></i> Tambah Pengguna
        </a>
    </div>

    <div class="dashboard-card">
        <div class="card-header bg-white p-3 d-flex align-items-center justify-content-between" style="border-radius: 12px 12px 0 0;">
            <h6 class="mb-0 font-weight-bold text-dark">Daftar Akun</h6>
            
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex form-inline m-0">
                <select name="role" class="form-control form-control-sm mr-2 shadow-sm" style="border-radius: 20px; background: #fff;" onchange="this.form.submit()">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="company" {{ request('role') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                    <option value="seeker" {{ request('role') == 'seeker' ? 'selected' : '' }}>Pencari Kerja</option>
                </select>

                <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control border-right-0" placeholder="Cari nama atau email..." value="{{ request('search') }}" style="border-radius: 20px 0 0 20px;">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-secondary border-left-0" style="border-radius: 0 20px 20px 0; background: #fff;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle text-nowrap mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Pengguna</th>
                        <th class="text-center">Peran</th>
                        <th class="text-center">Status Akun</th>
                        <th class="text-center">Aktivitas</th>
                        <th>Tanggal Daftar</th>
                        <th class="pr-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="pl-4">
                            <div class="d-flex align-items-center">
                                @php
                                    $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=f1f5f9&color=334155';
                                    if ($user->role === 'company' && $user->company?->company_logo) {
                                        $avatarUrl = asset('storage/' . $user->company->company_logo);
                                    } elseif ($user->avatar) {
                                        $avatarUrl = asset('storage/' . $user->avatar);
                                    }
                                @endphp
                                
                                <img src="{{ $avatarUrl }}" class="rounded-circle mr-3 border shadow-sm" width="42" height="42" style="object-fit: cover;" alt="Avatar">
                                
                                <div>
                                    <div class="mb-0 font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="status-pill {{ $user->role === 'admin' ? 'bg-soft-danger' : ($user->role === 'company' ? 'bg-soft-success' : 'bg-soft-primary') }}">
                                {{ match($user->role) {
                                    'admin' => 'Admin',
                                    'company' => 'HR HerbaTech',
                                    'seeker' => 'Pelamar',
                                    default => ucfirst($user->role)
                                } }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success border-success border px-2 py-1" style="border-radius: 8px; font-size: 0.7rem;">
                                    <i class="fas fa-check-circle mr-1"></i> AKTIF
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-muted border px-2 py-1" style="border-radius: 8px; font-size: 0.7rem;">
                                    <i class="fas fa-ban mr-1"></i> NONAKTIF
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->role === 'seeker')
                                <div class="font-weight-bold text-primary">{{ $user->applications_count ?? 0 }}</div>
                                <div class="text-muted small" style="font-size: 0.65rem; text-transform: uppercase;">Lamaran</div>
                            @elseif($user->role === 'company')
                                <small class="text-muted italic">Rekruter</small>
                            @else
                                <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $user->created_at->format('d M Y') }}</div>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="pr-4 text-right">
                            <div class="d-flex justify-content-end gap-1">
                                {{-- Detail --}}
                                @if($user->role !== 'admin')
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-primary btn-action" title="Lihat Profil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif

                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-info btn-action mx-1" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($user->id !== Auth::id())
                                    {{-- Toggle Status --}}
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-action" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'power-off' : 'check' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action ml-1" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i>
                                <h5>Tidak ada pengguna ditemukan</h5>
                                <p>Silakan sesuaikan kata kunci pencarian atau filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-3" style="border-radius: 0 0 12px 12px;">
            <div class="d-flex justify-content-center">
                {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection