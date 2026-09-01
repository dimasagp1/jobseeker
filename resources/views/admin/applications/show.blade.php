@extends('layouts.admin')

@section('content')

{{-- ================================================================ --}}
{{-- BLOK DATA RELASI PSIKOTES --}}
{{-- ================================================================ --}}
@php
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
    
    $hasKraepelin = $application->kraepelin_id || $application->kraepelinTest;
    $kraepelinTest = $application->kraepelinTest;
@endphp

<style>
    :root {
        --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0;
        --text-heading: #1e293b; --brand-primary: #0d6efd; --brand-indigo: #4338ca;
    }
    .detail-card {
        border-radius: 16px; border: 1px solid var(--slate-200);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); background: #fff; overflow: hidden;
    }
    .info-box-custom {
        background: var(--slate-50); border: 1px solid var(--slate-100); border-radius: 12px; padding: 15px;
    }
    .pdf-container { width: 100%; height: 700px; border-radius: 12px; border: 1px solid var(--slate-200); }
    .status-badge-lg { padding: 8px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    
    /* Scrollable Tabs ala Company */
    .nav-tabs-custom { border-bottom: 2px solid var(--slate-100); gap: 10px; flex-wrap: nowrap; overflow-x: auto; white-space: nowrap; padding-bottom: 2px; }
    .nav-tabs-custom::-webkit-scrollbar { height: 4px; }
    .nav-tabs-custom::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 4px; }
    .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 12px 15px; position: relative; background: transparent; transition: all 0.3s; }
    .nav-tabs-custom .nav-link:hover { color: var(--brand-primary); }
    .nav-tabs-custom .nav-link.active { color: var(--brand-primary); }
    .nav-tabs-custom .nav-link.active::after { content: ""; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px; background: var(--brand-primary); }
    
    /* Kraepelin & Psikotes Styles */
    .kraepelin-card { border-radius: 16px; padding: 24px; background: #fff; border: 1px solid var(--slate-200); }
    .k-chart-container { position: relative; width: 100%; }
    .psy-badge { width: 35px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800; border-radius: 6px; }
</style>

<div class="container-fluid pb-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.applications.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-4">
            <div class="detail-card text-center p-4 mb-4">
                @php
                    $avatarUrl = $application->user->avatar 
                        ? asset('storage/' . $application->user->avatar) 
                        : 'https://ui-avatars.com/api/?name='.urlencode($application->user->name).'&background=f1f5f9&color=334155';
                @endphp
                <img src="{{ $avatarUrl }}" class="rounded-circle border border-4 border-white shadow-sm mb-3 object-fit-cover" width="110" height="110" alt="Avatar">
                
                <h5 class="fw-bold text-dark mb-1">{{ $application->user->name }}</h5>
                <p class="text-muted small mb-3">{{ $application->user->email }}</p>
                
                <div class="mb-4">
                    <span class="status-badge-lg 
                        bg-{{ match($application->status) {
                            'pending' => 'warning text-dark',
                            'accepted' => 'success text-white',
                            'rejected' => 'danger text-white',
                            default => 'info text-white'
                        } }}">
                        {{ match($application->status) {
                            'pending' => 'Menunggu',
                            'reviewed' => 'Ditinjau',
                            'shortlisted' => 'Terpilih',
                            'test_invited' => 'Undangan Tes',
                            'test_in_progress' => 'Mengerjakan Tes',
                            'test_completed' => 'Tes Selesai',
                            'interview' => 'Wawancara',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            default => ucfirst($application->status)
                        } }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="fw-bold text-muted small text-uppercase mb-2" style="font-size: 0.7rem;">Status Psikotes</div>
                    <div class="d-flex gap-2 justify-content-center mb-3">
                        <span class="psy-badge {{ $hasKraepelin ? 'bg-primary text-white' : 'bg-light text-muted border' }}" title="Kraepelin">KRA</span>
                        <span class="psy-badge {{ $discResult ? 'bg-success text-white' : 'bg-light text-muted border' }}" title="DISC">DSC</span>
                        <span class="psy-badge {{ $msdtResult ? 'bg-danger text-white' : 'bg-light text-muted border' }}" title="MSDT">MSD</span>
                        <span class="psy-badge {{ $papiResult ? 'bg-info text-white' : 'bg-light text-muted border' }}" title="PAPI Kostick">PAP</span>
                    </div>
                    @if($hasKraepelin || $discResult || $msdtResult || $papiResult)
                        <a href="{{ route('admin.applications.all-psychological-pdf', $application->id) }}" 
                           onclick="openPdfPreviewModal('{{ route('admin.applications.all-psychological-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('admin.applications.all-psychological-pdf', $application->id) }}', 'Pratinjau Laporan Lengkap Psikotes'); return false;" 
                           class="btn btn-danger btn-sm w-100 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-file-pdf me-1"></i> Unduh Hasil Semua Tes (1 File)
                        </a>
                    @endif
                </div>

                <div class="text-left mt-4 border-top pt-4">
                    <div class="info-box-custom mb-3">
                        <small class="text-muted d-block fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Melamar Posisi</small>
                        <span class="fw-bold text-primary">{{ $application->job->title }}</span>
                    </div>
                    <div class="info-box-custom">
                        <small class="text-muted d-block fw-bold text-uppercase mb-1" style="font-size: 0.65rem;">Waktu Melamar</small>
                        <span class="fw-medium text-dark">{{ $application->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="detail-card">
                
                <div class="card-header bg-white p-3 border-bottom-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="applicationTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-cv-btn" data-bs-toggle="tab" data-bs-target="#tab-cv" type="button" role="tab"><i class="fas fa-file-pdf mr-1"></i> CV</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-profile-btn" data-bs-toggle="tab" data-bs-target="#tab-profile" type="button" role="tab"><i class="fas fa-user-circle mr-1"></i> Profil</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-kuesioner-btn" data-bs-toggle="tab" data-bs-target="#tab-kuesioner" type="button" role="tab"><i class="fas fa-poll-h mr-1"></i> Kuesioner</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-internal-btn" data-bs-toggle="tab" data-bs-target="#tab-internal" type="button" role="tab"><i class="fas fa-clipboard-check mr-1"></i> Catatan</button>
                        </li>
                        
                        @if($hasKraepelin)
                            <li class="nav-item"><button class="nav-link text-primary fw-bold" id="tab-kraepelin-btn" data-bs-toggle="tab" data-bs-target="#tab-kraepelin" type="button" role="tab"><i class="fas fa-calculator mr-1"></i> Kraepelin</button></li>
                        @endif
                        @if($discResult)
                            <li class="nav-item"><button class="nav-link text-success fw-bold" id="tab-disc-btn" data-bs-toggle="tab" data-bs-target="#tab-disc" type="button" role="tab"><i class="fas fa-shapes mr-1"></i> DISC</button></li>
                        @endif
                        @if($msdtResult)
                            <li class="nav-item"><button class="nav-link text-danger fw-bold" id="tab-msdt-btn" data-bs-toggle="tab" data-bs-target="#tab-msdt" type="button" role="tab"><i class="fas fa-users-cog mr-1"></i> MSDT</button></li>
                        @endif
                        @if($papiResult)
                            <li class="nav-item"><button class="nav-link text-info fw-bold" id="tab-papi-btn" data-bs-toggle="tab" data-bs-target="#tab-papi" type="button" role="tab"><i class="fas fa-clipboard-check mr-1"></i> PAPI</button></li>
                        @endif
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="applicationTabContent">
                        
                        <div class="tab-pane fade show active" id="tab-cv" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0">Berkas Curriculum Vitae</h6>
                                @if($application->cv_path)
                                    <a href="{{ route('admin.applications.download-cv', $application->id) }}" class="btn btn-sm btn-primary px-3 rounded-pill fw-bold shadow-sm"><i class="fas fa-download mr-1"></i> Unduh PDF</a>
                                @endif
                            </div>
                            @if($application->cv_path)
                                <iframe src="{{ asset('storage/' . $application->cv_path) }}#toolbar=0" class="pdf-container"></iframe>
                            @else
                                <div class="alert alert-warning border-0 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i> File CV tidak ditemukan.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="tab-profile" role="tabpanel">
                            @php $profile = $application->user->seekerProfile; @endphp
                            @if($profile)
                                <h6 class="fw-bold text-primary mb-3">Ringkasan Profesional</h6>
                                <p class="text-muted bg-light p-3 rounded" style="line-height: 1.6;">{{ $profile->summary ?? 'Pelamar tidak mencantumkan ringkasan.' }}</p>

                                <div class="row mt-4">
                                    <div class="col-md-6 mb-4">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-briefcase text-success mr-2"></i>Pengalaman Kerja</h6>
                                        @forelse($profile->experiences ?? [] as $exp)
                                            <div class="mb-3 pl-3 border-left border-3" style="border-left-color: #10b981 !important;">
                                                <div class="fw-bold text-dark">{{ $exp->job_title }}</div>
                                                <small class="text-primary fw-medium">{{ $exp->company_name }}</small><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Sekarang' }}</small>
                                            </div>
                                        @empty <p class="text-muted small italic">Tidak ada data pengalaman.</p> @endforelse
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-graduation-cap text-primary mr-2"></i>Pendidikan</h6>
                                        @forelse($profile->educations ?? [] as $edu)
                                            <div class="mb-3 pl-3 border-left border-3" style="border-left-color: #3b82f6 !important;">
                                                <div class="fw-bold text-dark">{{ $edu->institution }}</div>
                                                <small class="text-dark">{{ $edu->degree }} {{ $edu->field_of_study ? '- ' . $edu->field_of_study : '' }}</small><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($edu->start_date)->format('Y') }} - {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : 'Sekarang' }}</small>
                                            </div>
                                        @empty <p class="text-muted small italic">Tidak ada data pendidikan.</p> @endforelse
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5"><i class="fas fa-user-slash fa-3x text-muted mb-3 opacity-50"></i><p class="text-muted">Profil belum dilengkapi.</p></div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="tab-kuesioner" role="tabpanel">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-1"><i class="fas fa-poll-h text-primary me-2"></i>Hasil Kuesioner Pra-Seleksi</h6>
                                <p class="text-muted small">Jawaban yang diisi oleh pelamar saat mengajukan lamaran pekerjaan.</p>
                            </div>

                            @if(!empty($application->answers) && is_array($application->answers))
                                @php
                                    $questions = [
                                        'q1'  => ['icon' => 'fa-user-shield',     'q' => 'Pernyataan Kejujuran Data'],
                                        'q2'  => ['icon' => 'fa-clock',           'q' => 'Ketersediaan Full-Time'],
                                        'q3'  => ['icon' => 'fa-map-marked-alt',  'q' => 'Kesediaan Relokasi'],
                                        'q4'  => ['icon' => 'fa-car',             'q' => 'Kepemilikan Kendaraan'],
                                        'q5'  => ['icon' => 'fa-money-bill-wave', 'q' => 'Ekspektasi Gaji Bulanan'],
                                        'q15' => ['icon' => 'fa-calendar-check',  'q' => 'Tanggal Mulai Bergabung'],
                                        'q6'  => ['icon' => 'fa-tools',           'q' => 'Rating Skill Teknis'],
                                        'q7'  => ['icon' => 'fa-trophy',          'q' => 'Pencapaian Terbesar'],
                                        'q8'  => ['icon' => 'fa-star',            'q' => 'Keahlian Spesifik Utama'],
                                        'q9'  => ['icon' => 'fa-users',           'q' => 'Preferensi Gaya Kerja'],
                                        'q10' => ['icon' => 'fa-building',        'q' => 'Budaya Kerja Memotivasi'],
                                        'q11' => ['icon' => 'fa-comments',        'q' => 'Sikap Terhadap Kritik'],
                                        'q12' => ['icon' => 'fa-handshake',       'q' => 'Penanganan Perbedaan Pendapat'],
                                        'q13' => ['icon' => 'fa-bullseye',        'q' => 'Motivasi Melamar'],
                                        'q14' => ['icon' => 'fa-rocket',          'q' => 'Visi Karier 1-3 Tahun']
                                    ];
                                @endphp

                                <div class="row g-3">
                                    @foreach($questions as $key => $data)
                                        @if(isset($application->answers[$key]))
                                            <div class="{{ in_array($key, ['q7','q8','q10','q11','q12','q13','q14']) ? 'col-12' : 'col-md-6' }} mb-3">
                                                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="flex-shrink-0 bg-white shadow-sm d-flex align-items-center justify-content-center rounded-3 me-2" style="width: 36px; height: 36px;">
                                                                <i class="fas {{ $data['icon'] }} text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">Pertanyaan {{ strtoupper($key) }}</div>
                                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $data['q'] }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="bg-white p-3 rounded-3 border border-light shadow-sm mt-2">
                                                            @if($key === 'q5')
                                                                <div class="d-flex align-items-center">
                                                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold" style="font-size: 0.95rem;">
                                                                        Rp {{ number_format($application->answers[$key], 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="ms-2 text-muted small">/ bulan</span>
                                                                </div>
                                                            @elseif($key === 'q15')
                                                                <div class="d-flex align-items-center text-primary fw-bold">
                                                                    <i class="far fa-calendar-alt me-2"></i>
                                                                    {{ \Carbon\Carbon::parse($application->answers[$key])->translatedFormat('d F Y') }}
                                                                </div>
                                                            @elseif($key === 'q6')
                                                                <div class="progress" style="height: 8px; border-radius: 10px; background: #f1f5f9; width: 100%;">
                                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $application->answers[$key] * 10 }}%"></div>
                                                                </div>
                                                                <div class="mt-2 fw-bold text-primary small">{{ $application->answers[$key] }} / 10</div>
                                                            @else
                                                                <p class="mb-0 text-secondary" style="font-size: 0.9rem; line-height: 1.5; white-space: pre-line;">
                                                                    {{ $application->answers[$key] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 border rounded-4 bg-light" style="border-style: dashed;">
                                    <i class="fas fa-clipboard-list fa-3x text-muted opacity-25 mb-3"></i>
                                    <h6 class="fw-bold text-muted">Tidak Ada Data Kuesioner</h6>
                                    <p class="small text-muted mb-0">Pelamar ini tidak mengisi atau tidak memiliki data kuesioner pra-seleksi.</p>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="tab-internal" role="tabpanel">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-envelope-open-text text-warning mr-2"></i>Surat Lamaran (Cover Letter)</h6>
                                <div class="bg-light p-4 rounded-lg border shadow-sm" style="color: #475569; min-height: 100px;">
                                    @if(!empty($application->cover_letter_path))
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-file-alt fa-2x text-primary mr-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">Dokumen Terlampir</h6>
                                                <a href="{{ asset('storage/' . $application->cover_letter_path) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 mt-1"><i class="fas fa-download mr-1"></i> Unduh File</a>
                                            </div>
                                        </div>
                                    @elseif(!empty(trim($application->cover_letter)))
                                        <div style="white-space: pre-line;">{{ $application->cover_letter }}</div>
                                    @else
                                        <p class="mb-0 italic text-muted">Kandidat tidak melampirkan pesan atau dokumen surat lamaran.</p>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-user-shield text-danger mr-2"></i>Catatan Internal Admin</h6>
                                <div class="p-4 rounded-lg border" style="background-color: #f0f7ff; border-color: #cfe2ff !important;">
                                    @if($application->notes)
                                        <p class="mb-0 text-primary fw-medium" style="font-size: 1rem; white-space: pre-line;">{{ $application->notes }}</p>
                                    @else
                                        <p class="mb-0 text-muted italic">Belum ada catatan evaluasi untuk lamaran ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ================= TAB: KRAEPELIN ================= --}}
                        @if($hasKraepelin)
                        <div class="tab-pane fade" id="tab-kraepelin" role="tabpanel">
                            @php
                                $chartData = is_string($kraepelinTest->results_chart) ? json_decode($kraepelinTest->results_chart, true) : $kraepelinTest->results_chart;
                                $correct = $kraepelinTest->total_correct;
                                $error = $kraepelinTest->total_answered - $kraepelinTest->total_correct;
                                $skipped = max(0, $kraepelinTest->tianker - $error);
                                
                                $quarters = [];
                                if (is_array($chartData) && count($chartData) > 0) {
                                    for ($i = 0; $i < 50; $i += 10) {
                                        $slice = array_slice($chartData, $i, 10);
                                        $quarters[] = count($slice) > 0 ? round(array_sum($slice) / count($slice), 1) : 0;
                                    }
                                } else { $quarters = [0,0,0,0,0]; }
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <div><h5 class="fw-bold mb-1">Executive Summary Kraepelin</h5><p class="small text-muted mb-0">Laporan performa kognitif, stabilitas emosi, dan akurasi.</p></div>
                                
                                {{-- Tombol Export Kraepelin tetap ada --}}
                                @if(Route::has('admin.applications.kraepelin-pdf'))
                                <a href="{{ route('admin.applications.kraepelin-pdf', $application->id) }}" 
                                   onclick="openPdfPreviewModal('{{ route('admin.applications.kraepelin-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('admin.applications.kraepelin-pdf', $application->id) }}', 'Pratinjau Kraepelin Assessment'); return false;" 
                                   class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-file-pdf mr-1"></i> Ekspor Laporan</a>
                                @endif
                            </div>

                            <div class="p-4 border border-primary border-opacity-25 bg-light rounded-lg mb-4 shadow-sm">
                                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-clipboard-list mr-2"></i>Ringkasan Evaluasi</h6>
                                <p class="small text-dark mb-0" style="line-height: 1.6;">
                                    Kecepatan kerja: <b>{{ $kraepelinTest->panker >= 15 ? 'tinggi' : ($kraepelinTest->panker >= 10 ? 'sedang / rata-rata' : 'rendah') }}</b>. 
                                    Ketelitian: <b>{{ $kraepelinTest->tianker <= 5 ? 'sangat baik (jarang salah)' : ($kraepelinTest->tianker <= 15 ? 'cukup baik' : 'kurang (terburu-buru)') }}</b>. 
                                    Stabilitas emosi (tekanan): <b>{{ $kraepelinTest->janker <= 4 ? 'sangat stabil' : ($kraepelinTest->janker <= 10 ? 'cukup stabil' : 'mudah terpengaruh') }}</b>. 
                                    Ketahanan kerja (stamina): <b>{{ $kraepelinTest->ganker >= 0 ? 'positif (mampu menjaga fokus)' : 'negatif (rentan lelah)' }}</b>.
                                </p>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="kraepelin-card h-100 shadow-sm border-0 bg-light p-4 rounded d-flex flex-column">
                                        <h6 class="fw-bold mb-3 text-center text-dark"><i class="fas fa-chart-pie mr-2"></i>Distribusi Jawaban</h6>
                                        <div class="k-chart-container mx-auto flex-grow-1" style="min-height: 180px; width: 100%; max-width: 180px;"><canvas id="donutAnswers"></canvas></div>
                                        <div class="mt-4 bg-white p-3 rounded shadow-sm border mt-auto">
                                            <div class="d-flex justify-content-between small mb-2 border-bottom pb-1"><span>Benar</span> <span class="fw-bold text-success">{{ $correct }}</span></div>
                                            <div class="d-flex justify-content-between small mb-2 border-bottom pb-1"><span>Salah</span> <span class="fw-bold text-danger">{{ $error }}</span></div>
                                            <div class="d-flex justify-content-between small"><span>Hole</span> <span class="fw-bold text-warning">{{ $skipped }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="kraepelin-card h-100 shadow-sm border-0 p-4 rounded bg-white d-flex flex-column">
                                        <h6 class="fw-bold mb-4 text-dark"><i class="fas fa-chart-bar mr-2"></i>Grafik P-T-J-G</h6>
                                        <div class="k-chart-container w-100 flex-grow-1" style="min-height: 250px;"><canvas id="barPerformance"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-sm rounded-lg overflow-hidden bg-white">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 text-primary"><i class="fas fa-wave-square mr-2"></i>Kurva Kerja (Work Rhythm Trend)</h6>
                                    <div class="k-chart-container" style="height: 280px;"><canvas id="lineTrend"></canvas></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ================= TAB: DISC ================= --}}
                        @if($discResult)
                        <div class="tab-pane fade" id="tab-disc" role="tabpanel">
                            @php 
                                $discData = is_array($discResult->final_score) ? $discResult->final_score : [];
                                $d_score = $discData['D'] ?? 0; $i_score = $discData['I'] ?? 0;
                                $s_score = $discData['S'] ?? 0; $c_score = $discData['C'] ?? 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <div><h5 class="fw-bold mb-1">Evaluasi Psikologi DISC</h5><p class="small text-muted mb-0">Pemetaan perilaku, komunikasi, dan adaptasi kerja.</p></div>
                                @if(Route::has('admin.applications.disc-pdf'))
                                <a href="{{ route('admin.applications.disc-pdf', $application->id) }}" 
                                   onclick="openPdfPreviewModal('{{ route('admin.applications.disc-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('admin.applications.disc-pdf', $application->id) }}', 'Pratinjau Evaluasi Perilaku DISC'); return false;" 
                                   class="btn btn-outline-success rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-file-pdf mr-1"></i> Ekspor Laporan</a>
                                @endif
                            </div>
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="kraepelin-card shadow-sm border-0 h-100 bg-white p-4 rounded-lg d-flex flex-column">
                                        <h6 class="fw-bold mb-4 text-center text-dark"><i class="fas fa-chart-bar mr-2 text-success"></i>Intensitas Karakter DISC</h6>
                                        <div class="k-chart-container flex-grow-1 w-100" style="min-height: 300px;"><canvas id="discBarChart"></canvas></div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-4 border rounded-lg bg-light shadow-sm h-100">
                                        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-sliders-h mr-2 text-secondary"></i>Rincian Skor Mentah</h6>
                                        <div class="mb-3 p-3 bg-white border-left border-danger shadow-sm">
                                            <div class="d-flex justify-content-between small fw-bold"><span>D (Dominance)</span> <span class="badge bg-danger">{{ $d_score }}</span></div>
                                            <div class="progress mt-2" style="height: 5px;"><div class="progress-bar bg-danger" style="width: {{ ($d_score / 40) * 100 }}%"></div></div>
                                        </div>
                                        <div class="mb-3 p-3 bg-white border-left border-warning shadow-sm">
                                            <div class="d-flex justify-content-between small fw-bold"><span>I (Influence)</span> <span class="badge bg-warning text-dark">{{ $i_score }}</span></div>
                                            <div class="progress mt-2" style="height: 5px;"><div class="progress-bar bg-warning" style="width: {{ ($i_score / 40) * 100 }}%"></div></div>
                                        </div>
                                        <div class="mb-3 p-3 bg-white border-left border-success shadow-sm">
                                            <div class="d-flex justify-content-between small fw-bold"><span>S (Steadiness)</span> <span class="badge bg-success">{{ $s_score }}</span></div>
                                            <div class="progress mt-2" style="height: 5px;"><div class="progress-bar bg-success" style="width: {{ ($s_score / 40) * 100 }}%"></div></div>
                                        </div>
                                        <div class="p-3 bg-white border-left border-primary shadow-sm">
                                            <div class="d-flex justify-content-between small fw-bold"><span>C (Compliance)</span> <span class="badge bg-primary">{{ $c_score }}</span></div>
                                            <div class="progress mt-2" style="height: 5px;"><div class="progress-bar bg-primary" style="width: {{ ($c_score / 40) * 100 }}%"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ================= TAB: MSDT ================= --}}
                        @if($msdtResult)
                        <div class="tab-pane fade" id="tab-msdt" role="tabpanel">
                            @php 
                                $msdtData = is_string($msdtResult->final_score) ? json_decode($msdtResult->final_score, true) : ($msdtResult->final_score ?? []);
                                $to_score = $msdtData['TO'] ?? 0; $ro_score = $msdtData['RO'] ?? 0; $e_score = $msdtData['E'] ?? 0;  
                                $style = str_replace('"', '', $msdtData['style'] ?? 'Deserter');
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <div><h5 class="fw-bold mb-1">MSDT Kepemimpinan</h5><p class="small text-muted mb-0">Orientasi Tugas (TO) vs Relasi (RO).</p></div>
                                @if(Route::has('admin.applications.msdt-pdf'))
                                <a href="{{ route('admin.applications.msdt-pdf', $application->id) }}" 
                                   onclick="openPdfPreviewModal('{{ route('admin.applications.msdt-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('admin.applications.msdt-pdf', $application->id) }}', 'Pratinjau Laporan MSDT Kepemimpinan'); return false;" 
                                   class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-file-pdf mr-1"></i> Ekspor Laporan</a>
                                @endif
                            </div>
                            <div class="card bg-danger text-white mb-4 border-0 shadow-sm rounded-lg">
                                <div class="card-body p-4 text-center">
                                    <div class="badge bg-white text-danger fw-bold mb-2">GAYA KEPEMIMPINAN:</div>
                                    <h3 class="fw-bold mb-0 text-uppercase">{{ $style }}</h3>
                                </div>
                            </div>
                            <div class="kraepelin-card shadow-sm border-0 p-4 rounded-lg bg-light">
                                <h6 class="fw-bold mb-3 text-dark">Skor Dimensi Kepemimpinan</h6>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small fw-bold mb-1"><span>Orientasi Tugas (TO)</span> <span>{{ $to_score }} / 20</span></div>
                                    <div class="progress" style="height: 8px;"><div class="progress-bar bg-primary" style="width: {{ ($to_score/20)*100 }}%"></div></div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small fw-bold mb-1"><span>Orientasi Relasi (RO)</span> <span>{{ $ro_score }} / 20</span></div>
                                    <div class="progress" style="height: 8px;"><div class="progress-bar bg-info" style="width: {{ ($ro_score/20)*100 }}%"></div></div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small fw-bold mb-1"><span>Efektivitas Situasional (E)</span> <span>{{ $e_score }} / 20</span></div>
                                    <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: {{ ($e_score/20)*100 }}%"></div></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- ================= TAB: PAPI ================= --}}
                        @if($papiResult)
                        <div class="tab-pane fade" id="tab-papi" role="tabpanel">
                            @php 
                                $papiData = is_string($papiResult->final_score) ? json_decode($papiResult->final_score, true) : ($papiResult->final_score ?? []);
                                $papiKeys = ['G', 'L', 'I', 'T', 'V', 'S', 'R', 'D', 'C', 'E', 'N', 'A', 'P', 'X', 'B', 'O', 'Z', 'K', 'F', 'W'];
                                $papiScores = []; foreach($papiKeys as $key) { $papiScores[$key] = $papiData[$key] ?? 5; }
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                <div><h5 class="fw-bold mb-1">PAPI Kostick</h5><p class="small text-muted mb-0">Pemetaan peran kerja dan kebutuhan psikologis.</p></div>
                                @if(Route::has('admin.applications.papi-pdf'))
                                <a href="{{ route('admin.applications.papi-pdf', $application->id) }}" 
                                   onclick="openPdfPreviewModal('{{ route('admin.applications.papi-pdf', [$application->id, 'stream' => 1]) }}', '{{ route('admin.applications.papi-pdf', $application->id) }}', 'Pratinjau Laporan PAPI Kostick'); return false;" 
                                   class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-file-pdf mr-1"></i> Ekspor Laporan</a>
                                @endif
                            </div>
                            <div class="kraepelin-card shadow-sm border-0 bg-white p-4 rounded-lg">
                                <h6 class="fw-bold mb-3 text-center text-dark"><i class="fas fa-spider mr-2 text-info"></i>Peta Kepribadian (Radar Chart)</h6>
                                <div class="k-chart-container mx-auto" style="height: 400px; width: 100%;"><canvas id="papiRadarChart"></canvas></div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($hasKraepelin)
        const tabK = document.getElementById('tab-kraepelin-btn');
        if(tabK) tabK.addEventListener('shown.bs.tab', initKraepelinCharts, { once: true });
        if(window.jQuery) { $('#tab-kraepelin-btn').on('shown.bs.tab', function() { initKraepelinCharts(); }); }
        @endif

        @if($discResult)
        const tabD = document.getElementById('tab-disc-btn');
        if(tabD) tabD.addEventListener('shown.bs.tab', initDiscChart, { once: true });
        if(window.jQuery) { $('#tab-disc-btn').on('shown.bs.tab', function() { initDiscChart(); }); }
        @endif

        @if($papiResult)
        const tabP = document.getElementById('tab-papi-btn');
        if(tabP) tabP.addEventListener('shown.bs.tab', initPapiChart, { once: true });
        if(window.jQuery) { $('#tab-papi-btn').on('shown.bs.tab', function() { initPapiChart(); }); }
        @endif
    });

    @if($hasKraepelin)
    let isKraepelinRendered = false;
    function initKraepelinCharts() {
        if(isKraepelinRendered) return;
        isKraepelinRendered = true;
        new Chart(document.getElementById('donutAnswers').getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['Benar', 'Salah', 'Hole'], datasets: [{ data: [{{ $correct }}, {{ $error }}, {{ $skipped }}], backgroundColor: ['#10b981', '#ef4444', '#f59e0b'], borderWidth: 2, borderColor: '#fff' }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false } } }
        });
        new Chart(document.getElementById('barPerformance').getContext('2d'), {
            type: 'bar',
            data: { labels: ['PANKER', 'TIANKER', 'JANKER', 'GANKER'], datasets: [{ data: [{{ $kraepelinTest->panker }}, {{ $kraepelinTest->tianker }}, {{ $kraepelinTest->janker }}, {{ $kraepelinTest->ganker }}], backgroundColor: ['#0d6efd', '#ef4444', '#f59e0b', '#10b981'], borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
        });
        const trendCtx = document.getElementById('lineTrend').getContext('2d');
        const trendDataRaw = {!! json_encode($chartData ?? []) !!};
        new Chart(trendCtx, {
            type: 'line',
            data: { labels: trendDataRaw.map((_, i) => `Kol  ${i + 1}`), datasets: [{ label: 'Skor', data: trendDataRaw, borderColor: '#0d6efd', borderWidth: 2, backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.3, pointRadius: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f1f5f9' } } } }
        });
    }
    @endif

    @if($discResult)
    let isDiscRendered = false;
    function initDiscChart() {
        if(isDiscRendered) return;
        isDiscRendered = true;
        new Chart(document.getElementById('discBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['D', 'I', 'S', 'C'],
                datasets: [{ data: [{{ $d_score }}, {{ $i_score }}, {{ $s_score }}, {{ $c_score }}], backgroundColor: ['rgba(239, 68, 68, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(59, 130, 246, 0.8)'], borderRadius: 6, barThickness: 40 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 40, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
        });
    }
    @endif

    @if($papiResult)
    let isPapiRendered = false;
    function initPapiChart() {
        if(isPapiRendered) return;
        isPapiRendered = true;
        const papiScoresArray = {!! json_encode(array_values($papiScores)) !!};
        const papiLabelsArray = {!! json_encode(array_keys($papiScores)) !!};
        new Chart(document.getElementById('papiRadarChart').getContext('2d'), {
            type: 'radar',
            data: { labels: papiLabelsArray, datasets: [{ data: papiScoresArray, backgroundColor: 'rgba(13, 202, 240, 0.2)', borderColor: '#0dcaf0', pointBackgroundColor: '#0dcaf0', borderWidth: 2 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { r: { beginAtZero: true, max: 10, ticks: { stepSize: 2 } } } }
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

@endsection