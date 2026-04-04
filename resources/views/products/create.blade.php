@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex align-items-center">
        <a href="/products" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Kembali</a>
        <div>
            <h2 class="mb-0">Pendaftaran Barang Baru</h2>
            <p class="text-muted mb-0">SKU akan otomatis dibuatkan oleh sistem. Pastikan Anda sudah membuat Kategori terlebih dahulu.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/products" method="POST">
                    @csrf
                    
                    <h5 class="mb-3 text-primary border-bottom pb-2">Identitas Barang</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Barang</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text"><a href="/categories" class="text-decoration-none">+ Buat Kategori Baru</a></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Supplier (Opsional)</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary border-bottom pb-2 mt-4">Stok & Harga</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Stok Awal di Gudang</label>
                            <input type="number" name="current_stock" class="form-control" value="{{ old('current_stock', 0) }}" min="0" required>
                            <div class="form-text">Biarkan 0 jika belum ada fisik barangnya.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Safety Stock (Batas Kritis)</label>
                            <input type="number" name="safety_stock" class="form-control" value="{{ old('safety_stock', 10) }}" min="0" required>
                            <div class="form-text">Sistem akan memberi peringatan jika stok di bawah angka ini.</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Harga Modal / Beli (Rp)</label>
                            <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price', 0) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Harga Jual (Rp)</label>
                            <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price', 0) }}" min="0" required>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5 py-2">
                            <i class="bi bi-save me-2"></i> Daftarkan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
