@extends('layouts.admin')

@section('content')
<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-600: #475569;
        --text-heading: #1e293b;
        --brand-primary: #0d6efd;
    }
    .main-card {
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }
    .card-header-custom {
        background: #fff;
        padding: 24px;
        border-bottom: 1px solid var(--slate-100);
    }
    .table-modern thead th {
        background-color: var(--slate-50);
        color: var(--slate-600);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        border-top: none;
        padding: 16px 24px;
    }
    .table-modern tbody td {
        padding: 18px 24px;
        vertical-align: middle;
        color: #334155;
        font-size: 0.95rem;
        border-bottom: 1px solid var(--slate-50);
    }
    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-soft {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-success { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .badge-soft-secondary { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .count-indicator {
        background: #f0f9ff;
        color: var(--brand-primary);
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        border: 1px solid #e0f2fe;
        display: inline-block;
        min-width: 40px;
    }
</style>

<div class="container-fluid pb-5">
    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="main-card">
        {{-- Header Card Identik dengan Settings --}}
        <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-soft-primary p-3 rounded-circle d-none d-md-flex" style="background: #eef2ff; color: #4338ca; width: 48px; height: 48px; align-items: center; justify-content: center;">
                    <i class="fas fa-tags fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-heading);">Manajemen Kategori</h4>
                    <p class="text-muted small mb-0">Kelola pengelompokan pekerjaan untuk memudahkan pencarian pelamar.</p>
                </div>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 20px;">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th width="80" class="text-center">ID</th>
                        <th>Informasi Kategori</th>
                        <th>Slug (URL)</th>
                        <th class="text-center">Total Lowongan</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="text-muted text-center fw-bold">#{{ $category->id }}</td>
                        <td>
                            <span class="fw-bold d-block text-dark" style="font-size: 1rem;">{{ $category->name }}</span>
                        </td>
                        <td>
                            <code class="text-xs bg-light px-2 py-1 rounded text-muted border">{{ $category->slug }}</code>
                        </td>
                        <td class="text-center">
                            {{-- Saya hapus icon briefcase dan ganti dengan styling angka yang lebih tebal dan bersih --}}
                            <span class="count-indicator">
                                {{ $category->jobs_count ?? 0 }}
                            </span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge-soft badge-soft-success">
                                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i> Aktif
                                </span>
                            @else
                                <span class="badge-soft badge-soft-secondary">
                                    <i class="fas fa-circle mr-1" style="font-size: 0.5rem;"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                               class="btn btn-action btn-light border text-warning mr-1" 
                               title="Edit Kategori">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                  method="POST" 
                                  class="d-inline" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Data lowongan yang terkait mungkin akan terpengaruh.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-action btn-light border text-danger" title="Hapus Kategori">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-folder-open fa-2x text-muted opacity-50"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Tidak ada kategori</h6>
                                <p class="text-muted small">Anda belum menambahkan kategori lowongan apapun.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <small class="text-muted mb-0">
                Menampilkan {{ $categories->firstItem() }}-{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
            </small>
            {{ $categories->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection