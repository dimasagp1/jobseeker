@extends('layouts.admin')

@section('title', 'Pengaturan Perusahaan')
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
    .img-preview-box {
        border: 2px dashed var(--slate-200);
        border-radius: 12px;
        padding: 10px;
        background: var(--slate-50);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .section-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-heading);
        border-left: 4px solid var(--brand-primary);
        padding-left: 12px;
        margin-bottom: 25px;
        margin-top: 10px;
    }
</style>

<div class="container-fluid pb-5">
    {{-- Tampilkan Pesan Error Validasi Jika Ada --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tampilkan Pesan Sukses --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="form-card shadow-sm">
                {{-- HEADER --}}
                <div class="form-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--text-heading);">Identitas & Branding Portal</h4>
                        <p class="text-muted small mb-0">Kelola tampilan visual dan informasi dasar portal karir HerbaTech.</p>
                    </div>
                    <div class="p-3 rounded-circle" style="background: #eef2ff; color: #4338ca;">
                        <i class="fas fa-cogs fa-lg"></i>
                    </div>
                </div>

                {{-- FORM START --}}
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body p-4 p-md-5">
                        <div class="row">
                            {{-- KOLOM KIRI --}}
                            <div class="col-lg-7">
                                <div class="section-title">Informasi Dasar Perusahaan</div>
                                
                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control input-style" 
                                           value="{{ old('company_name', $company->company_name) }}" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Deskripsi Singkat</label>
                                    <textarea name="company_description" class="form-control input-style" 
                                              rows="4">{{ old('company_description', $company->company_description) }}</textarea>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Industri</label>
                                        <input type="text" name="industry" class="form-control input-style" 
                                               value="{{ old('industry', $company->industry) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Jumlah Karyawan</label>
                                        <input type="number" name="company_size" class="form-control input-style" 
                                               value="{{ old('company_size', $company->company_size) }}">
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Website Resmi</label>
                                    <input type="url" name="company_website" class="form-control input-style" 
                                           value="{{ old('company_website', $company->company_website) }}">
                                </div>

                                <div class="section-title mt-5 text-primary">Media Sosial</div>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label-custom"><i class="fab fa-facebook me-1"></i> Facebook</label>
                                        <input type="url" name="facebook" class="form-control input-style" value="{{ old('facebook', $company->facebook) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label-custom"><i class="fab fa-instagram me-1"></i> Instagram</label>
                                        <input type="url" name="instagram" class="form-control input-style" value="{{ old('instagram', $company->instagram) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label-custom"><i class="fab fa-linkedin me-1"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" class="form-control input-style" value="{{ old('linkedin', $company->linkedin) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label-custom"><i class="fab fa-twitter me-1"></i> Twitter</label>
                                        <input type="url" name="twitter" class="form-control input-style" value="{{ old('twitter', $company->twitter) }}">
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            {{-- PERBAIKAN: border-left jadi border-start, pl-lg-5 jadi ps-lg-5 --}}
                            <div class="col-lg-5 border-start ps-lg-5 mt-4 mt-lg-0">
                                <div class="section-title">Aset Visual</div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Logo Perusahaan</label>
                                    <div class="d-flex align-items-center">
                                        <div class="img-preview-box me-3" style="width: 80px; height: 80px;">
                                            <img id="logoPreview" src="{{ $company->company_logo ? asset('storage/' . $company->company_logo) : 'https://placehold.co/80x80?text=Logo' }}" 
                                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        </div>
                                        <input type="file" id="logoInput" name="company_logo" class="form-control-file border p-1 rounded w-100" accept="image/*">
                                    </div>
                                </div>

                                <div class="form-group mb-5">
                                    <label class="form-label-custom">Favicon</label>
                                    <div class="d-flex align-items-center">
                                        <div class="img-preview-box me-3" style="width: 48px; height: 48px;">
                                            <img id="faviconPreview" src="{{ $company->favicon ? asset('storage/' . $company->favicon) : 'https://placehold.co/32x32?text=Fav' }}" 
                                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        </div>
                                        <input type="file" id="faviconInput" name="favicon" class="form-control-file border p-1 rounded w-100" accept="image/x-icon,image/png,image/jpeg">
                                    </div>
                                </div>

                                <div class="bg-light p-4 rounded border mt-3">
                                    <h6 class="fw-bold mb-3">Hero Section (Beranda)</h6>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Gambar Hero</label>
                                        <div class="mb-2">
                                            <div class="img-preview-box w-100 p-0" style="height: 140px;">
                                                <img id="heroPreview" src="{{ $company->hero_image ? asset('storage/' . $company->hero_image) : 'https://placehold.co/600x300?text=Hero+Image' }}" 
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                        <input type="file" id="heroInput" name="hero_image" class="form-control-file border p-1 rounded bg-white w-100" accept="image/*">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Judul Hero</label>
                                        <input type="text" name="hero_title" class="form-control input-style bg-white" 
                                               value="{{ old('hero_title', $company->hero_title) }}">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Deskripsi Hero</label>
                                        <textarea name="hero_description" class="form-control input-style bg-white" rows="3">{{ old('hero_description', $company->hero_description) }}</textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Teks Tombol (CTA)</label>
                                        <input type="text" name="hero_cta_text" class="form-control input-style bg-white" 
                                               value="{{ old('hero_cta_text', $company->hero_cta_text) }}">
                                    </div>
                                </div>

                                <div class="bg-light p-4 rounded border mt-4">
                                    <h6 class="fw-bold mb-3">Halaman Autentikasi & Register</h6>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Judul Halaman Register</label>
                                        <input type="text" name="register_title" class="form-control input-style bg-white" 
                                               value="{{ old('register_title', $company->register_title) }}" placeholder="Contoh: Mulai Karir Anda 🚀">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Deskripsi Halaman Register</label>
                                        <textarea name="register_description" class="form-control input-style bg-white" rows="2" placeholder="Teks kecil di bawah judul">{{ old('register_description', $company->register_description) }}</textarea>
                                    </div>

                                    <hr class="my-4 border-secondary opacity-25">

                                    <h6 class="fw-bold mb-3">Banner Samping (Guest Layout)</h6>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Gambar Banner</label>
                                        <div class="mb-2">
                                            <div class="img-preview-box w-100 p-0" style="height: 140px;">
                                                <img id="guestBannerPreview" src="{{ $company->guest_banner_image ? asset('storage/' . $company->guest_banner_image) : 'https://placehold.co/600x300?text=Banner+Image' }}" 
                                                     style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        </div>
                                        <input type="file" id="guestBannerInput" name="guest_banner_image" class="form-control-file border p-1 rounded bg-white w-100" accept="image/*">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Judul Banner</label>
                                        <input type="text" name="guest_banner_title" class="form-control input-style bg-white" 
                                               value="{{ old('guest_banner_title', $company->guest_banner_title) }}" placeholder="Bawaan: Nama Perusahaan">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label-custom">Deskripsi Banner</label>
                                        <textarea name="guest_banner_description" class="form-control input-style bg-white" rows="3" placeholder="Teks deskripsi singkat di banner...">{{ old('guest_banner_description', $company->guest_banner_description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER BUTTON --}}
                    {{-- PERBAIKAN: text-right diganti menjadi text-end agar sesuai dengan Bootstrap 5 --}}
                    {{-- FOOTER BUTTON --}}
                    <div class="card-footer bg-white text-end py-4 px-4 px-md-5 border-top" style="border-radius: 0 0 16px 16px;">
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm" style="border-radius: 20px;">
                            <i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
                        </button>
                    </div>  
                </form>
                {{-- FORM END --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fungsi re-usable untuk mengubah sumber gambar preview ketika file di-upload
    function setupImagePreview(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Inisialisasi event listener setelah DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
        setupImagePreview('logoInput', 'logoPreview');
        setupImagePreview('faviconInput', 'faviconPreview');
        setupImagePreview('heroInput', 'heroPreview');
        setupImagePreview('guestBannerInput', 'guestBannerPreview');
    });
</script>
@endpush
@endsection