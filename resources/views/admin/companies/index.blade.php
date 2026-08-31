@extends('layouts.admin')

@section('title', 'Pengelolaan Perusahaan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Perusahaan</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Perusahaan Terdaftar</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Perusahaan</th>
                    <th>Pemilik</th>
                    <th>Industri</th>
                    <th>Verifikasi</th>
                    <th>Lowongan</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    <td>{{ $company->company_name }}</td>
                    <td>{{ $company->user->name ?? 'N/A' }}</td>
                    <td>{{ $company->industry }}</td>
                    <td>
                        <span class="badge badge-{{ $company->is_verified ? 'success' : 'warning' }}">
                            {{ $company->is_verified ? 'Terverifikasi' : 'Menunggu' }}
                        </span>
                    </td>
                    <td>{{ $company->jobs_count ?? 0 }}</td>
                    <td>{{ $company->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus perusahaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix d-flex justify-content-center">
        {{ $companies->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
