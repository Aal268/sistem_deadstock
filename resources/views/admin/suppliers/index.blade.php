@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Supplier (Pemasok)</h2>
    <p class="text-sm text-slate-500">Daftar pihak/toko asal barang gudang dibeli.</p>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <!-- Form Tambah Supplier -->
    <div class="lg:col-span-4">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h5 class="font-bold text-slate-800">Tambah Pemasok Baru</h5>
            </div>
            <div class="p-6">
                @if(session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="/suppliers" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Perusahaan / Supplier</label>
                        <input type="text" name="name" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required placeholder="Nama supplier">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Kontak (Opsional)</label>
                        <input type="text" name="contact_person" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" placeholder="Nama PIC">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Telepon / WA (Opsional)</label>
                        <input type="text" name="phone" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" placeholder="0812...">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Alamat (Opsional)</label>
                        <textarea name="address" rows="2" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" placeholder="Alamat kantor/toko"></textarea>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                        <i class="bi bi-truck text-base"></i> 
                        Simpan Pemasok
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tabel Daftar Supplier -->
    <div class="lg:col-span-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h5 class="font-bold text-slate-800">Daftar Pemasok</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Nama Supplier</th>
                            <th class="px-6 py-4">Kontak & Alamat</th>
                            <th class="px-6 py-4">Suplai</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $supplier)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $supplier->name }}</td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">{{ $supplier->contact_person ?? '-' }}</div>
                                <div class="text-xs text-secondary">{{ $supplier->phone ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $supplier->address ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                    {{ $supplier->products_count }} Barang
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="/suppliers/{{ $supplier->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-slate-400" {{ $supplier->products_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <i class="bi bi-truck text-4xl block mb-2 opacity-20"></i>
                                Belum ada data supplier.
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
