@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Master Barang</h2>
        <p class="text-sm text-slate-500">Daftar semua varian barang yang terdaftar di dalam sistem.</p>
    </div>
    <a href="/products/create" class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
        <i class="bi bi-plus-lg"></i> Tambah Barang Baru
    </a>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4 text-center">Sisa Stok</th>
                    <th class="px-6 py-4 text-right">Harga Beli</th>
                    <th class="px-6 py-4 text-right">Harga Jual</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $p)
                <tr class="transition hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $p->sku }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900">{{ $p->name }}</div>
                        <div class="text-xs text-slate-500">{{ $p->supplier->name ?? 'Tanpa Supplier' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                            {{ $p->category->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($p->current_stock <= $p->safety_stock)
                            <span class="font-bold text-red-600">{{ $p->current_stock }}</span>
                        @else
                            <span class="font-bold text-emerald-600">{{ $p->current_stock }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-slate-600 font-medium">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-slate-900 font-bold">Rp {{ number_format($p->unit_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="/products/{{ $p->id }}/edit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="/products/{{ $p->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus master data barang ini beserta seluruh nilai stoknya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <i class="bi bi-boxes text-4xl block mb-2 opacity-20"></i>
                        Master Data Barang masih kosong.<br>
                        <a href="/products/create" class="mt-4 inline-flex text-secondary font-bold hover:underline">Daftarkan Sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
