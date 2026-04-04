@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex align-items-center">
        <a href="/products" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i> Batal</a>
        <div>
            <h2 class="mb-0">Edit Master Barang</h2>
            <p class="text-muted mb-0">Ubah atribut barang. SKU dan Sisa Stok Gudang tidak bisa diubah dari sini.</p>
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

                <form action="/products/{{ $product->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-3 text-primary border-bottom pb-2">Identitas Barang</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">SKU (Kode Barang)</label>
                        <input type="text" class="form-control bg-light" value="{{ $product->sku }}" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Barang</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Supplier (Opsional)</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">-- Tanpa Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 text-primary border-bottom pb-2 mt-4">Manajemen Harga & Batas Gudang</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Safety Stock (Batas Kritis Gudang)</label>
                        <input type="number" name="safety_stock" class="form-control" value="{{ old('safety_stock', $product->safety_stock) }}" min="0" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Harga Modal (Rp)</label>
                            <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price', floor($product->cost_price)) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Harga Jual (Rp)</label>
                            <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price', floor($product->unit_price)) }}" min="0" required>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-5 py-2">
                            <i class="bi bi-save me-2"></i> Update Data Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
