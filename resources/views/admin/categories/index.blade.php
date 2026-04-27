@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Kategori Barang</h2>
    <p class="text-sm text-slate-500">Manajemen pengelompokan jenis barang.</p>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <!-- Form Tambah Kategori -->
    <div class="lg:col-span-4">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h5 class="font-bold text-slate-800">Tambah Kategori Baru</h5>
            </div>
            <div class="p-6">
                @if(session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="/categories" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Kategori</label>
                        <input type="text" name="name" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required placeholder="Cth: Pakaian Pria">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" placeholder="Penjelasan singkat kategori"></textarea>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                        <i class="bi bi-tag text-base"></i> 
                        Simpan Kategori
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tabel Daftar Kategori -->
    <div class="lg:col-span-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h5 class="font-bold text-slate-800">Daftar Kategori</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Jumlah Barang</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($categories as $category)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $category->name }}</div>
                                <div class="text-xs text-slate-500">{{ $category->description ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                    {{ $category->products_count }} Item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="/categories/{{ $category->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-slate-400" {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                <i class="bi bi-inbox text-4xl block mb-2 opacity-20"></i>
                                Belum ada data kategori.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
