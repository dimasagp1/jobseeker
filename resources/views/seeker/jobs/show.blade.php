    @extends('layouts.seeker')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-light rounded-4 p-3 me-4 text-center" style="width: 80px; height: 80px;">
                            @if($job->company?->company_logo)
                                <img src="{{ asset('storage/' . $job->company->company_logo) }}" class="img-fluid" alt="Logo">
                            @else
                                <i class="fas fa-building fa-2x text-muted opacity-50 mt-2"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1">{{ $job->title }}</h3>
                            <p class="text-primary fw-bold mb-0">{{ $job->company->name }}</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-6 col-md-3 text-center border-end">
                            <small class="text-muted d-block text-uppercase fw-bold fs-9 tracking-wider">Gaji</small>
                            <span class="fw-bold text-dark small">{{ $job->salary_formatted }}</span>
                        </div>
                        <div class="col-6 col-md-3 text-center border-end">
                            <small class="text-muted d-block text-uppercase fw-bold fs-9 tracking-wider">Tipe</small>
                            <span class="fw-bold text-dark small">{{ ucfirst($job->job_type) }}</span>
                        </div>
                        <div class="col-6 col-md-3 text-center border-end">
                            <small class="text-muted d-block text-uppercase fw-bold fs-9 tracking-wider">Lokasi</small>
                            <span class="fw-bold text-dark small">{{ $job->location?->name ?? 'Remote' }}</span>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <small class="text-muted d-block text-uppercase fw-bold fs-9 tracking-wider">Sisa Waktu</small>
                            <div class="small mt-1">
                                {!! $job->remaining_time_html !!}
                            </div>
                        </div>
                    </div>

                        <div class="mb-5">
                            <h6 class="fw-bold text-dark border-start border-4 border-primary ps-3 mb-3 text-uppercase tracking-wider">Deskripsi Pekerjaan</h6>
                            <div class="text-secondary lh-lg fs-7">
                                {!! nl2br(e($job->description)) !!}
                            </div>
                        </div>

                        <div class="mb-5">
                            <h6 class="fw-bold text-dark border-start border-4 border-primary ps-3 mb-3 text-uppercase tracking-wider">Persyaratan</h6>
                            <div class="text-secondary lh-lg fs-7">
                                {!! nl2br(e($job->requirements)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 sticky-top" style="top: 100px;">
                <h6 class="fw-bold text-dark mb-4">Aksi Cepat</h6>
                
                @if($hasApplied)
                    <div class="alert alert-info border-0 rounded-3 small py-3">
                        <i class="fas fa-check-circle me-2"></i> Anda sudah melamar lowongan ini.
                    </div>
                    @elseif($job->isExpired())
                    <div class="alert alert-danger border-0 rounded-3 small py-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> Lowongan ini sudah berakhir.
                    </div>
                @else       
                    {{-- Hapus <form> lama yang menggunakan route('seeker.jobs.apply') --}}
                    <a href="{{ route('seeker.jobs.apply.form', $job) }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm mb-3">
                        Lamar Sekarang
                    </a>
                @endif
                <div class="d-flex gap-2">
                    @if($isSaved)
                        <form action="{{ route('seeker.jobs.unsave', $job) }}" method="POST" class="flex-grow-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-3 fw-bold fs-7"><i class="fas fa-bookmark me-2"></i> Hapus Simpan</button>
                        </form>
                    @else
                        <form action="{{ route('seeker.jobs.save', $job) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 rounded-3 fw-bold fs-7"><i class="far fa-bookmark me-2"></i> Simpan</button>
                        </form>
                    @endif
                    <button class="btn btn-light rounded-3 px-3" onclick="copyToClipboard('{{ url()->current() }}')">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.9rem; }
    .fs-9 { font-size: 0.65rem; }
    .tracking-wider { letter-spacing: 0.08rem; }
</style>
@endsection