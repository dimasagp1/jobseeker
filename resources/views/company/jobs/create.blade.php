@extends('layouts.company')

@section('title', 'Pasang Lowongan Baru')

@section('content')
<style>
    /* Konsistensi Font Modern */
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

    .create-card {
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
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
        outline: none;
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
</style>

<div class="full-container pb-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('company.jobs.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-chevron-left me-2"></i> KEMBALI KE MANAJEMEN
        </a>
    </div>

    <div class="card create-card border-0">
        <div class="card-header-full">
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-heading);">Pasang Lowongan Baru</h1>
            <p class="text-muted small mb-0">Lengkapi formulir di bawah untuk mempublikasikan peluang karir di HerbaTech.</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ route('company.jobs.store') }}" method="POST">
                @csrf

                <div class="section-label-display" style="margin-top: 0;">Identitas Posisi</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Jabatan / Judul Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Senior Pharmacist" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Departemen</label>
                        <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department') }}" placeholder="Contoh: Research & Development">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach(\App\Models\JobCategory::where('is_active', true)->get() as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="location_id" class="form-label">Lokasi Wilayah <span class="text-danger">*</span></label>
                        <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach(\App\Models\JobLocation::where('is_active', true)->get() as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="work_setting" class="form-label">Opsi Tempat Kerja <span class="text-danger">*</span></label>
                        <select class="form-select @error('work_setting') is-invalid @enderror" id="work_setting" name="work_setting" required>
                            <option value="on_site" {{ old('work_setting') == 'on_site' ? 'selected' : '' }}>On-site (Di Kantor)</option>
                            <option value="hybrid" {{ old('work_setting') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="remote" {{ old('work_setting') == 'remote' ? 'selected' : '' }}>Remote (Jarak Jauh)</option>
                        </select>
                    </div>
                </div>

                <div class="section-label-display">Ketentuan & Kompensasi</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <label for="job_type" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                        <select class="form-select" id="job_type" name="job_type" required>
                            <option value="full_time" {{ old('job_type') == 'full_time' ? 'selected' : '' }}>Purnawaktu</option>
                            <option value="part_time" {{ old('job_type') == 'part_time' ? 'selected' : '' }}>Paruh Waktu</option>
                            <option value="contract" {{ old('job_type') == 'contract' ? 'selected' : '' }}>Kontrak</option>
                            <option value="harian_lepas" {{ old('job_type') == 'harian_lepas' ? 'selected' : '' }}>Harian Lepas</option>
                            <option value="internship" {{ old('job_type') == 'internship' ? 'selected' : '' }}>Magang</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="salary_currency" class="form-label">Mata Uang <span class="text-danger">*</span></label>
                        <select class="form-select @error('salary_currency') is-invalid @enderror" name="salary_currency" required>
                            <option value="IDR" {{ old('salary_currency') == 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                            <option value="USD" {{ old('salary_currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        </select>
                        @error('salary_currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="salary_min" class="form-label">Gaji Minimum</label>
                        <input type="number" class="form-control" name="salary_min" value="{{ old('salary_min') }}" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label for="salary_max" class="form-label">Gaji Maksimum</label>
                        <input type="number" class="form-control" name="salary_max" value="{{ old('salary_max') }}" placeholder="0">
                    </div>
                </div>

                <div class="row g-4 mb-4 align-items-center">
                    <div class="col-md-3">
                        <label for="salary_type" class="form-label">Tampilkan Gaji Per</label>
                        <select class="form-select" name="salary_type">
                            <option value="monthly" {{ old('salary_type') == 'monthly' ? 'selected' : '' }}>Bulan</option>
                            <option value="yearly" {{ old('salary_type') == 'yearly' ? 'selected' : '' }}>Tahun</option>
                            <option value="hourly" {{ old('salary_type') == 'hourly' ? 'selected' : '' }}>Jam</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="vacancy" class="form-label">Jumlah Lowongan</label>
                        <input type="number" class="form-control" name="vacancy" value="{{ old('vacancy', 1) }}" min="1">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch pt-4">
                            <input type="hidden" name="is_salary_visible" value="0">
                            <input class="form-check-input" type="checkbox" name="is_salary_visible" id="is_salary_visible" value="1" {{ old('is_salary_visible', 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="is_salary_visible">Tampilkan Gaji ke Publik</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch pt-4">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small text-primary" for="is_featured"><i class="fas fa-star me-1"></i> Tandai Unggulan</label>
                        </div>
                    </div>
                </div>

                <div class="section-label-display">Detail Persyaratan</div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="experience_level" class="form-label">Minimal Pengalaman <span class="text-danger">*</span></label>
                        <select class="form-select" name="experience_level" required>
                            <option value="entry_level" {{ old('experience_level') == 'entry_level' ? 'selected' : '' }}>Fresh Graduate / Entry Level</option>
                            <option value="1_3_years" {{ old('experience_level') == '1_3_years' ? 'selected' : '' }}>1 - 3 Tahun</option>
                            <option value="3_5_years" {{ old('experience_level') == '3_5_years' ? 'selected' : '' }}>3 - 5 Tahun</option>
                            <option value="more_than_5_years" {{ old('experience_level') == 'more_than_5_years' ? 'selected' : '' }}>Diatas 5 Tahun</option>
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
                    <div class="col-md-4">
                        <label for="deadline" class="form-label">Batas Waktu Lamaran <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="deadline" name="deadline" value="{{ old('deadline') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Tuliskan deskripsi pekerjaan secara detail..." required>{{ old('description') }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="requirements" class="form-label">Kualifikasi / Persyaratan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="requirements" name="requirements" rows="5" placeholder="Tuliskan kualifikasi yang dibutuhkan..." required>{{ old('requirements') }}</textarea>
                </div>

                <div class="mb-5">
                    <label for="responsibilities" class="form-label">Tanggung Jawab Utama</label>
                    <textarea class="form-control" id="responsibilities" name="responsibilities" rows="5" placeholder="Tuliskan poin-poin tanggung jawab pekerjaan...">{{ old('responsibilities', $job->responsibilities ?? '') }}</textarea>
                </div>

                {{-- Bagian 3.5: Persyaratan Tes Psikologi --}}
                <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fas fa-brain text-info mr-2"></i>Persyaratan Tes Psikologi</h6>
                <div class="row g-4 mb-5">
                    <div class="col-12">
                        <label class="form-label-custom">Pilih Tes yang Wajib Dikerjakan Pelamar</label>
                        <div class="bg-light p-3 rounded-lg border d-flex gap-4 flex-wrap">
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="test_kraepelin" name="required_tests[]" value="kraepelin" {{ is_array(old('required_tests')) && in_array('kraepelin', old('required_tests')) ? 'checked' : '' }}>
                                <label class="custom-control-label fw-bold" for="test_kraepelin">Kraepelin (Ketelitian)</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="test_msdt" name="required_tests[]" value="msdt" {{ is_array(old('required_tests')) && in_array('msdt', old('required_tests')) ? 'checked' : '' }}>
                                <label class="custom-control-label fw-bold" for="test_msdt">MSDT (Manajerial)</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="test_papi" name="required_tests[]" value="papi" {{ is_array(old('required_tests')) && in_array('papi', old('required_tests')) ? 'checked' : '' }}>
                                <label class="custom-control-label fw-bold" for="test_papi">PAPI Kostick (Sikap Kerja)</label>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" id="test_disc" name="required_tests[]" value="disc" {{ is_array(old('required_tests')) && in_array('disc', old('required_tests')) ? 'checked' : '' }}>
                                <label class="custom-control-label fw-bold" for="test_disc">DISC (Kepribadian)</label>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Centang tes yang sesuai dengan pemetaan level jabatan lowongan ini. Kosongkan jika tidak ada tes.</small>
                        @error('required_tests') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <input type="hidden" name="status" value="published">

                <div class="card-footer bg-white px-0 py-4 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('company.jobs.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none">
                        BATALKAN
                    </a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow" style="border-radius: 12px; background: var(--brand-indigo); border: none;">
                        TERBITKAN LOWONGAN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection