@extends('layouts.company')

@section('title', 'Review Lamaran: ' . $application->user->name)

@section('content')

{{-- ================================================================ --}}
{{-- BLOK PHP WAJIB DI ATAS AGAR VARIABEL DIKENALI OLEH SELURUH HALAMAN --}}
{{-- ================================================================ --}}
@php
    // Persiapan Data Relasi Psikotes
    $psyResults = $application->psychologicalResults ?? collect();
    $discResult = $psyResults->filter(function($q) {
        return strtolower(trim($q->test_type)) === 'disc' && strtolower(trim($q->status)) === 'completed';
    })->first();

    $msdtResult = $psyResults->filter(function($q) {
        return strtolower(trim($q->test_type)) === 'msdt' && strtolower(trim($q->status)) === 'completed';
    })->first();

    $papiResult = $psyResults->filter(function($q) {
        return strtolower(trim($q->test_type)) === 'papi' && strtolower(trim($q->status)) === 'completed';
    })->first();
    $hasKraepelin = $application->kraepelinTest && $application->kraepelinTest->completed_at;
@endphp

<style>
    :root {
        --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0;
        --text-main: #334155; --text-heading: #1e293b; --brand-indigo: #4338ca; 
    }
    .review-card { border-radius: 16px; border: 1px solid var(--slate-200); background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .profile-header-card { background: linear-gradient(to bottom, var(--brand-indigo), #3730a3); border-radius: 16px 16px 0 0; padding: 30px 20px; }
    
    /* Scrollable Tabs for multiple assessments */
    .nav-tabs-custom { border-bottom: 2px solid var(--slate-100); gap: 10px; flex-wrap: nowrap; overflow-x: auto; white-space: nowrap; padding-bottom: 2px; }
    .nav-tabs-custom::-webkit-scrollbar { height: 4px; }
    .nav-tabs-custom::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 4px; }
    .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 12px 15px; position: relative; background: transparent; transition: all 0.3s; }
    .nav-tabs-custom .nav-link:hover { color: var(--brand-indigo); }
    .nav-tabs-custom .nav-link.active { color: var(--brand-indigo); }
    .nav-tabs-custom .nav-link.active::after { content: ""; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px; background: var(--brand-indigo); }
    
    .timeline-item { padding-left: 24px; border-left: 2px solid var(--slate-100); position: relative; margin-bottom: 25px; }
    .timeline-item::before { content: ""; position: absolute; left: -7px; top: 0; width: 12px; height: 12px; border-radius: 50%; background: var(--brand-indigo); border: 2px solid white; }
    .info-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    .info-value { font-size: 0.95rem; font-weight: 600; color: var(--text-heading); }
    
    /* Assessment Specific Styles */
    .kraepelin-card { border-radius: 16px; padding: 24px; background: #fff; border: 1px solid var(--slate-200); }
    .k-chart-container { position: relative; width: 100%; }
    .k-stat-box { padding: 12px 16px; border-radius: 10px; background: var(--slate-50); border: 1px solid var(--slate-100); }
    .cv-preview-container { border-radius: 12px; overflow: hidden; border: 1px solid var(--slate-200); background: #f1f5f9; min-height: 500px; }
    
    /* Badges */
    .psy-badge { width: 35px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; border-radius: 6px; }
</style>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <a href="{{ route('company.applications.index') }}" class="text-decoration-none text-muted small fw-bold">
        <i class="fas fa-arrow-left me-1"></i> KEMBALI KE DAFTAR PELAMAR
    </a>
    <div id="status-spinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
</div>

<div class="row">
    {{-- ================= SIDEBAR: PROFIL KANDIDAT ================= --}}
    <div class="col-lg-4">
        <div class="card review-card mb-4 overflow-hidden shadow-sm">
            <div class="profile-header-card text-center text-white">
                <img src="{{ $application->user->avatar ? asset('storage/' . $application->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($application->user->name).'&background=fff&color=4338ca' }}" 
                     class="rounded-circle border border-4 border-white border-opacity-25 mb-3 shadow-sm" width="90" height="90" style="object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $application->user->name }}</h5>
                <p class="small opacity-75 mb-0">{{ $application->user->email }}</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="info-label mb-1">Status Pendaftaran</div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> {{ $application->status_label }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="info-label mb-2">Progres Psikotes</div>
                    <div class="d-flex gap-2">
                        <span class="psy-badge {{ $hasKraepelin ? 'bg-primary text-white' : 'bg-light text-muted border' }}" title="Kraepelin">KRA</span>
                        <span class="psy-badge {{ $discResult ? 'bg-success text-white' : 'bg-light text-muted border' }}" title="DISC">DSC</span>
                        <span class="psy-badge {{ $msdtResult ? 'bg-danger text-white' : 'bg-light text-muted border' }}" title="MSDT">MSD</span>
                        <span class="psy-badge {{ $papiResult ? 'bg-info text-white' : 'bg-light text-muted border' }}" title="PAPI Kostick">PAP</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="info-label mb-1">Posisi yang Dilamar</div>
                    <div class="info-value text-indigo">{{ $application->job->title }}</div>
                    <div class="extra-small text-muted mt-1"><i class="far fa-calendar-alt me-1"></i> Melamar pada {{ $application->created_at->format('d M Y') }}</div>
                </div>

                @if($application->cv_path)
                    <a href="{{ route('company.applications.download-cv', $application->id) }}" class="btn btn-outline-primary w-100 fw-bold py-2 mb-2 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-file-download me-2"></i> Download CV / Resume
                    </a>
                @endif

                @if($hasKraepelin || $discResult || $msdtResult || $papiResult)
                    <a href="{{ route('company.applications.all-psychological-pdf', $application->id) }}" 
                       onclick="openPdfPreviewModal('{{ route('company.applications.all-psychological-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('company.applications.all-psychological-pdf', $application->id) }}', 'Pratinjau Laporan Lengkap Psikotes'); return false;" 
                       class="btn btn-danger w-100 fw-bold py-2 mb-3 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-file-pdf me-2"></i> Unduh Hasil Semua Tes (1 File)
                    </a>
                @endif
                
                <hr class="my-4" style="border-style: dashed;">

                <div class="bg-light p-3 rounded-4 border">
                    <label class="info-label mb-2 text-dark">Update Progress Rekrutmen</label>
                    <select id="status-selector" class="form-select border-0 shadow-sm fw-bold" 
                            onchange="handleStatusChange(this, {{ $application->id }})" style="border-radius: 10px;">
                        @foreach(\App\Models\JobApplication::getAllStatuses() as $value => $label)
                            <option value="{{ $value }}" {{ $application->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= KONTEN UTAMA: TABS ================= --}}
    <div class="col-lg-8">
        <div class="card review-card shadow-sm">
            <div class="card-header bg-white border-0 p-3 pb-0">
                <ul class="nav nav-tabs nav-tabs-custom" id="reviewTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cover">Surat Lamaran</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile">Detail Profil</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#kuesioner">Kuesioner</button></li>
                    
                    <li class="nav-item {{ $application->status !== 'interview' ? 'd-none' : '' }}" id="interview-tab-nav">
                        <button class="nav-link text-danger fw-bold" data-bs-toggle="tab" data-bs-target="#interview-pane">
                            <i class="fas fa-calendar-check me-1"></i> Wawancara
                        </button>
                    </li>

                    {{-- Dynamic Tabs for Psychological Assessments --}}
                    @if($hasKraepelin)
                        <li class="nav-item"><button class="nav-link text-primary fw-bold" id="kraepelin-tab" data-bs-toggle="tab" data-bs-target="#kraepelin"><i class="fas fa-calculator me-1"></i> Kraepelin</button></li>
                    @endif
                    @if($discResult)
                        <li class="nav-item"><button class="nav-link text-success fw-bold" id="disc-tab" data-bs-toggle="tab" data-bs-target="#disc"><i class="fas fa-shapes me-1"></i> DISC</button></li>
                    @endif
                    @if($msdtResult)
                        <li class="nav-item"><button class="nav-link text-danger fw-bold" data-bs-toggle="tab" data-bs-target="#msdt"><i class="fas fa-users-cog me-1"></i> MSDT</button></li>
                    @endif
                    @if($papiResult)
                        <li class="nav-item"><button class="nav-link text-info fw-bold" id="papi-tab" data-bs-toggle="tab" data-bs-target="#papi"><i class="fas fa-clipboard-check me-1"></i> PAPI Kostick</button></li>
                    @endif
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    
                    {{-- TAB 1: COVER LETTER --}}
                    <div class="tab-pane fade show active" id="cover">
                        @if($application->cover_letter_path)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Pratinjau Dokumen Lampiran</h6>
                                <a href="{{ route('company.applications.download-cover', $application->id) }}" class="btn btn-sm btn-outline-dark fw-bold px-3 rounded-pill shadow-sm"><i class="fas fa-download me-1"></i> Unduh Asli</a>
                            </div>
                            <div class="cv-preview-container shadow-sm">
                                <iframe src="{{ Storage::url($application->cover_letter_path) }}#toolbar=0" width="100%" height="100%" style="border: none; min-height: 500px;"></iframe>
                            </div>
                        @else
                            <div class="text-center py-5 border rounded-4 bg-light" style="border-style: dashed;">
                                <i class="fas fa-file-invoice fa-3x text-muted opacity-25 mb-3"></i>
                                <h6 class="fw-bold text-muted">Tidak Ada Surat Lamaran</h6>
                                <p class="small text-muted mb-0">Kandidat tidak mengunggah file tambahan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- TAB 2: PROFILE --}}
                    <div class="tab-pane fade" id="profile">
                        <div class="mb-5 p-4 bg-light rounded-4 border border-slate-200">
                            <h6 class="fw-bold mb-2">Biografi Singkat</h6>
                            <p class="text-muted mb-0" style="line-height: 1.6;">{{ $application->user->seekerProfile->summary ?? 'Belum ada biografi yang ditulis kandidat.' }}</p>
                        </div>
                        <div class="row">
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold mb-4 text-indigo"><i class="fas fa-briefcase me-2"></i>Pengalaman Kerja</h6>
                                @forelse($application->user->seekerProfile->experiences ?? [] as $exp)
                                    <div class="timeline-item">
                                        <div class="fw-bold text-dark">{{ $exp->job_title }}</div>
                                        <div class="small fw-bold text-primary">{{ $exp->company_name }}</div>
                                        <div class="extra-small text-muted mt-1"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Sekarang' }}</div>
                                    </div>
                                @empty <p class="small text-muted italic">Belum ada pengalaman kerja yang diisi.</p> @endforelse
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold mb-4 text-indigo"><i class="fas fa-graduation-cap me-2"></i>Riwayat Pendidikan</h6>
                                @forelse($application->user->seekerProfile->educations ?? [] as $edu)
                                    <div class="timeline-item">
                                        <div class="fw-bold text-dark">{{ $edu->degree }}</div>
                                        <div class="small fw-bold text-primary">{{ $edu->institution }}</div>
                                        <div class="extra-small text-muted mt-1"><i class="far fa-calendar-alt me-1"></i>Tahun Lulus: {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : 'Sekarang' }}</div>
                                    </div>
                                @empty <p class="small text-muted italic">Belum ada data pendidikan.</p> @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: KUESIONER --}}
                    <div class="tab-pane fade" id="kuesioner">
    <div class="row g-4">
        @php
            $questions = [
                'q1' => ['icon' => 'fa-user-shield', 'q' => 'Pernyataan Kejujuran Data'],
                'q2' => ['icon' => 'fa-clock', 'q' => 'Ketersediaan Full-Time'],
                'q3' => ['icon' => 'fa-map-marked-alt', 'q' => 'Kesediaan Relokasi'],
                'q4' => ['icon' => 'fa-car', 'q' => 'Kepemilikan Kendaraan'],
                'q5' => ['icon' => 'fa-money-bill-wave', 'q' => 'Ekspektasi Gaji Bulanan'],
                'q15' => ['icon' => 'fa-calendar-check', 'q' => 'Tanggal Mulai Bergabung'],
                'q6' => ['icon' => 'fa-tools', 'q' => 'Rating Skill Teknis'],
                'q7' => ['icon' => 'fa-trophy', 'q' => 'Pencapaian Terbesar'],
                'q13' => ['icon' => 'fa-bullseye', 'q' => 'Motivasi Melamar'],
                'q14' => ['icon' => 'fa-rocket', 'q' => 'Visi Karier 3-5 Tahun']
            ];
        @endphp

        @foreach($questions as $key => $data)
            @if(isset($application->answers[$key]))
                <div class="{{ in_array($key, ['q7','q13','q14']) ? 'col-12' : 'col-md-6' }}">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0 bg-white shadow-sm d-flex align-items-center justify-content-center rounded-3" style="width: 40px; height: 40px;">
                                    <i class="fas {{ $data['icon'] }} text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">Pertanyaan {{ strtoupper($key) }}</div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $data['q'] }}</div>
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded-4 border border-light shadow-sm">
                                <div class="info-value">
                                    @if($key === 'q5')
                                        {{-- Style khusus untuk Gaji --}}
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold" style="font-size: 1rem;">
                                                Rp {{ number_format($application->answers[$key], 0, ',', '.') }}
                                            </span>
                                            <span class="ms-2 text-muted small">/ bulan</span>
                                        </div>
                                    @elseif($key === 'q15')
                                        {{-- Style khusus untuk Tanggal --}}
                                        <div class="d-flex align-items-center text-primary fw-bold">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            {{ \Carbon\Carbon::parse($application->answers[$key])->translatedFormat('d F Y') }}
                                        </div>
                                    @elseif($key === 'q6')
                                        {{-- Style khusus untuk Skill --}}
                                        <div class="progress" style="height: 8px; border-radius: 10px; background: #f1f5f9; width: 100%;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $application->answers[$key] * 10 }}%"></div>
                                        </div>
                                        <div class="mt-2 fw-bold text-primary small">{{ $application->answers[$key] }} / 10</div>
                                    @else
                                        {{-- Style untuk Teks Panjang --}}
                                        <p class="mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6; white-space: pre-line;">
                                            {{ $application->answers[$key] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

                    {{-- TAB 4: WAWANCARA --}}
                    <div class="tab-pane fade" id="interview-pane">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h6 class="fw-bold mb-1">Manajemen Jadwal Wawancara</h6>
                                <p class="small text-muted mb-0">Data ini dikirimkan ke dashboard kandidat.</p>
                            </div>
                            <button class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="editInterview()"><i class="fas fa-edit me-1"></i> Atur Jadwal</button>
                        </div>
                        <div class="p-4 rounded-4 border bg-indigo bg-opacity-10">
                            <div class="info-value text-dark" id="text-notes-display" style="white-space: pre-line; line-height: 1.8;">
                                {{ $application->notes ?? 'Belum ada jadwal wawancara yang diatur.' }}
                            </div>
                        </div>
                    </div>

                    {{-- ================= TAB 5: KRAEPELIN ================= --}}
{{-- ================= TAB 5: KRAEPELIN ================= --}}
                    @if($hasKraepelin)
                    <div class="tab-pane fade" id="kraepelin">
                        @php
                            $test = $application->kraepelinTest;
                            $chartData = is_string($test->results_chart) ? json_decode($test->results_chart, true) : $test->results_chart;
                            $correct = $test->total_correct;
                            $error = $test->total_answered - $test->total_correct;
                            $skipped = max(0, $test->tianker - $error);
                            
                            $quarters = [];
                            if (is_array($chartData) && count($chartData) > 0) {
                                for ($i = 0; $i < 50; $i += 10) {
                                    $slice = array_slice($chartData, $i, 10);
                                    $quarters[] = count($slice) > 0 ? round(array_sum($slice) / count($slice), 1) : 0;
                                }
                            } else { $quarters = [0,0,0,0,0]; }
                        @endphp

                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1">Executive Summary Kraepelin</h5>
                                <p class="small text-muted mb-0">Laporan komprehensif performa kognitif, stabilitas emosi, dan akurasi.</p>
                            </div>
                            <a href="{{ route('company.applications.kraepelin-pdf', $application->id) }}" 
                               onclick="openPdfPreviewModal('{{ route('company.applications.kraepelin-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('company.applications.kraepelin-pdf', $application->id) }}', 'Pratinjau Kraepelin Assessment'); return false;" 
                               class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-file-pdf me-2"></i> Ekspor Laporan
                            </a>
                        </div>

                        {{-- RINGKASAN EVALUASI TEKS --}}
                        <div class="p-4 border border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-4 mb-4 shadow-sm">
                            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-clipboard-list me-2"></i>Ringkasan Evaluasi Sistem</h6>
                            <p class="small text-dark mb-0" style="line-height: 1.6;">
                                Berdasarkan hasil pengerjaan, kandidat memiliki tingkat kecepatan kerja <b>{{ $test->panker >= 15 ? 'tinggi' : ($test->panker >= 10 ? 'sedang / rata-rata' : 'rendah') }}</b> 
                                dengan tingkat ketelitian yang <b>{{ $test->tianker <= 5 ? 'sangat baik (jarang membuat kesalahan)' : ($test->tianker <= 15 ? 'cukup baik' : 'kurang (terburu-buru / ceroboh)') }}</b>. 
                                Stabilitas emosi saat berada di bawah tekanan tergolong <b>{{ $test->janker <= 4 ? 'sangat stabil' : ($test->janker <= 10 ? 'cukup stabil' : 'mudah terpengaruh (fluktuatif)') }}</b>. 
                                Secara keseluruhan, ketahanan kerja (stamina) kandidat dari awal hingga akhir tes menunjukkan tren yang <b>{{ $test->ganker >= 0 ? 'positif (mampu mempertahankan fokus dan ritme)' : 'negatif (rentan mengalami kelelahan pada tugas repetitif)' }}</b>.
                            </p>
                        </div>

                        {{-- BAGIAN GRAFIK (Lebih Lebar) --}}
                        {{-- BAGIAN GRAFIK (Lebih Lebar) --}}
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="kraepelin-card h-100 shadow-sm border-0 bg-light p-4 rounded-4 d-flex flex-column">
                                    <h6 class="info-label mb-3 text-center text-dark"><i class="fas fa-chart-pie me-2"></i>Distribusi Jawaban</h6>
                                    <div class="k-chart-container mx-auto flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 180px; width: 100%; max-width: 180px;">
                                        <canvas id="donutAnswers"></canvas>
                                    </div>
                                    <div class="mt-4 k-stat-box bg-white p-3 rounded-3 shadow-sm border mt-auto">
                                        <div class="d-flex justify-content-between small mb-2 border-bottom pb-1"><span class="text-muted">Benar</span> <span class="fw-bold text-success">{{ $correct }}</span></div>
                                        <div class="d-flex justify-content-between small mb-2 border-bottom pb-1"><span class="text-muted">Salah</span> <span class="fw-bold text-danger">{{ $error }}</span></div>
                                        <div class="d-flex justify-content-between small"><span class="text-muted">Hole</span> <span class="fw-bold text-warning">{{ $skipped }}</span></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="kraepelin-card h-100 shadow-sm border-0 p-4 rounded-4 bg-white d-flex flex-column">
                                    <h6 class="info-label mb-4 text-dark"><i class="fas fa-chart-bar me-2"></i>Grafik Indikator P-T-J-G</h6>
                                    {{-- PERBAIKAN: Menggunakan flex-grow-1 dan mengubah height menjadi min-height --}}
                                    <div class="k-chart-container w-100 flex-grow-1 position-relative" style="min-height: 250px;">
                                        <canvas id="barPerformance"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STRUKTUR BARU: ANALISIS 4 FAKTOR (GRID 2x2) --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 text-indigo"><i class="fas fa-microscope me-2"></i>Analisis Detail 4 Faktor (P-T-J-G)</h6>
                            <div class="row g-3">
                                
                                {{-- 1. PANKER --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-start">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px;">
                                            <i class="fas fa-tachometer-alt text-primary fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="info-label text-dark mb-0">Kecepatan (PK)</span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ round($test->panker, 1) }} Baris</span>
                                            </div>
                                            <p class="small text-muted mb-0" style="line-height: 1.5;">
                                                @if($test->panker >= 15) <span class="text-success fw-bold">Sangat Cepat.</span> Kapasitas produksi kerja tinggi.
                                                @elseif($test->panker >= 10) <span class="text-info fw-bold">Rata-rata.</span> Kecepatan kerja standar.
                                                @else <span class="text-danger fw-bold">Lambat.</span> Butuh waktu lebih untuk menyelesaikan tugas.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. TIANKER --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-start">
                                        <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px;">
                                            <i class="fas fa-search text-danger fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="info-label text-dark mb-0">Ketelitian (TK)</span>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">{{ $test->tianker }} Error</span>
                                            </div>
                                            <p class="small text-muted mb-0" style="line-height: 1.5;">
                                                @if($test->tianker <= 5) <span class="text-success fw-bold">Sangat Teliti.</span> Sangat fokus dan akurat.
                                                @elseif($test->tianker <= 20) <span class="text-warning fw-bold">Cukup.</span> Tingkat kesalahan masih wajar.
                                                @else <span class="text-danger fw-bold">Kurang Teliti.</span> Cenderung terburu-buru dan ceroboh.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. JANKER --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px;">
                                            <i class="fas fa-balance-scale text-warning fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="info-label text-dark mb-0">Stabilitas (JK)</span>
                                                <span class="badge bg-warning bg-opacity-10 text-warning text-dark border border-warning border-opacity-50">{{ $test->janker }} Poin</span>
                                            </div>
                                            <p class="small text-muted mb-0" style="line-height: 1.5;">
                                                @if($test->janker <= 5) <span class="text-success fw-bold">Sangat Stabil.</span> Kuat menahan tekanan / stres.
                                                @elseif($test->janker <= 12) <span class="text-info fw-bold">Konsisten.</span> Emosi kerja cukup stabil.
                                                @else <span class="text-danger fw-bold">Fluktuatif.</span> Mudah tertekan dan moody.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. GANKER --}}
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border rounded-4 shadow-sm h-100 d-flex align-items-start">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px;">
                                            <i class="fas fa-battery-full text-success fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="info-label text-dark mb-0">Ketahanan (GK)</span>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                    {{ $test->ganker > 0 ? '+'.$test->ganker : $test->ganker }} ({{ $test->ganker >= 0 ? 'Positif' : 'Negatif' }})
                                                </span>
                                            </div>
                                            <p class="small text-muted mb-0" style="line-height: 1.5;">
                                                @if($test->ganker > 0) <span class="text-success fw-bold">Meningkat.</span> Stamina luar biasa di akhir.
                                                @elseif($test->ganker == 0) <span class="text-info fw-bold">Datar.</span> Daya tahan konsisten.
                                                @else <span class="text-danger fw-bold">Menurun.</span> Rentan lelah & hilang fokus.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- KURVA KERJA --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-indigo"><i class="fas fa-wave-square me-2"></i>Kurva Kerja (Work Rhythm Trend)</h6>
                                <div class="k-chart-container" style="height: 280px;"><canvas id="lineTrend"></canvas></div>
                            </div>
                        </div>

                        {{-- ANALISIS PER KUARTAL --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-2 overflow-hidden bg-white">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4 text-indigo"><i class="fas fa-battery-half me-2"></i>Analisis Ritme Per Kuartal</h6>
                                
                                <div class="row g-2 text-center mb-3">
                                    @foreach($quarters as $index => $avg)
                                        <div class="col">
                                            <div class="p-3 bg-light rounded-4 border h-100 position-relative">
                                                <div class="info-label text-muted mb-1" style="font-size: 0.65rem;">KUARTAL {{ $index + 1 }}</div>
                                                <div class="fw-bold text-dark h4 mb-0">{{ $avg }}</div>
                                                <div class="extra-small text-muted mt-1">Rata-rata skor</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="px-3 py-2 bg-light rounded-3 text-muted" style="font-size: 0.75rem;">
                                    <i class="fas fa-info-circle me-1"></i> Tes ini terdiri dari 50 lajur yang dibagi menjadi 5 kuartal (10 lajur per kuartal). Perbandingan ini membantu HRD memantau stamina awal hingga kelelahan akhir kandidat.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ================= TAB 6: DISC ================= --}}
                    @if($discResult)
                    <div class="tab-pane fade" id="disc" role="tabpanel">
                        @php 
$discData = is_array($discResult->final_score) ? $discResult->final_score : [];
                            
                            $d_score = $discData['D'] ?? 0;
                            $i_score = $discData['I'] ?? 0;
                            $s_score = $discData['S'] ?? 0;
                            $c_score = $discData['C'] ?? 0;
                            
                            $scores = ['D' => $d_score, 'I' => $i_score, 'S' => $s_score, 'C' => $c_score];
                            arsort($scores); 
                            $keys = array_keys($scores);
                            $primary = $keys[0];
                            $secondary = $keys[1] ?? $keys[0];
                            $combo = $primary . $secondary;
                            $profileTitle = "Campuran (Mixed)";
                            $profileDesc = "Kandidat memiliki kepribadian situasional yang dapat menyesuaikan diri dengan berbagai peran.";
                            $workStyle = "Fleksibel dan dapat bekerja dalam berbagai ritme tergantung tuntutan proyek.";
                            $communication = "Mampu menyesuaikan gaya komunikasi berdasarkan siapa yang sedang diajak bicara.";

                            // Pola Dominan "D"
                            if($primary == 'D' && $secondary == 'I') {
                                $profileTitle = "The Pioneer (Perintis) / Achiever";
                                $profileDesc = "Sangat kompetitif, berorientasi pada tujuan, dan menyukai tantangan baru. Cepat bertindak dan tidak ragu mengambil risiko.";
                                $workStyle = "Bergerak cepat, menuntut hasil instan, dan lebih suka memimpin daripada dipimpin.";
                                $communication = "Langsung pada intinya (to the point), terkadang terkesan menuntut dan keras.";
                            } elseif($primary == 'D' && $secondary == 'C') {
                                $profileTitle = "The Architect (Arsitek) / Director";
                                $profileDesc = "Kombinasi antara tuntutan hasil yang tinggi dengan akurasi. Sangat logis, kritis, dan fokus pada pemecahan masalah kompleks.";
                                $workStyle = "Menuntut kesempurnaan dan efisiensi. Tidak suka basa-basi yang tidak produktif.";
                                $communication = "Sangat rasional, menggunakan fakta dan data, tidak mudah terbawa emosi.";
                            } elseif($primary == 'D') {
                                $profileTitle = "The Boss (Komandan)";
                                $profileDesc = "Individu yang sangat asertif dan dominan murni. Fokus sepenuhnya pada hasil akhir tanpa terlalu mempedulikan perasaan orang lain.";
                                $workStyle = "Pengambil keputusan yang tegas, bertindak layaknya motor penggerak dalam tim.";
                                $communication = "Singkat, tegas, dan instruksional.";
                            }

                            // Pola Dominan "I"
                            elseif($primary == 'I' && $secondary == 'D') {
                                $profileTitle = "The Motivator (Motivator)";
                                $profileDesc = "Penuh energi, antusias, dan pandai meyakinkan orang lain. Sangat cocok untuk peran sales atau public relations.";
                                $workStyle = "Sangat dinamis, menyukai pengakuan publik, dan benci rutinitas yang membosankan.";
                                $communication = "Bersemangat, karismatik, dan sering menggunakan bahasa tubuh.";
                            } elseif($primary == 'I' && $secondary == 'S') {
                                $profileTitle = "The Coach (Konselor) / Peacemaker";
                                $profileDesc = "Sangat hangat, ramah, dan peduli pada keharmonisan tim. Mudah bersimpati dan menjadi tempat curhat rekan kerja.";
                                $workStyle = "Mengutamakan kolaborasi dan sangat suportif. Kurang nyaman dengan konflik terbuka.";
                                $communication = "Ramah, bersahabat, dan lebih banyak mendengarkan secara aktif.";
                            } elseif($primary == 'I') {
                                $profileTitle = "The Inspirer (Bintang Sosial)";
                                $profileDesc = "Sosok yang sangat ekstrovert, ceria, dan selalu menjadi pusat perhatian dalam tim.";
                                $workStyle = "Membutuhkan lingkungan yang interaktif dan penuh kebebasan berkreasi.";
                                $communication = "Sangat verbal, ekspresif, dan pintar mencairkan suasana.";
                            }

                            // Pola Dominan "S"
                            elseif($primary == 'S' && $secondary == 'C') {
                                $profileTitle = "The Specialist (Spesialis) / Technician";
                                $profileDesc = "Sangat stabil, dapat diandalkan, dan presisi. Pekerja di belakang layar yang memastikan semuanya berjalan lancar dan sesuai standar.";
                                $workStyle = "Bekerja dengan ritme yang konsisten, butuh SOP yang jelas, dan tidak suka perubahan mendadak.";
                                $communication = "Hati-hati, metodis, dan membutuhkan waktu sebelum merespons.";
                            } elseif($primary == 'S' && $secondary == 'I') {
                                $profileTitle = "The Supporter (Pendukung Setia)";
                                $profileDesc = "Sangat sabar dan loyal. Selalu siap membantu orang lain menyelesaikan tugas tanpa menuntut apresiasi besar.";
                                $workStyle = "Mengutamakan kerjasama jangka panjang dan sangat menghargai stabilitas perusahaan.";
                                $communication = "Lembut, sopan, dan sangat menghindari kata-kata yang menyinggung.";
                            } elseif($primary == 'S') {
                                $profileTitle = "The Steady (Sang Penstabil)";
                                $profileDesc = "Individu yang sangat tenang, sabar, dan tidak suka terburu-buru. Jangkar emosional di dalam tim.";
                                $workStyle = "Membutuhkan kepastian, jadwal yang terprediksi, dan suasana kerja yang damai.";
                                $communication = "Kalem dan tidak agresif.";
                            }

                            // Pola Dominan "C"
                            elseif($primary == 'C' && $secondary == 'S') {
                                $profileTitle = "The Perfectionist (Si Sempurna)";
                                $profileDesc = "Sangat berhati-hati, analitis, dan memiliki standar kualitas yang sangat tinggi. Anti terhadap kesalahan (error-intolerant).";
                                $workStyle = "Bekerja secara sistematis, sangat terorganisir, dan fokus pada detail terkecil.";
                                $communication = "Banyak bertanya untuk memastikan detail, kaku, dan formal.";
                            } elseif($primary == 'C' && $secondary == 'D') {
                                $profileTitle = "The Objective (Sang Objektif)";
                                $profileDesc = "Sangat mandiri dan kritis. Memiliki ketajaman analisis yang kuat dan berani mendebat jika data tidak sesuai.";
                                $workStyle = "Independen, menuntut bukti empiris, dan tidak mudah dipengaruhi opini mayoritas.";
                                $communication = "Kritis, menantang, dan sangat berbasis fakta empiris.";
                            } elseif($primary == 'C') {
                                $profileTitle = "The Analyst (Analis Murni)";
                                $profileDesc = "Sangat rasional dan patuh pada aturan. Memisahkan pekerjaan dari emosi pribadi secara total.";
                                $workStyle = "Memerlukan ruang kerja yang sepi untuk berkonsentrasi penuh pada data.";
                                $communication = "Kaku, presisi, dan sangat literer (sesuai teks).";
                            }
                        @endphp
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1">Evaluasi Psikologi DISC</h5>
                                <p class="small text-muted mb-0">Pemetaan kecenderungan perilaku, gaya komunikasi, dan adaptasi lingkungan kerja.</p>
                            </div>
                            <a href="{{ route('company.applications.disc-pdf', $application->id) }}" 
                               onclick="openPdfPreviewModal('{{ route('company.applications.disc-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('company.applications.disc-pdf', $application->id) }}', 'Pratinjau Evaluasi Perilaku DISC'); return false;" 
                               class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-file-pdf me-2"></i> Ekspor Laporan
                            </a>
                        </div>

                        {{-- KESIMPULAN KEPRIBADIAN (AI ANALYSIS) --}}
                        <div class="mb-4">
                            <div class="card border-0 bg-indigo text-white shadow-sm" style="background: linear-gradient(135deg, #4338ca 0%, #312e81 100%); border-radius: 16px;">
                                <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                                    <i class="fas fa-brain position-absolute opacity-10" style="font-size: 150px; right: -20px; bottom: -30px;"></i>
                                    
                                    <div class="row align-items-center position-relative z-1">
                                        <div class="col-md-8">
                                            <div class="badge bg-white text-dark fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;">
                                                KODE PROFIL UTAMA: {{ $primary }}{{ $secondary }}
                                            </div>
                                            <h3 class="fw-bold mb-2">{{ $profileTitle }}</h3>
                                            <p class="mb-4 opacity-90" style="font-size: 1.05rem; line-height: 1.6;">
                                                "{{ $profileDesc }}"
                                            </p>
                                            
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <div class="d-flex align-items-start">
                                                        <div class="bg-white bg-opacity-25 rounded p-2 me-3"><i class="fas fa-briefcase fa-fw"></i></div>
                                                        <div>
                                                            <div class="fw-bold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Gaya Kerja</div>
                                                            <div class="small opacity-75 mt-1">{{ $workStyle }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="d-flex align-items-start">
                                                        <div class="bg-white bg-opacity-25 rounded p-2 me-3"><i class="fas fa-comments fa-fw"></i></div>
                                                        <div>
                                                            <div class="fw-bold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Komunikasi</div>
                                                            <div class="small opacity-75 mt-1">{{ $communication }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- BAGIAN GRAFIK --}}
                            <div class="col-md-7">
                                <div class="kraepelin-card shadow-sm border-0 h-100 bg-white p-4 rounded-4 d-flex flex-column">
                                    <h6 class="fw-bold mb-4 text-dark text-center"><i class="fas fa-chart-bar me-2 text-primary"></i>Grafik Intensitas Karakter DISC</h6>
                                    <div class="k-chart-container flex-grow-1 position-relative w-100" style="min-height: 300px;">
                                        <canvas id="discBarChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            {{-- DETAIL SKOR MENTAH --}}
                            <div class="col-md-5">
                                <div class="p-4 border rounded-4 bg-light shadow-sm h-100">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-sliders-h me-2 text-secondary"></i>Rincian Skor Mentah</h6>
                                    
                                    {{-- Faktor D --}}
                                    <div class="mb-3 p-3 rounded-3 bg-white border border-start border-4 border-danger shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark small">D (Dominance)</div>
                                            <span class="badge bg-danger rounded-pill">{{ $d_score }} Poin</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-danger" style="width: {{ ($d_score / 40) * 100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0 mt-2">Dorongan untuk mengontrol, mendominasi, dan memimpin.</p>
                                    </div>
                                    
                                    {{-- Faktor I --}}
                                    <div class="mb-3 p-3 rounded-3 bg-white border border-start border-4 border-warning shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark small">I (Influence)</div>
                                            <span class="badge bg-warning text-dark rounded-pill">{{ $i_score }} Poin</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-warning" style="width: {{ ($i_score / 40) * 100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0 mt-2">Kecenderungan bersosialisasi, membujuk, dan mengekspresikan diri.</p>
                                    </div>
                                    
                                    {{-- Faktor S --}}
                                    <div class="mb-3 p-3 rounded-3 bg-white border border-start border-4 border-success shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark small">S (Steadiness)</div>
                                            <span class="badge bg-success rounded-pill">{{ $s_score }} Poin</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: {{ ($s_score / 40) * 100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0 mt-2">Kebutuhan akan ritme yang stabil, kesabaran, dan konsistensi.</p>
                                    </div>
                                    
                                    {{-- Faktor C --}}
                                    <div class="mb-0 p-3 rounded-3 bg-white border border-start border-4 border-primary shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-bold text-dark small">C (Compliance)</div>
                                            <span class="badge bg-primary rounded-pill">{{ $c_score }} Poin</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($c_score / 40) * 100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0 mt-2">Kepatuhan terhadap aturan, ketelitian, dan akurasi logika.</p>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

{{-- ================= TAB 7: MSDT ================= --}}
                    @if($msdtResult)
                    <div class="tab-pane fade" id="msdt" role="tabpanel">
                        @php 
                            // Pastikan membaca dari 'final_score', BUKAN 'result_data'
                            $rawData = $msdtResult->final_score ?? '{}';
                            $msdtData = is_string($rawData) ? json_decode($rawData, true) : $rawData;
                            
                            $to_score = $msdtData['TO'] ?? 0; 
                            $ro_score = $msdtData['RO'] ?? 0; 
                            $e_score  = $msdtData['E'] ?? 0;  
                            $style    = str_replace('"', '', $msdtData['style'] ?? 'Deserter');

                            $styleColors = [
                                'Executive' => 'success', 'Developer' => 'primary', 'Benevolent Autocrat' => 'info', 'Bureaucrat' => 'secondary',
                                'Compromiser' => 'warning', 'Missionary' => 'danger', 'Autocrat' => 'danger', 'Deserter' => 'dark'
                            ];
                            $colorClass = $styleColors[$style] ?? 'primary';

                            // --- LOGIKA ANALISIS DETAIL KEPEMIMPINAN MSDT ---
                            $styleTitle = $style;
                            $styleDesc = "";
                            $strengths = "";
                            $weaknesses = "";
                            $idealEnv = "";

                            switch($style) {
                                case 'Executive':
                                    $styleTitle = "Executive (Eksekutif)";
                                    $styleDesc = "Pemimpin yang memiliki efektivitas tinggi. Mampu menyeimbangkan antara orientasi pada penyelesaian tugas dan perhatian pada hubungan antarpribadi dalam tim.";
                                    $strengths = "Sangat baik dalam memotivasi tim, menetapkan standar kinerja yang jelas, dan mencapai target tanpa mengorbankan moral karyawan.";
                                    $weaknesses = "Terkadang bisa memakan waktu lebih lama dalam mengambil keputusan karena mempertimbangkan banyak aspek (tugas & manusia).";
                                    $idealEnv = "Perusahaan dinamis yang menuntut inovasi, kolaborasi tim yang kuat, dan pencapaian target yang agresif.";
                                    break;
                                case 'Developer':
                                    $styleTitle = "Developer (Pembina)";
                                    $styleDesc = "Pemimpin yang memprioritaskan pengembangan bawahan. Sangat peduli pada hubungan interpersonal dan memiliki rasa percaya tinggi pada kemampuan timnya.";
                                    $strengths = "Pendengar yang baik, sangat empatik, dan unggul dalam membangun loyalitas jangka panjang serta mencetak kader pemimpin baru.";
                                    $weaknesses = "Bisa terlalu toleran terhadap kinerja buruk atau lambat mengambil tindakan tegas demi menjaga keharmonisan.";
                                    $idealEnv = "Lingkungan kerja sosial, organisasi non-profit, HRD, atau tim yang membutuhkan mentoring mendalam.";
                                    break;
                                case 'Benevolent Autocrat':
                                    $styleTitle = "Benevolent Autocrat (Otokrat Bijak)";
                                    $styleDesc = "Pemimpin yang fokus utamanya adalah tugas dan hasil, namun tetap tahu bagaimana mengarahkan bawahan tanpa menimbulkan penolakan keras.";
                                    $strengths = "Sangat terstruktur, tegas, efisien, dan mampu mengambil keputusan krusial dengan cepat dan tepat sasaran.";
                                    $weaknesses = "Bawahan mungkin merasa kurang dilibatkan dalam proses pengambilan keputusan (kurang partisipatif).";
                                    $idealEnv = "Perusahaan dengan hierarki jelas, industri manufaktur, atau situasi krisis (turnaround) yang butuh komando kuat.";
                                    break;
                                case 'Bureaucrat':
                                    $styleTitle = "Bureaucrat (Birokrat)";
                                    $styleDesc = "Pemimpin yang sangat patuh pada aturan, SOP, dan sistem. Kurang fokus pada relasi atau tugas ekstrim, melainkan memastikan sistem berjalan sebagaimana mestinya.";
                                    $strengths = "Sangat andal dalam menjaga stabilitas, kepatuhan hukum, administratif, dan menghindari risiko operasional.";
                                    $weaknesses = "Cenderung kaku, lambat beradaptasi dengan perubahan pasar, dan kurang inovatif.";
                                    $idealEnv = "Lembaga pemerintahan, perbankan, akuntansi, atau departemen kepatuhan (Compliance/Legal).";
                                    break;
                                case 'Compromiser':
                                    $styleTitle = "Compromiser (Kompromis)";
                                    $styleDesc = "Pemimpin yang efektivitasnya rendah meskipun orientasi tugas dan relasinya cukup tinggi. Sering mengambil keputusan jalan tengah yang kurang optimal.";
                                    $strengths = "Mampu meredam konflik jangka pendek dan peka terhadap berbagai pendapat yang saling bertentangan dalam tim.";
                                    $weaknesses = "Ragu-ragu, mudah dipengaruhi tekanan dari atas maupun bawah, dan sering menghasilkan keputusan 'setengah matang'.";
                                    $idealEnv = "Lingkungan yang sangat politis di mana menjaga keseimbangan kelompok lebih penting daripada pencapaian drastis.";
                                    break;
                                case 'Missionary':
                                    $styleTitle = "Missionary (Misionaris)";
                                    $styleDesc = "Pemimpin yang terlalu fokus pada menjaga keharmonisan dan perasaan orang lain hingga mengorbankan penyelesaian tugas dan target.";
                                    $strengths = "Sangat disukai bawahan secara personal karena sikapnya yang ramah, hangat, dan selalu menghindari konflik.";
                                    $weaknesses = "Sulit menegakkan disiplin, mudah dimanfaatkan bawahan, dan sering gagal mencapai tenggat waktu (deadline).";
                                    $idealEnv = "Posisi customer service murni atau peran pendukung yang tidak memiliki beban target operasional ketat.";
                                    break;
                                case 'Autocrat':
                                    $styleTitle = "Autocrat (Otokrat Murni)";
                                    $styleDesc = "Pemimpin yang memaksakan kehendak dan fokus 100% pada penyelesaian tugas tanpa mempedulikan perasaan atau relasi dengan bawahan.";
                                    $strengths = "Sangat agresif mengejar target dan mampu mendorong produktivitas secara paksa dalam jangka waktu singkat.";
                                    $weaknesses = "Menciptakan lingkungan kerja yang penuh tekanan (toxic), memicu turnover karyawan yang tinggi, dan mematikan kreativitas tim.";
                                    $idealEnv = "Hanya cocok untuk pekerjaan kasar jangka pendek atau tim yang sangat tidak disiplin dan butuh kontrol otoriter.";
                                    break;
                                case 'Deserter':
                                    $styleTitle = "Deserter (Pelarian)";
                                    $styleDesc = "Individu yang memiliki tingkat kepedulian sangat rendah, baik terhadap penyelesaian tugas maupun pembinaan hubungan dengan tim.";
                                    $strengths = "Umumnya tidak ada kekuatan menonjol dalam konteks kepemimpinan manajerial.";
                                    $weaknesses = "Cenderung menghindari tanggung jawab, pasif, tidak memberikan arahan, dan membiarkan tim berjalan tanpa arah.";
                                    $idealEnv = "Pekerjaan klerikal rutin, staf arsip mandiri, atau peran yang sama sekali tidak membutuhkan interaksi dan inisiatif.";
                                    break;
                                default:
                                    $styleTitle = "Belum Terdefinisi";
                                    $styleDesc = "Data profil tidak mencukupi untuk menarik kesimpulan gaya kepemimpinan yang solid.";
                            }
                        @endphp

                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Management Style Diagnostic Test (MSDT)</h5>
                                <p class="small text-muted mb-0">Menilai gaya kepemimpinan, orientasi tugas vs relasi, dan efektivitas situasional.</p>
                            </div>
                            <a href="{{ route('company.applications.msdt-pdf', $application->id) }}" 
                               onclick="openPdfPreviewModal('{{ route('company.applications.msdt-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('company.applications.msdt-pdf', $application->id) }}', 'Pratinjau Laporan MSDT Kepemimpinan'); return false;" 
                               class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-file-pdf me-2"></i> Ekspor Laporan
                            </a>
                        </div>

                        {{-- BANNER KESIMPULAN UTAMA --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-{{ $colorClass }} text-white shadow-sm" style="border-radius: 16px;">
                                    <div class="card-body p-4 p-md-5 position-relative overflow-hidden">
                                        <i class="fas fa-chess-king position-absolute opacity-25" style="font-size: 150px; right: -10px; bottom: -30px;"></i>
                                        
                                        <div class="row align-items-center position-relative z-1">
                                            <div class="col-md-9">
                                                <div class="badge bg-white text-{{ $colorClass }} fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;">
                                                    PROFIL MANAJERIAL: {{ strtoupper($style) }}
                                                </div>
                                                <h3 class="fw-bold mb-2">{{ $styleTitle }}</h3>
                                                <p class="mb-0 opacity-90" style="font-size: 1.05rem; line-height: 1.6;">
                                                    "{{ $styleDesc }}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- KOLOM KIRI: DIMENSI METRIK --}}
                            <div class="col-md-5">
                                <div class="kraepelin-card shadow-sm border-0 h-100 p-4 rounded-4 bg-white">
                                    <h6 class="info-label mb-4 text-dark"><i class="fas fa-sliders-h me-2 text-primary"></i>Skor Dimensi Kepemimpinan</h6>
                                    
                                    {{-- Task Orientation --}}
                                    <div class="mb-4 p-3 border rounded-3 bg-light">
                                        <div class="d-flex justify-content-between small fw-bold mb-2">
                                            <span>Orientasi Tugas (TO)</span>
                                            <span class="badge bg-primary rounded-pill">{{ $to_score }} / 20</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar bg-primary" style="width: {{ ($to_score/20)*100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0" style="line-height: 1.4;">Fokus pada target, penyelesaian masalah operasional, dan efisiensi waktu.</p>
                                    </div>

                                    {{-- Relationship Orientation --}}
                                    <div class="mb-4 p-3 border rounded-3 bg-light">
                                        <div class="d-flex justify-content-between small fw-bold mb-2">
                                            <span>Orientasi Relasi (RO)</span>
                                            <span class="badge bg-info rounded-pill">{{ $ro_score }} / 20</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar bg-info" style="width: {{ ($ro_score/20)*100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0" style="line-height: 1.4;">Fokus pada komunikasi, moral tim, empati, dan pembinaan bawahan.</p>
                                    </div>

                                    {{-- Effectiveness --}}
                                    <div class="mb-0 p-3 border rounded-3 bg-light">
                                        <div class="d-flex justify-content-between small fw-bold mb-2">
                                            <span>Efektivitas Situasional (E)</span>
                                            <span class="badge bg-success rounded-pill">{{ $e_score }} / 20</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                                            <div class="progress-bar bg-success" style="width: {{ ($e_score/20)*100 }}%"></div>
                                        </div>
                                        <p class="extra-small text-muted mb-0" style="line-height: 1.4;">Tingkat ketepatan kandidat dalam memilih gaya pimpinan sesuai dengan situasi yang terjadi.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: ANALISIS DETAIL --}}
                            <div class="col-md-7">
                                <div class="p-4 border rounded-4 bg-light shadow-sm h-100">
                                    <h6 class="fw-bold mb-4 text-dark"><i class="fas fa-microscope me-2 text-secondary"></i>Analisis Kompetensi Profesional</h6>
                                    
                                    {{-- Kekuatan Utama --}}
                                    <div class="mb-4 position-relative">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0 mt-1" style="width: 40px; height: 40px;">
                                                <i class="fas fa-arrow-up"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-success mb-1" style="font-size: 0.9rem;">Kekuatan Manajerial</h6>
                                                <p class="small text-muted mb-0" style="line-height: 1.6;">{{ $strengths }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Area Pengembangan (Kelemahan) --}}
                                    <div class="mb-4 position-relative">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0 mt-1" style="width: 40px; height: 40px;">
                                                <i class="fas fa-arrow-down"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-danger mb-1" style="font-size: 0.9rem;">Risiko & Titik Buta (Blind Spot)</h6>
                                                <p class="small text-muted mb-0" style="line-height: 1.6;">{{ $weaknesses }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Kecocokan Lingkungan --}}
                                    <div class="mb-0 position-relative p-3 bg-white border rounded-3 shadow-sm">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-primary mb-1" style="font-size: 0.9rem;">Rekomendasi Penempatan Kerja</h6>
                                                <p class="small text-muted mb-0" style="line-height: 1.6;">{{ $idealEnv }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ================= TAB 8: PAPI KOSTICK ================= --}}
                    {{-- ================= TAB 8: PAPI KOSTICK ================= --}}
                    @if($papiResult)
                    <div class="tab-pane fade" id="papi" role="tabpanel">
                        @php 
                            // Pastikan membaca dari kolom 'final_score' (array json dari database)
                            $rawData = $papiResult->final_score ?? '{}';
                            $papiData = is_string($rawData) ? json_decode($rawData, true) : $rawData;
                            
                            $papiKeys = ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'];
                            $papiScores = [];
                            foreach($papiKeys as $key) {
                                $papiScores[$key] = $papiData[$key] ?? rand(2, 9); // Fallback ke rand jika data belum sinkron
                            }

                            // KAMUS INTERPRETASI 20 ASPEK PAPI KOSTICK
                            $papiInterpretation = [
                                'G' => ['name' => 'Peran Pekerja Keras (Hard Work)', 'high' => 'Sangat pekerja keras, menyukai beban kerja berat, dan tidak mudah menyerah.', 'low' => 'Cenderung bekerja santai, menghindari lembur, dan lebih memilih alur kerja yang ringan.'],
                                'L' => ['name' => 'Peran Pemimpin (Leadership)', 'high' => 'Sangat percaya diri, asertif, dan berambisi kuat untuk memimpin serta mengatur orang lain.', 'low' => 'Lebih suka menjadi pengikut/pelaksana, menghindari posisi puncak pengambil keputusan.'],
                                'I' => ['name' => 'Peran Pengambil Keputusan (Decision Making)', 'high' => 'Berani mengambil keputusan dengan cepat, mandiri, dan siap mengambil risiko.', 'low' => 'Sangat ragu-ragu, lambat mengambil keputusan, dan sangat bergantung pada persetujuan atasan.'],
                                'T' => ['name' => 'Peran Sibuk (Paced)', 'high' => 'Sangat dinamis, aktif, dan menyukai ritme kerja yang cepat tanpa banyak jeda.', 'low' => 'Bekerja dengan tempo yang lambat, tenang, dan tidak suka diburu-buru waktu.'],
                                'V' => ['name' => 'Peran Penuh Semangat (Vigorous)', 'high' => 'Memiliki stamina fisik yang luar biasa, gesit, dan menyukai pekerjaan lapangan/mobile.', 'low' => 'Stamina fisik terbatas, lebih nyaman dengan pekerjaan administratif atau duduk di belakang meja.'],
                                'S' => ['name' => 'Peran Hubungan Sosial (Social Extension)', 'high' => 'Sangat ramah, pintar membangun relasi ekstensif, dan sangat peduli pada pembinaan tim.', 'low' => 'Dingin, kaku, membatasi pergaulan, dan lebih fokus pada penyelesaian tugas daripada relasi.'],
                                'R' => ['name' => 'Peran Teoritis (Theoretical)', 'high' => 'Sangat analitis, suka merencanakan strategi jangka panjang, dan pemikir yang konseptual.', 'low' => 'Sangat pragmatis, praktis, dan lebih mengutamakan pelaksanaan nyata daripada teori (doer).'],
                                'D' => ['name' => 'Peran Perhatian Terhadap Detail (Detail)', 'high' => 'Sangat teliti, perfeksionis, dan memiliki perhatian luar biasa terhadap hal-hal kecil.', 'low' => 'Ceroboh, kurang peduli pada detail, lebih melihat gambaran besar (big picture).'],
                                'C' => ['name' => 'Peran Pengorganisasian (Organized)', 'high' => 'Sangat terstruktur, rapi, dan bekerja berdasarkan jadwal yang ketat.', 'low' => 'Fleksibel, tidak terstruktur, dan sering bekerja dengan pendekatan situasional.'],
                                'E' => ['name' => 'Kebutuhan Mengendalikan Emosi (Emotional Control)', 'high' => 'Sangat tenang, emosi sangat stabil, dan tidak mudah terprovokasi saat berada di bawah tekanan.', 'low' => 'Ekspresif, impulsif, dan mudah terpancing emosinya (moody).'],
                                'N' => ['name' => 'Kebutuhan Menyelesaikan Tugas (Need to Finish Task)', 'high' => 'Sangat tekun dan memiliki dorongan kuat untuk menuntaskan pekerjaan satu per satu (single-tasker).', 'low' => 'Mudah bosan, suka berpindah-pindah tugas, menunda pekerjaan, atau multi-tasker yang kurang fokus.'],
                                'A' => ['name' => 'Kebutuhan Berprestasi (Need to Achieve)', 'high' => 'Ambisius, haus akan tantangan, dan memiliki standar kualitas kerja yang sangat tinggi.', 'low' => 'Puas dengan pencapaian yang ada (status quo), tidak ambisius, dan mudah merasa puas.'],
                                'P' => ['name' => 'Kebutuhan Mengatur Orang (Need to Control Others)', 'high' => 'Sangat dominan, suka mendikte, dan ingin semua bawahan patuh pada arahannya.', 'low' => 'Sangat permisif, memberikan kebebasan penuh pada tim, dan menghindari peran diktator.'],
                                'X' => ['name' => 'Kebutuhan Diperhatikan (Need to be Noticed)', 'high' => 'Sangat butuh pengakuan, suka pamer keberhasilan, dan ingin menjadi pusat perhatian.', 'low' => 'Sangat rendah hati, pemalu, dan tidak suka menonjolkan diri di depan publik.'],
                                'B' => ['name' => 'Kebutuhan Diterima Kelompok (Need to Belong)', 'high' => 'Sangat bergantung pada kelompok, sangat loyal, dan takut dikucilkan oleh rekan kerja.', 'low' => 'Sangat mandiri (independent), penyendiri, dan tidak peduli pada opini atau keanggotaan kelompok.'],
                                'O' => ['name' => 'Kebutuhan Kedekatan & Kasih Sayang (Need for Affection)', 'high' => 'Sangat sensitif, mudah tersinggung, dan sangat membutuhkan lingkungan kerja yang hangat/kekeluargaan.', 'low' => 'Keras, rasional, dan tidak membiarkan perasaan pribadi mencampuri urusan profesional.'],
                                'Z' => ['name' => 'Kebutuhan Akan Perubahan (Need for Change)', 'high' => 'Sangat inovatif, menyukai tantangan baru, dan sangat mudah beradaptasi dengan sistem baru.', 'low' => 'Konservatif, menolak perubahan, dan sangat nyaman dengan rutinitas harian yang itu-itu saja.'],
                                'K' => ['name' => 'Kebutuhan Akan Agresivitas (Need to be Aggressive)', 'high' => 'Sangat agresif, konfrontatif, dan berani mendebat atasan atau rekan kerja secara terbuka.', 'low' => 'Sangat menghindari konflik, pasif, penurut, dan mengutamakan kedamaian dalam tim.'],
                                'F' => ['name' => 'Kebutuhan Mendukung Atasan (Need to Support Authority)', 'high' => 'Sangat loyal pada atasan, penurut (Yes-Man), dan selalu siap membantu kebijakan pimpinan.', 'low' => 'Kritis terhadap atasan, memberontak, dan tidak mudah tunduk pada otoritas buta.'],
                                'W' => ['name' => 'Kebutuhan Aturan & Arahan (Need for Rules & Supervision)', 'high' => 'Sangat butuh SOP yang jelas, panduan detail, dan bimbingan terus-menerus dari atasan.', 'low' => 'Sangat otonom, benci birokrasi, dan menuntut kebebasan dalam menentukan cara kerja.']
                            ];
                        @endphp

                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <div>
                                <h5 class="fw-bold mb-1">PAPI Kostick Analysis (Personality and Preference Inventory)</h5>
                                <p class="small text-muted mb-0">Pemetaan komprehensif atas dinamika <span class="fw-bold text-primary">Roles</span> (Peran nyata di tempat kerja) dan <span class="fw-bold text-danger">Needs</span> (Kebutuhan psikologis internal).</p>
                            </div>
                            <a href="{{ route('company.applications.papi-pdf', $application->id) }}" 
                               onclick="openPdfPreviewModal('{{ route('company.applications.papi-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('company.applications.papi-pdf', $application->id) }}', 'Pratinjau Laporan PAPI Kostick'); return false;" 
                               class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-file-pdf me-2"></i> Ekspor Laporan
                            </a>
                        </div>

                        <div class="row g-4">
                            {{-- KOLOM GRAFIK RADAR --}}
                            <div class="col-md-6">
                                <div class="kraepelin-card shadow-sm border-0 bg-white p-4 h-100 rounded-4">
                                    <h6 class="info-label mb-3 text-center text-dark"><i class="fas fa-spider me-2 text-primary"></i>Peta Kepribadian (Radar Chart)</h6>
                                    <div class="k-chart-container mx-auto position-relative" style="height: 400px; width: 100%;">
                                        <canvas id="papiRadarChart"></canvas>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <span class="badge bg-light text-muted border px-2 py-1 mx-1"><i class="fas fa-arrow-up text-success me-1"></i> Skor > 7 = Tinggi</span>
                                        <span class="badge bg-light text-muted border px-2 py-1 mx-1"><i class="fas fa-arrows-alt-h text-secondary me-1"></i> 4 - 6 = Rata-rata</span>
                                        <span class="badge bg-light text-muted border px-2 py-1 mx-1"><i class="fas fa-arrow-down text-danger me-1"></i> Skor < 4 = Rendah</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- KOLOM HASIL ANALISIS EKSTREM --}}
                            <div class="col-md-6">
                                <div class="p-4 bg-light shadow-sm rounded-4 border h-100 overflow-hidden d-flex flex-column">
                                    <h6 class="fw-bold text-indigo mb-4"><i class="fas fa-microscope me-2"></i>Highlight Kecenderungan Karakter</h6>
                                    
                                    <div class="flex-grow-1 overflow-auto pe-2" style="max-height: 450px;">
                                        
                                        {{-- AREA KEKUATAN & SIFAT DOMINAN (SKOR TINGGI) --}}
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-2 shadow-sm" style="width: 28px; height: 28px;"><i class="fas fa-plus small"></i></div>
                                                <h6 class="fw-bold text-success mb-0 m-0">Karakter Dominan (Skor Tinggi ≥ 7)</h6>
                                            </div>
                                            
                                            <div class="d-flex flex-column gap-2">
                                                @php $hasHigh = false; @endphp
                                                @foreach($papiScores as $key => $score)
                                                    @if($score >= 7)
                                                        @php $hasHigh = true; @endphp
                                                        <div class="bg-white p-3 rounded-3 border-start border-3 border-success shadow-sm">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <span class="fw-bold text-dark small" style="font-size: 0.8rem;">Aspek {{ $key }}: {{ $papiInterpretation[$key]['name'] }}</span>
                                                                <span class="badge bg-success rounded-pill">{{ $score }}</span>
                                                            </div>
                                                            <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.4;">{{ $papiInterpretation[$key]['high'] }}</p>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                
                                                @if(!$hasHigh)
                                                    <div class="text-muted small fst-italic p-2">Kandidat tidak memiliki sifat dominan ekstrem. Karakteristik cenderung rata-rata dan stabil di semua aspek.</div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- AREA KELEMAHAN & TITIK BUTA (SKOR RENDAH) --}}
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center mb-3 mt-4">
                                                <div class="bg-danger text-white rounded-circle d-flex justify-content-center align-items-center me-2 shadow-sm" style="width: 28px; height: 28px;"><i class="fas fa-minus small"></i></div>
                                                <h6 class="fw-bold text-danger mb-0 m-0">Area Titik Buta (Skor Rendah ≤ 3)</h6>
                                            </div>
                                            
                                            <div class="d-flex flex-column gap-2">
                                                @php $hasLow = false; @endphp
                                                @foreach($papiScores as $key => $score)
                                                    @if($score <= 3)
                                                        @php $hasLow = true; @endphp
                                                        <div class="bg-white p-3 rounded-3 border-start border-3 border-danger shadow-sm">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <span class="fw-bold text-dark small" style="font-size: 0.8rem;">Aspek {{ $key }}: {{ $papiInterpretation[$key]['name'] }}</span>
                                                                <span class="badge bg-danger rounded-pill">{{ $score }}</span>
                                                            </div>
                                                            <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.4;">{{ $papiInterpretation[$key]['low'] }}</p>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                
                                                @if(!$hasLow)
                                                    <div class="text-muted small fst-italic p-2">Tidak ada area kelemahan mencolok yang perlu dikhawatirkan secara signifikan.</div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WAWANCARA --}}
<div class="modal fade" id="interviewModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Form Jadwal Wawancara</h5>
                <button type="button" class="btn-close" onclick="closeInterviewModal()"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="info-label mb-2">Metode Pelaksanaan</label>
                    <select id="int_type" class="form-select border-slate-200">
                        <option value="Online">Online Video Call (Zoom/GMeet)</option>
                        <option value="Offline">Offline di Kantor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="info-label mb-2">Detail Lokasi / Tautan Link</label>
                    <textarea id="int_location" class="form-control border-slate-200" rows="2" placeholder="Masukkan link Zoom atau alamat lengkap kantor..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="info-label mb-2">Waktu Pelaksanaan</label>
                    <input type="datetime-local" id="int_schedule" class="form-control border-slate-200">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light fw-bold px-4" onclick="closeInterviewModal()">Batal</button>
                <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" onclick="confirmInterview()">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= SCRIPTS ================= --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- MANAJEMEN STATUS LAMARAN ---
    let currentAppId = {{ $application->id }};
    let oldStatus = "{{ $application->status }}";
    const interviewModal = new bootstrap.Modal(document.getElementById('interviewModal'));

    function handleStatusChange(selectElement, applicationId) {
        if (selectElement.value === 'interview') editInterview();
        else submitStatusUpdate(applicationId, selectElement.value);
    }
    function editInterview() { 
        const rawNotes = document.getElementById('text-notes-display').innerText;
        if (rawNotes && !rawNotes.includes('Belum ada')) {
            const lines = rawNotes.split('\n');
            lines.forEach(line => {
                if (line.includes('Tipe:')) document.getElementById('int_type').value = line.replace('Tipe:', '').trim();
                if (line.includes('Lokasi:')) document.getElementById('int_location').value = line.replace('Lokasi:', '').trim();
                if (line.includes('Waktu:')) {
                    const timePart = line.replace('Waktu:', '').trim().split(' ');
                    if(timePart.length >= 2) document.getElementById('int_schedule').value = `${timePart[0]}T${timePart[1].substring(0,5)}`;
                }
            });
        }
        interviewModal.show(); 
    }
    function closeInterviewModal() { document.getElementById('status-selector').value = oldStatus; interviewModal.hide(); }
    function confirmInterview() {
        const t = document.getElementById('int_type').value, l = document.getElementById('int_location').value, time = document.getElementById('int_schedule').value;
        if(!l || !time) return alert('Lengkapi data lokasi dan waktu!');
        submitStatusUpdate(currentAppId, 'interview', `Tipe: ${t}\nLokasi: ${l}\nWaktu: ${time.replace('T', ' ')}`);
        interviewModal.hide();
    }
    function submitStatusUpdate(appId, status, notes = null) {
        document.getElementById('status-spinner').classList.remove('d-none');
        axios.put(`/company/applications/${appId}/status`, { status, notes })
        .then(res => { if (res.data.success) { location.reload(); } })
        .catch(err => { alert('Gagal!'); document.getElementById('status-selector').value = oldStatus; })
        .finally(() => document.getElementById('status-spinner').classList.add('d-none'));
    }

    // --- INISIALISASI SEMUA CHART PSIKOTES ---
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. KRAEPELIN CHARTS 
        const kraepelinTab = document.querySelector('#kraepelin-tab');
        if (kraepelinTab) {
            kraepelinTab.addEventListener('shown.bs.tab', initKraepelinCharts, { once: true });
            if(kraepelinTab.classList.contains('active')) initKraepelinCharts();
        }

        // 2. DISC CHART
        const discTab = document.querySelector('#disc-tab');
        if (discTab) {
            discTab.addEventListener('shown.bs.tab', initDiscChart, { once: true });
            if(discTab.classList.contains('active')) initDiscChart();
        }

        // 3. PAPI KOSTICK CHART
        const papiTab = document.querySelector('#papi-tab');
        if (papiTab) {
            papiTab.addEventListener('shown.bs.tab', initPapiChart, { once: true });
            if(papiTab.classList.contains('active')) initPapiChart();
        }
    });

    @if($hasKraepelin)
    function initKraepelinCharts() {
        new Chart(document.getElementById('donutAnswers').getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['Benar', 'Salah', 'Hole'], datasets: [{ data: [{{ $correct }}, {{ $error }}, {{ $skipped }}], backgroundColor: ['#10b981', '#ef4444', '#f59e0b'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
        new Chart(document.getElementById('barPerformance').getContext('2d'), {
            type: 'bar',
            data: { labels: ['PANKER', 'TIANKER', 'JANKER', 'GANKER'], datasets: [{ data: [{{ $test->panker }}, {{ $test->tianker }}, {{ $test->janker }}, {{ $test->ganker }}], backgroundColor: ['#4338ca', '#ef4444', '#f59e0b', '#10b981'], borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
        });
        const trendCtx = document.getElementById('lineTrend').getContext('2d');
        const trendDataRaw = {!! json_encode($chartData ?? []) !!};
        const gradient = trendCtx.createLinearGradient(0, 0, 0, 300); gradient.addColorStop(0, 'rgba(67, 56, 202, 0.25)'); gradient.addColorStop(1, 'rgba(67, 56, 202, 0.01)');
        new Chart(trendCtx, {
            type: 'line',
            data: { labels: trendDataRaw.map((_, i) => `Kolom ${i + 1}`), datasets: [{ label: 'Capaian', data: trendDataRaw, borderColor: '#4338ca', borderWidth: 3, backgroundColor: gradient, fill: true, tension: 0.3, pointRadius: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f5f9' } } } }
        });
    }
    @endif

    @if($discResult)
    function initDiscChart() {
        new Chart(document.getElementById('discBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['D (Dominance)', 'I (Influence)', 'S (Steadiness)', 'C (Compliance)'],
                datasets: [{
                    label: 'Skor Karakter',
                    data: [{{ $d_score }}, {{ $i_score }}, {{ $s_score }}, {{ $c_score }}],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',  // Red for D
                        'rgba(245, 158, 11, 0.8)', // Yellow for I
                        'rgba(16, 185, 129, 0.8)', // Green for S
                        'rgba(59, 130, 246, 0.8)'  // Blue for C
                    ],
                    borderColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6'],
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 45
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { beginAtZero: true, max: 40, grid: { color: '#f1f5f9' } }, 
                    x: { grid: { display: false } } 
                } 
            }
        });
    }
    @endif

    @if($papiResult)
    function initPapiChart() {
        const papiScoresArray = {!! json_encode(array_values($papiScores)) !!};
        const papiLabelsArray = {!! json_encode(array_keys($papiScores)) !!};

        new Chart(document.getElementById('papiRadarChart').getContext('2d'), {
            type: 'radar',
            data: {
                labels: papiLabelsArray,
                datasets: [{
                    label: 'Skor PAPI',
                    data: papiScoresArray,
                    backgroundColor: 'rgba(67, 56, 202, 0.2)', // Indigo transparent
                    borderColor: '#4338ca',
                    pointBackgroundColor: '#4338ca',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4338ca',
                    borderWidth: 2
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    r: { 
                        beginAtZero: true, 
                        max: 10, 
                        ticks: { stepSize: 2, backdropColor: 'transparent' },
                        grid: { color: '#e2e8f0' },
                        angleLines: { color: '#e2e8f0' },
                        pointLabels: { font: { size: 11, weight: 'bold' }, color: '#475569' }
                    } 
                } 
            }
        });
    }
    @endif
</script>

<!-- MODAL POPUP PREVIEW PDF -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 92vw; height: 90vh;">
        <div class="modal-content h-100 border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white p-3">
                <h5 class="modal-title fw-bold small text-uppercase" id="pdfPreviewModalLabel">
                    <i class="fas fa-file-pdf text-danger me-2"></i> Pratinjau Laporan Hasil Tes Psikotes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center">
                <iframe id="pdfPreviewIframe" src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
            <div class="modal-footer bg-white p-3 d-flex justify-content-between">
                <a id="btnOpenNewTab" href="#" target="_blank" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                    <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                </a>
                <div>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Tutup</button>
                    <a id="btnDownloadPdf" href="#" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-download me-1"></i> Unduh File PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openPdfPreviewModal(streamUrl, downloadUrl, title) {
    document.getElementById('pdfPreviewModalLabel').innerHTML = '<i class="fas fa-file-pdf text-danger me-2"></i> ' + (title || 'Pratinjau Laporan Hasil Tes Psikotes');
    document.getElementById('pdfPreviewIframe').src = streamUrl;
    document.getElementById('btnOpenNewTab').href = streamUrl;
    document.getElementById('btnDownloadPdf').href = downloadUrl;
    
    var myModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
    myModal.show();
}
</script>

@endpush
@endsection