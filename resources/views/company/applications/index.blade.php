@extends('layouts.company')

@section('title', 'Daftar Lamaran Kerja')

@section('content')
<style>
    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --text-main: #334155; 
        --text-heading: #1e293b;
        --brand-indigo: #4338ca; 
    }

    .app-card { 
        border-radius: 16px; 
        border: 1px solid var(--slate-200); 
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table thead th {
        background: var(--slate-50);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        padding: 16px 24px;
        border-bottom: 1px solid var(--slate-200);
    }

    .table td { padding: 20px 24px; vertical-align: middle; }

    /* Kraepelin Score Mini Card */
    .test-score-box {
        display: inline-flex;
        flex-direction: column;
        gap: 2px;
        min-width: 90px;
    }
    .score-pill {
        font-size: 0.65rem;
        font-weight: 800;
        display: flex;
        justify-content: space-between;
        padding: 3px 8px;
        border-radius: 5px;
        background: var(--slate-50);
        border: 1px solid var(--slate-100);
    }
    .score-label { color: #64748b; }
    .score-val { color: var(--brand-indigo); }

    /* Soft Status Badges */
    .status-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    
    /* Mapping Status Colors */
    .status-pending { background: #fffbeb; color: #92400e; }
    .status-reviewed { background: #f1f5f9; color: #475569; }
    .status-shortlisted { background: #eff6ff; color: #1d4ed8; }
    .status-test_invited { background: #e0e7ff; color: #4338ca; }
    .status-test_in_progress { background: #fff7ed; color: #c2410c; }
    .status-test_completed { background: #f0fdf4; color: #166534; }
    .status-accepted { background: #dcfce7; color: #15803d; }
    .status-rejected { background: #fef2f2; color: #b91c1c; }

    .btn-review {
        background: white;
        border: 1px solid var(--slate-200);
        color: var(--text-heading);
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 8px;
        padding: 6px 14px;
        transition: all 0.2s;
    }
    .btn-review:hover {
        border-color: var(--brand-indigo);
        color: var(--brand-indigo);
        background: var(--slate-50);
    }
</style>

<div class="mb-4 d-flex justify-content-between align-items-end">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-heading); letter-spacing: -0.02em;">Manajemen Lamaran</h2>
        <p class="text-muted small mb-0">Tinjau profil kandidat dan pantau hasil Tes Kraepelin secara real-time.</p>
    </div>
    <div class="text-end">
        <span class="badge bg-white text-dark border px-3 py-2 rounded-3 shadow-sm">
            <i class="fas fa-users me-2 text-primary"></i>Total: <strong>{{ $applications->total() }}</strong> Kandidat
        </span>
    </div>
</div>
<div class="card app-card border-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Informasi Kandidat</th>
                    <th>Posisi & Departemen</th>
                    <th class="text-center">Ringkasan Tes</th>
                    <th class="text-center">Status Rekrutmen</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name) }}&background=f1f5f9&color=4338ca" 
                                class="rounded-circle me-3" width="38" alt="Avatar">
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $application->user->name }}</div>
                                <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ $application->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $application->job->title }}</div>
                        <div class="text-muted extra-small" style="font-size: 0.75rem;">
                            <i class="far fa-clock me-1"></i>{{ $application->created_at->diffForHumans() }}
                        </div>
                    </td>
                    
                    <td class="text-center">
                        @if($application->kraepelinTest && $application->kraepelinTest->completed_at)
                            <div class="test-score-box">
                                <div class="score-pill" title="Speed: Total jawaban">
                                    <span class="score-label">SPD:</span>
                                    <span class="score-val">{{ $application->kraepelinTest->total_answered }}</span>
                                </div>
                                <div class="score-pill" title="Accuracy: Persentase benar">
                                    <span class="score-label">ACC:</span>
                                    @php
                                        $acc = $application->kraepelinTest->total_answered > 0 
                                            ? round(($application->kraepelinTest->total_correct / $application->kraepelinTest->total_answered) * 100) 
                                            : 0;
                                    @endphp
                                    <span class="score-val text-success">{{ $acc }}%</span>
                                </div>
                            </div>
                        @elseif($application->status === 'test_in_progress')
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            <div class="extra-small text-warning fw-bold mt-1" style="font-size: 0.65rem;">PROGRESS</div>
                        @elseif($application->status === 'test_invited')
                            <span class="text-muted extra-small italic">Belum Mulai</span>
                        @else
                            <span class="text-muted opacity-25">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <span class="status-badge status-{{ $application->status }}">
                            <i class="fas fa-circle me-2" style="font-size: 0.35rem;"></i>
                            {{ $application->status_label }}
                        </span>
                    </td>
                    
                    <td class="text-end">
                        <a href="{{ route('company.applications.show', $application->id) }}" class="btn btn-review">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0 fw-bold">Belum ada lamaran masuk</p>
                            <small>Data lamaran yang masuk akan muncul di sini.</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($applications->hasPages())
    <div class="card-footer bg-white py-4 px-4 border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan {{ $applications->firstItem() }} - {{ $applications->lastItem() }} dari {{ $applications->total() }} kandidat
            </div>
            <div>
                {{ $applications->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection