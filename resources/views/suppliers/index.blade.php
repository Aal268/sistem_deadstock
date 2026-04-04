@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Kelola Supplier (Pemasok)</h2>
        <p class="text-muted">Daftar pihak/toko asal barang gudang dibeli.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Tambah Pemasok Baru</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="/suppliers" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Perusahaan / Supplier</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Kontak (Opsional)</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Alamat (Opsional)</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-info text-white w-100">
                        <i class="bi bi-truck"></i> Simpan Pemasok
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar Pemasok</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Supplier</th>
                                <th>Kontak</th>
                                <th>Suplai</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $supplier->name }}</td>
                                <td>
                                    {{ $supplier->contact_person ? $supplier->contact_person . ' - ' : '' }}
                                    <span class="text-primary">{{ $supplier->phone ?? '-' }}</span><br>
                                    <span class="small text-muted">{{ $supplier->address }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $supplier->products_count }} Barang</span>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="/suppliers/{{ $supplier->id }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $supplier->products_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data supplier.</td>
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
