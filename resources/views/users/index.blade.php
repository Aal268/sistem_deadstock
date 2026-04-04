@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Kelola Karyawan & Akun</h2>
        <p class="text-muted">Pusat manajemen hak akses pengguna sistem.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Tambah Akun Baru</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="/users" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Email Login</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Password Login</label>
                        <input type="password" name="password" class="form-control" minlength="8" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Tipe Akun (Role)</label>
                        <select name="role" class="form-select" required>
                            <option value="administrator">Kasir / Administrator</option>
                            <option value="admin">Manajer / Admin Owner</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-person-plus"></i> Simpan Pengguna Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar Pengguna Sistem</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama</th>
                                <th>Email</th>
                                <th>Role Akses</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="badge bg-primary ms-1">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin (Manajer)</span>
                                    @else
                                        <span class="badge bg-info text-dark">Administrator (Kasir)</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if(auth()->id() !== $user->id)
                                    <form action="/users/{{ $user->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
