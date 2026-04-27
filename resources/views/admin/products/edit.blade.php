@extends('layouts.app')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="/products" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Edit Master Barang</h2>
        <p class="text-sm text-slate-500">Ubah atribut barang. SKU dan Sisa Stok tidak bisa diubah dari sini.</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <div class="lg:col-span-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/products/{{ $product->id }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Identitas Barang -->
                    <div>
                        <h5 class="mb-4 flex items-center gap-2 font-bold text-slate-900">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-xs text-secondary">1</span>
                            Identitas Barang
                        </h5>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">SKU (Read-only)</label>
                                    <input type="text" value="{{ $product->sku }}" class="w-full rounded-xl border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-500 font-mono" readonly disabled>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Barang</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</label>
                                    <select name="category_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Supplier (Opsional)</label>
                                    <select name="supplier_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                                        <option value="">-- Tanpa Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Harga & Batas -->
                    <div class="pt-4">
                        <h5 class="mb-4 flex items-center gap-2 font-bold text-slate-900">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-xs text-secondary">2</span>
                            Harga & Batas Gudang
                        </h5>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Safety Stock</label>
                                <input type="number" name="safety_stock" value="{{ old('safety_stock', $product->safety_stock) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Modal (Rp)</label>
                                    <input type="number" name="cost_price" value="{{ old('cost_price', floor($product->cost_price)) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual (Rp)</label>
                                    <input type="number" name="unit_price" value="{{ old('unit_price', floor($product->unit_price)) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-8">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary px-8 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                            <i class="bi bi-save"></i> 
                            Update Data Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
