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
    .password-note {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        color: #92400e;
        padding: 12px;
        border-radius: 10px;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid pb-5">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pengguna
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card shadow-sm">
                <div class="form-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--text-heading);">Ubah Informasi Pengguna</h4>
                        <p class="text-muted small mb-0">Mengedit akun untuk <span class="fw-bold text-dark">{{ $user->email }}</span></p>
                    </div>
                    <div class="bg-soft-info p-3 rounded-circle" style="background: #f0f9ff; color: #0ea5e9;">
                        <i class="fas fa-user-edit fa-lg"></i>
                    </div>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4 p-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4 rounded-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan Validasi:</h6>
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-4">
                            {{-- Kolom Kiri: Profil Dasar --}}
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fas fa-id-card text-primary mr-2"></i>Informasi Profil</h6>
                                
                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control input-style @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Alamat Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control input-style @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Peran / Hak Akses <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control input-style @error('role') is-invalid @enderror" required>
                                        <option value="seeker" {{ old('role', $user->role) == 'seeker' ? 'selected' : '' }}>Pencari Kerja (Seeker)</option>
                                        <option value="company" {{ old('role', $user->role) == 'company' ? 'selected' : '' }}>HR HerbaTech (Company)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator System</option>
                                    </select>
                                    @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <div class="custom-control custom-switch custom-switch-lg">
                                        <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label fw-bold" for="isActive" style="cursor: pointer;">Status Akun Aktif</label>
                                        <small class="text-muted d-block">Nonaktifkan jika ingin mencabut akses pengguna ini sementara waktu.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Keamanan --}}
                            <div class="col-md-6 border-left pl-md-5">
                                <h6 class="fw-bold text-dark mb-4 pb-2 border-bottom"><i class="fas fa-shield-alt text-warning mr-2"></i>Keamanan Akun</h6>
                                
                                <div class="password-note mb-4">
                                    <i class="fas fa-info-circle mr-1"></i> <strong>Informasi:</strong> Biarkan kolom di bawah ini kosong jika Anda tidak bermaksud untuk mengganti kata sandi pengguna.
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Kata Sandi Baru</label>
                                    <input type="password" name="password" class="form-control input-style @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label-custom">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control input-style" placeholder="Ketik ulang kata sandi">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light text-right py-4" style="border-top: 1px solid var(--slate-100); border-radius: 0 0 16px 16px;">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-white border px-4 font-weight-bold" style="border-radius: 20px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 ml-2 font-weight-bold shadow-sm" style="border-radius: 20px;">
                            <i class="fas fa-save mr-1"></i> Perbarui Data Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection