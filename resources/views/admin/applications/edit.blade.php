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
    .form-card {
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        background: #fff;
    }
    .form-card-header {
        border-bottom: 1px solid var(--slate-100);
        padding: 24px;
        background: #fff;
        border-radius: 16px 16px 0 0;
    }
    .form-label-custom {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .input-style {
        background-color: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.2s;
        height: auto;
    }
    .input-style:focus {
        background-color: #fff;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        outline: none;
    }
    .data-box {
        background: var(--slate-50);
        border: 1px solid var(--slate-100);
        border-radius: 12px;
        padding: 16px 20px;
    }
    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        display: block;
        margin-bottom: 4px;
    }
    .data-value {
        font-weight: 600;
        color: var(--text-heading);
        font-size: 0.95rem;
    }
</style>

<div class="container-fluid pb-5">
    <div class="mb-4">
        <a href="{{ route('admin.applications.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Lamaran
        </a>
    </div>

    <div class="row">
        <div class="col-12"> {{-- Dibuat Full Width --}}
            <div class="form-card">
                <div class="form-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--text-heading);">Perbarui Status Lamaran</h4>
                        <p class="text-muted small mb-0">Kelola status dan catatan internal untuk lamaran <span class="fw-bold text-dark">#{{ $application->id }}</span>.</p>
                    </div>
                    <div class="bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #fffbeb; color: #d97706;">
                        <i class="fas fa-edit"></i>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 border-bottom">
                    {{-- Ringkasan Lamaran --}}
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="data-box h-100">
                                <span class="data-label">Nama Kandidat</span>
                                <span class="data-value">{{ $application->user->name ?? 'Kandidat Tidak Diketahui' }}</span>
                                <div class="text-muted small mt-1">{{ $application->user->email ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="data-box h-100">
                                <span class="data-label">Posisi Dilamar</span>
                                <span class="data-value text-primary">{{ $application->job->title ?? 'Pekerjaan Dihapus' }}</span>
                                <div class="text-muted small mt-1"><i class="fas fa-building mr-1"></i> {{ $application->job->company->company_name ?? 'HerbaTech' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="data-box h-100">
                                <span class="data-label">Waktu & Status Saat Ini</span>
                                <span class="data-value">{{ $application->created_at->format('d M Y - H:i') }}</span>
                                <div class="mt-1">
                                    <span class="badge bg-primary-subtle text-primary border px-2 py-1" style="border-radius: 6px; font-size: 0.75rem;">
                                        {{ strtoupper($application->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.applications.update', $application->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Ubah Status Evaluasi <span class="text-danger">*</span></label>
                                <select name="status" class="form-control input-style fw-bold text-dark @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $application->status) == 'pending' ? 'selected' : '' }}>🕒 Menunggu (Pending)</option>
                                    <option value="reviewed" {{ old('status', $application->status) == 'reviewed' ? 'selected' : '' }}>👀 Sedang Ditinjau (Reviewed)</option>
                                    <option value="shortlisted" {{ old('status', $application->status) == 'shortlisted' ? 'selected' : '' }}>⭐ Masuk Pertimbangan (Shortlisted)</option>
                                    <option value="test_invited" {{ old('status', $application->status) == 'test_invited' ? 'selected' : '' }}>📝 Undangan Tes Psikotes (Test Invited)</option>
                                    <option value="test_in_progress" {{ old('status', $application->status) == 'test_in_progress' ? 'selected' : '' }}>⏳ Sedang Mengerjakan Tes (Test in Progress)</option>
                                    <option value="test_completed" {{ old('status', $application->status) == 'test_completed' ? 'selected' : '' }}>📊 Tes Selesai (Test Completed)</option>
                                    <option value="interview" {{ old('status', $application->status) == 'interview' ? 'selected' : '' }}>🎤 Dijadwalkan Wawancara (Interview)</option>
                                    <option value="accepted" {{ old('status', $application->status) == 'accepted' ? 'selected' : '' }}>✅ Diterima Bekerja (Accepted)</option>
                                    <option value="rejected" {{ old('status', $application->status) == 'rejected' ? 'selected' : '' }}>❌ Ditolak (Rejected)</option>
                                </select>
                                @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Catatan Internal (Admin Only)</label>
                                <textarea name="notes" class="form-control input-style @error('notes') is-invalid @enderror" rows="2" placeholder="Catatan evaluasi tim HR...">{{ old('notes', $application->notes) }}</textarea>
                                @error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                        <div class="col-12 mt-4">
                            <label class="form-label-custom">Dokumen Lamaran (Cover Letter / CV)</label>
                            <div class="bg-light p-4 rounded-lg border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background: #fef2f2;">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">File Dokumen Kandidat</h6>
                                        <small class="text-muted">Format: PDF / DOCX</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    @if($application->cv_path)
                                        <a href="{{ asset('storage/' . $application->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold">
                                            <i class="fas fa-eye mr-1"></i> Lihat Dokumen
                                        </a>
                                        <a href="{{ route('admin.applications.download-cv', $application->id) }}" class="btn btn-sm btn-primary px-3 rounded-pill fw-bold shadow-sm">
                                            <i class="fas fa-download mr-1"></i> Unduh
                                        </a>
                                    @else
                                        <span class="text-danger small fw-bold">File tidak ditemukan</span>
                                    @endif
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle mr-1"></i> Sebagai Admin, Anda hanya dapat meninjau dokumen ini. Perubahan file hanya dapat dilakukan oleh kandidat atau melalui input manual.
                            </small>
                        </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-right py-4" style="border-top: 1px solid var(--slate-100); border-radius: 0 0 16px 16px;">
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-white border px-4 font-weight-bold shadow-sm" style="border-radius: 20px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 ml-2 font-weight-bold shadow-sm" style="border-radius: 20px; background-color: var(--brand-primary); border: none;">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection