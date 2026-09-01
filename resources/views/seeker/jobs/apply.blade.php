@extends('layouts.seeker')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between mb-5 position-relative">
                <div class="step-item text-center">
                    <div class="step-circle active" id="dot-1">1</div>
                    <div class="small fw-bold mt-2">Pilih Dokumen</div>
                </div>
                <div class="step-item text-center">
                    <div class="step-circle" id="dot-2">2</div>
                    <div class="small fw-bold mt-2">Pertanyaan</div>
                </div>
                <div class="step-item text-center">
                    <div class="step-circle" id="dot-3">3</div>
                    <div class="small fw-bold mt-2">Review & Kirim</div>
                </div>
            </div>

            <form action="{{ route('seeker.jobs.apply.submit', $job) }}" method="POST" enctype="multipart/form-data" id="multiStepForm">
                @csrf

                <div class="step-content" id="step-1">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Lengkapi Dokumen</h5>
                                <a href="{{ route('seeker.profile.edit') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="fas fa-user-edit me-1"></i> Perbarui Profil Utama
                                </a>
                            </div>

                            <div class="alert alert-light border-0 small mb-4 py-3 px-4 rounded-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Data identitas diambil dari profil Anda. Pastikan data sudah benar sebelum melanjutkan.
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label-custom">NAMA LENGKAP</label>
                                    <input type="text" class="form-control input-custom bg-light" value="{{ $user->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">ALAMAT EMAIL</label>
                                    <input type="text" class="form-control input-custom bg-light" value="{{ $user->email }}" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-custom">RESUMÉ / CV (PDF, DOCX, RTF)</label>

                                    @if($profile?->resume_path)
                                    <div class="card border-0 bg-light mb-3 rounded-4">
                                        <div class="card-body d-flex align-items-center p-3">
                                            <div class="icon-box bg-white text-primary me-3 shadow-sm">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="small fw-bold mb-0 text-dark">Gunakan resume dari profil Anda</p>
                                                <span class="extra-small text-muted">{{ $profile->resume_filename ?? 'CV_Terunggah.pdf' }}</span>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="use_existing_resume" value="1" id="useExisting" checked>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div id="new-upload-area" class="{{ $profile?->resume_path ? 'd-none' : '' }}">
                                        <div class="upload-area p-4 text-center rounded-4 border-dashed" id="dropzone-resume">
                                            <input type="file" name="resume" class="form-control d-none" id="resumeFile" {{ $profile?->resume_path ? '' : 'required' }}>
                                            <label for="resumeFile" class="mb-0 cursor-pointer">
                                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                                <p class="small mb-0 fw-bold">Klik untuk unggah file baru</p>
                                                <span class="text-muted extra-small">Maksimal 5MB</span>
                                            </label>
                                        </div>
                                        <div id="file-name-display" class="mt-2 small text-primary fw-bold d-none text-center"></div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label-custom">SURAT LAMARAN (COVER LETTER) <span class="text-muted fw-normal text-transform-none" style="text-transform: none;">(Opsional)</span></label>
                                    <input type="file" name="cover_letter_file" class="form-control input-custom">
                                </div>
                            </div>

                            <div class="mt-5 text-end">
                                <button type="button" class="btn btn-primary px-5 py-2 rounded-pill fw-bold" onclick="nextStep(2)">
                                    Lanjut <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-content d-none" id="step-2">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5">
                        <div class="mb-5 border-bottom pb-3">
                            <h5 class="fw-bold mb-1">Kuesioner Pra-Seleksi</h5>
                            <p class="text-muted small">Jawablah pertanyaan berikut dengan jujur. Jawaban Anda akan membantu HRD mengenal Anda lebih baik sebelum tahap wawancara.</p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 1</label>
                                <p class="text-dark fw-semibold mb-2">Apakah Anda menyatakan bahwa seluruh informasi profil dan dokumen yang Anda lampirkan adalah benar?</p>
                                <select name="q1" class="form-select input-custom" required>
                                    <option value="" disabled selected>Pilih jawaban...</option>
                                    <option value="Ya">Ya, saya menyatakan benar dan dapat dipertanggungjawabkan</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 2</label>
                                <p class="text-dark fw-semibold mb-2">Apakah Anda bersedia untuk bekerja penuh waktu (full-time) di kantor kami?</p>
                                <select name="q2" class="form-select input-custom" required>
                                    <option value="" disabled selected>Pilih jawaban...</option>
                                    <option value="Ya">Ya, saya bersedia</option>
                                    <option value="Tidak">Tidak, saya mencari remote/part-time</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 3</label>
                                <p class="text-dark fw-semibold mb-2">Jika Anda tinggal di luar kota, apakah Anda bersedia untuk melakukan relokasi secara mandiri?</p>
                                <select name="q3" class="form-select input-custom" required>
                                    <option value="" disabled selected>Pilih jawaban...</option>
                                    <option value="Ya">Ya, saya bersedia (atau sudah berdomisili sama)</option>
                                    <option value="Tidak">Tidak bersedia</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 4</label>
                                <p class="text-dark fw-semibold mb-2">Apakah Anda memiliki kendaraan pribadi untuk mendukung mobilitas kerja?</p>
                                <select name="q4" class="form-select input-custom" required>
                                    <option value="" disabled selected>Pilih jawaban...</option>
                                    <option value="Ya">Ya, saya memiliki kendaraan pribadi</option>
                                    <option value="Tidak">Tidak memiliki</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 5</label>
                                <p class="text-dark fw-semibold mb-2">Berapa ekspektasi gaji bulanan (Take Home Pay) yang Anda harapkan?</p>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light" style="border-radius: 12px 0 0 12px;">Rp</span>
                                    <input type="number" name="q5" class="form-control input-custom" style="border-radius: 0 12px 12px 0;" placeholder="Contoh: 5000000" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 6</label>
                                <p class="text-dark fw-semibold mb-2">Beri nilai (1-10) untuk tingkat keahlian teknis Anda yang paling relevan dengan posisi ini.</p>
                                <div class="d-flex align-items-center gap-3 mt-3">
                                    <input type="range" name="q6" class="form-range flex-grow-1" min="1" max="10" step="1" id="q6Range" value="5" required>
                                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; font-size: 1.2rem; font-weight: bold;" id="q6Val">5</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 7</label>
                                <p class="text-dark fw-semibold mb-2">Kapan tanggal tercepat Anda bisa mulai bekerja jika Anda diterima?</p>
                                <input type="date" name="q15" class="form-control input-custom" required>
                            </div>

                            <div class="col-12 mt-5">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Bagian II: Penilaian Karakter & Esai Singkat</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 8</label>
                                <p class="text-dark fw-semibold mb-2">Ceritakan satu pencapaian terbesar dalam karier/pendidikan Anda sejauh ini.</p>
                                <textarea name="q7" class="form-control input-custom" rows="3" required placeholder="Jelaskan secara singkat apa yang Anda capai dan bagaimana cara Anda meraihnya..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 9</label>
                                <p class="text-dark fw-semibold mb-2">Sebutkan satu keahlian spesifik yang membuat Anda merasa paling cocok untuk posisi ini.</p>
                                <input type="text" name="q8" class="form-control input-custom" required placeholder="Contoh: Saya mahir menggunakan framework Laravel untuk membangun API yang cepat.">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 10</label>
                                <p class="text-dark fw-semibold mb-2">Bagaimana preferensi gaya kerja Anda sehari-hari?</p>
                                <select name="q9" class="form-select input-custom" required>
                                    <option value="" disabled selected>Pilih jawaban...</option>
                                    <option value="Mandiri">Saya lebih produktif bekerja secara mandiri</option>
                                    <option value="Kolaborasi Tim">Saya lebih suka berkolaborasi dalam tim</option>
                                    <option value="Fleksibel">Saya bisa beradaptasi dengan keduanya</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Pertanyaan 11</label>
                                <p class="text-dark fw-semibold mb-2">Deskripsikan budaya atau lingkungan kerja yang paling memotivasi Anda.</p>
                                <input type="text" name="q10" class="form-control input-custom" required placeholder="Contoh: Lingkungan yang terbuka, transparan, dan minim birokrasi kaku.">
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 12</label>
                                <p class="text-dark fw-semibold mb-2">Bagaimana cara Anda menyikapi kritik atau masukan negatif dari atasan Anda?</p>
                                <textarea name="q11" class="form-control input-custom" rows="2" required placeholder="Tuliskan jawaban Anda di sini..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 13</label>
                                <p class="text-dark fw-semibold mb-2">Ceritakan pengalaman Anda saat harus menghadapi perbedaan pendapat dalam sebuah tim.</p>
                                <textarea name="q12" class="form-control input-custom" rows="3" required placeholder="Bagaimana situasinya dan bagaimana Anda menyelesaikannya?"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 14</label>
                                <p class="text-dark fw-semibold mb-2">Mengapa Anda tertarik melamar di perusahaan kami?</p>
                                <textarea name="q13" class="form-control input-custom" rows="2" required placeholder="Apa yang membuat Anda memilih kami dibandingkan perusahaan lain?"></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Pertanyaan 15</label>
                                <p class="text-dark fw-semibold mb-2">Di mana Anda melihat diri Anda (target karier) dalam 1-3 tahun ke depan?</p>
                                <input type="text" name="q14" class="form-control input-custom" required placeholder="Contoh: Saya ingin menjadi Senior Developer yang memimpin tim kecil.">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 border-top pt-4">
                            <button type="button" class="btn btn-light px-4 rounded-3 fw-bold" onclick="nextStep(1)"><i class="fas fa-arrow-left me-2"></i> Kembali</button>
                            <button type="button" class="btn btn-primary px-5 rounded-3 fw-bold" onclick="nextStep(3)">Lanjut ke Review <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>
                </div>

                <div class="step-content d-none" id="step-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                        <i class="fas fa-file-signature fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">Review Lamaran</h5>
                        <p class="text-muted small">Silakan periksa kembali semua dokumen dan jawaban Anda. Data yang sudah dikirim tidak dapat diubah kembali.</p>
                        <div class="alert alert-info py-2 small mb-4">
                            Semua pertanyaan q1 s/d q15 telah terisi.
                        </div>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-light px-4 fw-bold" onclick="nextStep(2)">Cek Jawaban</button>
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">Kirim Lamaran Sekarang</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .step-circle {
        width: 45px;
        height: 45px;
        line-height: 45px;
        background: #e9ecef;
        border-radius: 50%;
        display: inline-block;
        color: #adb5bd;
        font-weight: bold;
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #e9ecef;
    }

    .step-circle.active {
        background: #0d6efd;
        color: white;
        box-shadow: 0 0 0 1px #0d6efd;
    }

    .input-custom {
        border-radius: 12px;
        border: 1px solid #f1f3f5;
        padding: 12px;
        font-size: 0.9rem;
        background-color: #fcfcfc;
    }

    .form-label-custom {
        font-size: 0.7rem;
        font-weight: 800;
        color: #adb5bd;
        letter-spacing: 0.05rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }
</style>

<script>
    /**
     * Fungsi navigasi antar step dengan validasi otomatis
     */
    function nextStep(step) {
        // Ambil container step yang sedang aktif saat ini
        const currentStepDiv = document.querySelector('.step-content:not(.d-none)');

        // Cari semua input, select, dan textarea yang memiliki atribut 'required' di step ini
        const inputs = currentStepDiv.querySelectorAll('input[required], select[required], textarea[required]');

        // Cek validasi HTML5 sebelum diperbolehkan lanjut
        let isValid = true;
        inputs.forEach(input => {
            // Jika input kosong atau tidak sesuai format, tampilkan pesan error bawaan browser
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
            }
        });

        // Jika ada yang belum valid, hentikan proses perpindahan step
        if (!isValid) return;

        // Jika valid, sembunyikan semua step dan tampilkan step tujuan
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
        document.getElementById('step-' + step).classList.remove('d-none');

        // Update indikator lingkaran (progress bar)
        document.querySelectorAll('.step-circle').forEach((el, idx) => {
            el.classList.toggle('active', (idx + 1) <= step);
        });

        // Scroll kembali ke atas halaman agar user melihat dari awal step baru
        window.scrollTo(0, 0);
    }

    /**
     * Logika Input Range untuk Pertanyaan Skala Keahlian (q6)
     */
    const q6Range = document.getElementById('q6Range');
    const q6Val = document.getElementById('q6Val');
    if (q6Range && q6Val) {
        q6Range.addEventListener('input', () => {
            q6Val.textContent = q6Range.value;
        });
    }

    /**
     * Logika Toggle Resume: Antara pakai file profil atau upload baru
     */
    document.getElementById('useExisting')?.addEventListener('change', function() {
        const uploadArea = document.getElementById('new-upload-area');
        const resumeInput = document.getElementById('resumeFile');

        if (this.checked) {
            // Sembunyikan area upload dan matikan kewajiban mengisi file
            uploadArea.classList.add('d-none');
            resumeInput.required = false;
        } else {
            // Tampilkan area upload dan wajibkan user memilih file
            uploadArea.classList.remove('d-none');
            resumeInput.required = true;
        }
    });

    /**
     * Menampilkan nama file yang dipilih pada area upload
     */
    const resumeFileInput = document.getElementById('resumeFile');
    if (resumeFileInput) {
        resumeFileInput.addEventListener('change', function() {
            const display = document.getElementById('file-name-display');
            if (this.files.length > 0) {
                display.textContent = "File terpilih: " + this.files[0].name;
                display.classList.remove('d-none');
            }
        });
    }

    /**
     * Menampilkan nama file untuk Surat Lamaran (Cover Letter)
     */
    const clFileInput = document.querySelector('input[name="cover_letter_file"]');
    if (clFileInput) {
        clFileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                // Menambahkan feedback visual sederhana bahwa file sudah masuk
                this.classList.add('is-valid');
            }
        });
    }

    /**
     * Indikator Progres Loading saat Mengirim Lamaran & Email
     */
    document.getElementById('multiStepForm')?.addEventListener('submit', function() {
        const overlay = document.getElementById('email-loading-overlay');
        if (overlay) {
            overlay.classList.remove('d-none');
            overlay.style.display = 'flex';
        }
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
        }
    });
</script>

{{-- MODAL / OVERLAY LOADING PENGIRIMAN EMAIL --}}
<div id="email-loading-overlay" class="d-none" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.85); z-index: 99999; backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; color: #fff; text-align: center;">
    <div style="background: rgba(30, 41, 59, 0.95); border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; padding: 35px 30px; max-width: 440px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div class="spinner-border text-primary mb-4" style="width: 3.5rem; height: 3.5rem; border-width: 4px;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h5 class="fw-bold mb-2 text-white">Mengirimkan Lamaran & Email...</h5>
        <p class="text-white-50 small mb-0">Mohon tunggu sebentar, data lamaran dan berkas Anda sedang diproses serta email konfirmasi sedang dikirimkan ke email Anda.</p>
    </div>
</div>
@endsection