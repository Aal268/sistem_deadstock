@extends('layouts.app')

@section('title', 'Dashboard Gudang - Deadstock Sys')

@section('content')
<div class="space-y-5 font-sans">
    <div>
        <h2 class="text-3xl font-black tracking-tight text-[#13505B]">Dashboard Gudang</h2>
        <p class="mt-1 text-sm font-medium text-slate-500">Ringkasan stok barang dan manajemen inventaris gudang.</p>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-xl bg-primary p-4 text-white hover:bg-secondary transition shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-white/90">Varian Produk</p>
                    <p class="mt-1.5 text-4xl font-black leading-none">{{ $totalProducts }}</p>
                </div>
                <i class="bi bi-box-seam text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="rounded-xl bg-primary p-4 text-white hover:bg-secondary transition shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-white/90">Sisa Total Stok</p>
                    <p class="mt-1.5 text-4xl font-black leading-none">{{ $totalRemainingStock }} <span class="ml-1 text-lg font-bold">Pcs</span></p>
                </div>
                <i class="bi bi-boxes text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="rounded-xl {{ $criticalStockItems > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-secondary' }} p-4 text-white transition shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium text-white/90">Peringatan Stok Kritis</p>
                    <p class="mt-1.5 text-4xl font-black leading-none">{{ $criticalStockItems }} <span class="ml-1 text-lg font-bold">Item</span></p>
                </div>
                <i class="bi bi-exclamation-triangle text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        <div class="rounded-xl border border-info bg-white p-5 shadow-sm flex items-start gap-4">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 shrink-0">
                <i class="bi bi-database-add text-2xl"></i>
            </div>
            <div>
                <h5 class="text-lg font-bold text-slate-800">Manajemen Barang</h5>
                <p class="mt-1 text-sm text-slate-500">Tambah produk baru, ubah kategori, atau atur supplier.</p>
                <a href="/products" class="mt-3 inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary">Kelola Barang</a>
            </div>
        </div>

        <div class="rounded-xl border border-info bg-white p-5 shadow-sm flex items-start gap-4">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600 shrink-0">
                <i class="bi bi-box-arrow-in-down text-2xl"></i>
            </div>
            <div>
                <h5 class="text-lg font-bold text-slate-800">Penerimaan Barang</h5>
                <p class="mt-1 text-sm text-slate-500">Catat histori barang masuk dari supplier ke dalam sistem.</p>
                <a href="/purchases" class="mt-3 inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary">Form Barang Masuk</a>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Barang -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-[#13505B]">Daftar Kuantitas Barang</h3>
            <a href="/products" class="text-sm text-secondary hover:text-primary font-medium flex items-center gap-1">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">SKU / Nama Barang</th>
                        <th class="px-5 py-3 font-semibold">Kategori</th>
                        <th class="px-5 py-3 font-semibold text-center">Stok Saat Ini</th>
                        <th class="px-5 py-3 font-semibold text-center">Safety Stok</th>
                        <th class="px-5 py-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                <div class="text-xs text-slate-500">{{ $p->sku }}</div>
                            </td>
                            <td class="px-5 py-3">{{ $p->category->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-semibold {{ $p->current_stock <= $p->safety_stock ? 'text-red-600' : 'text-slate-900' }}">
                                    {{ $p->current_stock }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center text-slate-500">
                                {{ $p->safety_stock }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($p->current_stock == 0)
                                    <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">Habis</span>
                                @elseif($p->current_stock <= $p->safety_stock)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Kritis</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
