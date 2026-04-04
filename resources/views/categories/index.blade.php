@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Kelola Kategori Barang</h2>
        <p class="text-muted">Manajemen pengelompokan jenis barang.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Kategori Baru</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="/categories" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Kategori</label>
                        <input type="text" name="name" class="form-control" required placeholder="Cth: Pakaian Pria">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Deskripsi Kategori (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-tag"></i> Simpan Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar Kategori</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kategori</th>
                                <th class="text-center">Jumlah Barang Terkait</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                            <tr>
                                <td class="ps-4">
                                    <strong>{{ $category->name }}</strong><br>
                                    <span class="small text-muted">{{ $category->description ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill">{{ $category->products_count }} Item</span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="/categories/{{ $category->id }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
