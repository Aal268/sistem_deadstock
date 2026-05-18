@extends('layouts.app')

@section('title', 'Data Stok Gudang - Deadstock Sys')

@section('content')
<div class="space-y-6 font-sans pb-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-primary">Data Stok Gudang</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">Daftar seluruh barang yang tersedia di gudang beserta status kuantitasnya.</p>
        </div>
    </div>

    <!-- Tabel Daftar Barang -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                Daftar Kuantitas Barang
            </h3>
            <a href="/products" class="inline-flex items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-primary/20 transition hover:bg-primary">
                <i class="fa-solid fa-gear"></i> Kelola Master Data
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-white text-xs uppercase text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider">SKU / Nama Barang</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Kategori</th>
                        <th class="px-6 py-4 font-bold text-center tracking-wider">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-bold text-center tracking-wider">Safety Stok</th>
                        <th class="px-6 py-4 font-bold text-center tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-bold text-slate-500">{{ $p->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $p->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-black {{ $p->current_stock == 0 ? 'bg-alert text-white' : ($p->current_stock <= $p->safety_stock ? 'bg-alert/10 text-alert' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $p->current_stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-400">
                                {{ $p->safety_stock }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->current_stock == 0)
                                    <span class="inline-flex rounded-full bg-alert/10 px-2.5 py-1 text-[10px] font-bold text-alert uppercase tracking-wider">Habis Total</span>
                                @elseif($p->current_stock <= $p->safety_stock)
                                    <span class="inline-flex rounded-full bg-alert/10 px-2.5 py-1 text-[10px] font-bold text-alert uppercase tracking-wider">Kritis</span>
                                @else
                                    <span class="inline-flex rounded-full bg-secondary/10 px-2.5 py-1 text-[10px] font-bold text-secondary uppercase tracking-wider">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                </div>
                                <p class="font-bold text-slate-800 text-lg">Belum Ada Data Barang</p>
                                <p class="text-sm text-slate-500 mt-1">Data inventaris gudang akan muncul di sini setelah produk ditambahkan.</p>
                                <a href="/products" class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm shadow-primary/20 transition hover:bg-secondary">
                                    <i class="fa-solid fa-plus"></i> Tambah Produk
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
