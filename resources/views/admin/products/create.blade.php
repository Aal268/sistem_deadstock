@extends('layouts.app')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="/products" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Barang Baru</h2>
        <p class="text-sm text-slate-500">Pastikan Anda sudah membuat Kategori terlebih dahulu.</p>
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

                <form action="/products" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Identitas Barang -->
                    <div>
                        <h5 class="mb-4 flex items-center gap-2 font-bold text-slate-900">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-xs text-secondary">1</span>
                            Identitas Barang
                        </h5>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Barang</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required placeholder="Cth: Kaos Polos Hitam XL">
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</label>
                                    <select name="category_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1.5 text-xs text-slate-400">
                                        Belum ada? <a href="/categories" class="font-bold text-secondary hover:underline">+ Buat Kategori</a>
                                    </p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Supplier (Opsional)</label>
                                    <select name="supplier_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok & Harga -->
                    <div class="pt-4">
                        <h5 class="mb-4 flex items-center gap-2 font-bold text-slate-900">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-secondary/10 text-xs text-secondary">2</span>
                            Stok & Harga
                        </h5>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Stok Awal</label>
                                    <input type="number" name="current_stock" value="{{ old('current_stock', 0) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                    <p class="mt-1 text-[10px] text-slate-400 uppercase font-bold tracking-tighter">Biarkan 0 jika belum ada fisik.</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Safety Stock</label>
                                    <input type="number" name="safety_stock" value="{{ old('safety_stock', 10) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                    <p class="mt-1 text-[10px] text-slate-400 uppercase font-bold tracking-tighter">Batas peringatan stok kritis.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Modal (Rp)</label>
                                    <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual (Rp)</label>
                                    <input type="number" name="unit_price" value="{{ old('unit_price', 0) }}" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-8">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary px-8 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                            <i class="bi bi-save"></i> 
                            Daftarkan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
