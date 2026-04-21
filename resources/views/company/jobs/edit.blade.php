@extends('layouts.company')

@section('title', 'Ubah Lowongan: ' . $job->title)

@section('content')
<style>
    body,
    .full-container,
    button,
    input,
    select,
    textarea {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
    }

    :root {
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --text-main: #334155;
        --text-muted: #64748b;
        --text-heading: #1e293b;
        --brand-indigo: #4338ca;
    }

    .full-container {
        width: 100%;
        max-width: 100%;
        padding: 0 15px;
    }

    .edit-card {
        border-radius: 16px;
        border: 1px solid var(--slate-200);
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .card-header-full {
        padding: 30px 40px;
        border-bottom: 1px solid var(--slate-100);
        background: linear-gradient(to right, #ffffff, var(--slate-50));
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-heading);
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid var(--slate-200);
        padding: 12px 16px;
        font-size: 0.95rem;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
    }

    .section-label-display {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--brand-indigo);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin: 40px 0 20px 0;
        display: flex;
        align-items: center;
    }

    .section-label-display::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--slate-100);
        margin-left: 20px;
    }

    .status-badge-info {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        background: var(--slate-100);
        color: var(--text-heading);
    }
</style>

<div class="full-container">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('company.jobs.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-chevron-left me-2"></i> KEMBALI KE MANAJEMEN
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">ID Lowongan: <strong>#{{ $job->id }}</strong></span>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2;">
        <div class="d-flex">
            <i class="fas fa-exclamation-circle text-danger me-3 mt-1"></i>
            <div>
                <span class="fw-bold d-block text-danger">Gagal menyimpan perubahan:</span>
                <ul class="mb-0 small fw-medium text-danger">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="card edit-card border-0">
        <div class="card-header-full d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading);">Edit Lowongan Pekerjaan</h1>
                <p class="text-muted small mb-0">Pastikan informasi jabatan dan kualifikasi sudah sesuai dengan standar HerbaTech.</p>
            </div>
            <div class="status-badge-info">
                STATUS SAAT INI: <span class="text-primary">{{ strtoupper($job->status) }}</span>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('company.jobs.update', $job->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="section-label-display" style="margin-top: 0;">Identitas Posisi</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Jabatan / Judul Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $job->title) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Departemen <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control @error('department') is-invalid @enderror"
                            id="department"
                            name="department"
                            value="{{ old('department', $job->department) }}"
                            placeholder="Contoh: IT, Marketing, Produksi"
                            required> @error('department')
                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            @foreach(\App\Models\JobCategory::where('is_active', true)->get() as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $job->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="location_id" class="form-label">Lokasi Wilayah <span class="text-danger">*</span></label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            @foreach(\App\Models\JobLocation::where('is_active', true)->get() as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $job->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="work_setting" class="form-label">Sistem Kerja <span class="text-danger">*</span></label>
                        <select class="form-select" id="work_setting" name="work_setting" required>
                            <option value="on_site" {{ old('work_setting', $job->work_setting) == 'on_site' ? 'selected' : '' }}>On-site</option>
                            <option value="hybrid" {{ old('work_setting', $job->work_setting) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="remote" {{ old('work_setting', $job->work_setting) == 'remote' ? 'selected' : '' }}>Remote</option>
                        </select>
                    </div>
                </div>

                <div class="section-label-display">Ketentuan & Kompensasi</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <label for="job_type" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select class="form-select" id="job_type" name="job_type" required>
                            <option value="full_time" {{ old('job_type', $job->job_type) == 'full_time' ? 'selected' : '' }}>Full-time</option>
                            <option value="part_time" {{ old('job_type', $job->job_type) == 'part_time' ? 'selected' : '' }}>Part-time</option>
                            <option value="contract" {{ old('job_type', $job->job_type) == 'contract' ? 'selected' : '' }}>Kontrak</option>
                            <option value="harian_lepas" {{ old('job_type', $job->job_type) == 'harian_lepas' ? 'selected' : '' }}>Harian Lepas</option>
                            <option value="internship" {{ old('job_type', $job->job_type) == 'internship' ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="vacancy" class="form-label">Jumlah Orang <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('vacancy') is-invalid @enderror"
                            id="vacancy" name="vacancy"
                            value="{{ old('vacancy', $job->vacancy) }}" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label for="salary_min" class="form-label">Gaji Min (Rp)</label>
                        <input type="number" class="form-control" name="salary_min" value="{{ old('salary_min', $job->salary_min) }}" placeholder="Kosongkan jika tidak dicantumkan">
                    </div>
                    <div class="col-md-3">
                        <label for="salary_max" class="form-label">Gaji Maks (Rp)</label>
                        <input type="number" class="form-control" name="salary_max" value="{{ old('salary_max', $job->salary_max) }}" placeholder="Kosongkan jika tidak dicantumkan">
                    </div>
                </div>

                <div class="row g-4 mb-4 align-items-end">
                    <div class="col-md-4">
                        <label for="salary_type" class="form-label">Tampilkan Gaji Per</label>
                        <select class="form-select" name="salary_type">
                            <option value="monthly" {{ old('salary_type', $job->salary_type) == 'monthly' ? 'selected' : '' }}>Bulan</option>
                            <option value="yearly" {{ old('salary_type', $job->salary_type) == 'yearly' ? 'selected' : '' }}>Tahun</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pb-2">
                            <input type="hidden" name="is_salary_visible" value="0">
                            <input class="form-check-input" type="checkbox" name="is_salary_visible" id="is_salary_visible" value="1" {{ old('is_salary_visible', $job->is_salary_visible) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="is_salary_visible">Publikasikan Estimasi Gaji</label>
                        </div>
                    </div>

                    {{-- Bagian 3.5: Persyaratan Tes Psikologi --}}
                    @php
                    // Ambil data tes dari database atau data old() jika gagal validasi
                    $currentTests = old('required_tests', $job->required_tests ?? []);
                    if (!is_array($currentTests)) $currentTests = [];
                    @endphp
                    <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fas fa-brain text-info mr-2"></i>Persyaratan Tes Psikologi</h6>
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <label class="form-label-custom">Pilih Tes yang Wajib Dikerjakan Pelamar</label>
                            <div class="bg-light p-3 rounded-lg border d-flex gap-4 flex-wrap">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="edit_test_kraepelin" name="required_tests[]" value="kraepelin" {{ in_array('kraepelin', $currentTests) ? 'checked' : '' }}>
                                    <label class="custom-control-label fw-bold" for="edit_test_kraepelin">Kraepelin (Ketelitian)</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="edit_test_msdt" name="required_tests[]" value="msdt" {{ in_array('msdt', $currentTests) ? 'checked' : '' }}>
                                    <label class="custom-control-label fw-bold" for="edit_test_msdt">MSDT (Manajerial)</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="edit_test_papi" name="required_tests[]" value="papi" {{ in_array('papi', $currentTests) ? 'checked' : '' }}>
                                    <label class="custom-control-label fw-bold" for="edit_test_papi">PAPI Kostick (Sikap Kerja)</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="edit_test_disc" name="required_tests[]" value="disc" {{ in_array('disc', $currentTests) ? 'checked' : '' }}>
                                    <label class="custom-control-label fw-bold" for="edit_test_disc">DISC (Kepribadian)</label>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">Centang tes yang sesuai dengan pemetaan level jabatan lowongan ini. Kosongkan jika tidak ada tes.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status Lowongan <span class="text-danger">*</span></label>
                        <select class="form-select border-primary fw-bold" name="status" required>
                            <option value="published" {{ old('status', $job->status) == 'published' ? 'selected' : '' }}>PUBLISHED (TAYANG)</option>
                            <option value="draft" {{ old('status', $job->status) == 'draft' ? 'selected' : '' }}>DRAFT (ARSIP)</option>
                            <option value="closed" {{ old('status', $job->status) == 'closed' ? 'selected' : '' }}>CLOSED (TUTUP)</option>
                        </select>
                    </div>
                </div>

                <div class="section-label-display">Konten Detail & Kualifikasi</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="experience_level" class="form-label">Syarat Pengalaman <span class="text-danger">*</span></label>
                        <select class="form-select" name="experience_level" required>
                            <option value="entry_level" {{ old('experience_level', $job->experience_level) == 'entry_level' ? 'selected' : '' }}>Entry Level</option>
                            <option value="1_3_years" {{ old('experience_level', $job->experience_level) == '1_3_years' ? 'selected' : '' }}>1-3 Tahun</option>
                            <option value="3_5_years" {{ old('experience_level', $job->experience_level) == '3_5_years' ? 'selected' : '' }}>3-5 Tahun</option>
                            <option value="more_than_5_years" {{ old('experience_level', $job->experience_level) == 'more_than_5_years' ? 'selected' : '' }}>Diatas 5 Tahun</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="education_level" class="form-label">Minimal Pendidikan</label>
                        <select class="form-select" name="education_level">
                            <option value="">Pilih Pendidikan</option>
                            <option value="sma" {{ old('education_level') == 'sma' ? 'selected' : '' }}>SMA/SMK</option>
                            <option value="d3" {{ old('education_level') == 'd3' ? 'selected' : '' }}>D3</option>
                            <option value="s1" {{ old('education_level') == 's1' ? 'selected' : '' }}>S1 / D4</option>
                            <option value="s2" {{ old('education_level') == 's2' ? 'selected' : '' }}>S2</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label for="responsibilities" class="form-label">Tanggung Jawab Utama</label>
                        <textarea class="form-control" id="responsibilities" name="responsibilities" rows="5" placeholder="Tuliskan poin-poin tanggung jawab pekerjaan...">{{ old('responsibilities', $job->responsibilities ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="deadline" class="form-label">Batas Akhir Lamaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline', optional($job->deadline)->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi Tugas & Tanggung Jawab <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required style="line-height: 1.6;">{{ old('description', $job->description) }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="requirements" class="form-label">Kualifikasi & Persyaratan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="requirements" name="requirements" rows="6" required style="line-height: 1.6;">{{ old('requirements', $job->requirements) }}</textarea>
                </div>

                <div class="card-footer bg-white px-0 py-4 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('company.jobs.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none">
                        BATALKAN PERUBAHAN
                    </a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow" style="border-radius: 12px; background: var(--brand-indigo); border: none;">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection