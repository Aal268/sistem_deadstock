@extends('layouts.app')

@section('content')
<div class="space-y-6 font-sans pb-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-primary">Dashboard Gudang</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">Monitor stok dan kelola data master inventaris.</p>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-info shadow-sm">
            <i class="fa-solid fa-calendar-day text-primary"></i>
            <span class="text-sm font-bold text-slate-700">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-primary p-5 text-white shadow-lg shadow-primary/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Varian Produk</p>
                <p class="mt-2 text-4xl font-black">{{ $totalProducts }}</p>
                <p class="mt-1 text-xs text-white/60">Terdaftar di sistem</p>
            </div>
            <i class="fa-solid fa-box absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>

        <div class="rounded-2xl bg-primary p-5 text-white shadow-lg shadow-secondary/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Sisa Stok Total</p>
                <p class="mt-2 text-4xl font-black">{{ number_format($totalRemainingStock, 0, ',', '.') }} <span class="text-lg font-medium">Pcs</span></p>
                <p class="mt-1 text-xs text-white/60">Ketersediaan fisik</p>
            </div>
            <i class="fa-solid fa-warehouse absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>

        <div class="rounded-2xl bg-primary p-5 text-white shadow-lg shadow-tertiary/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Total Supplier</p>
                <p class="mt-2 text-4xl font-black">{{ $totalSuppliers }} <span class="text-lg font-medium">Vendor</span></p>
                <p class="mt-1 text-xs text-white/60">Supplier aktif</p>
            </div>
            <i class="fa-solid fa-truck-field absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>

        <div class="rounded-2xl bg-alert p-5 text-white shadow-lg shadow-alert/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Stok Kritis</p>
                <p class="mt-2 text-4xl font-black">{{ $criticalStockItems }} <span class="text-lg font-medium">Item</span></p>
                <p class="mt-1 text-xs text-white/60">Segera restock</p>
            </div>
            <i class="fa-solid fa-triangle-exclamation absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Critical Inventory Table -->
        <div class="rounded-2xl border border-info bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-alert"></i>
                    <h3 class="font-bold text-slate-800">Daftar Stok Kritis</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3 text-center">Stok Saat Ini</th>
                            <th class="px-6 py-3 text-center">Safety Stock</th>
                            <th class="px-6 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @php
                            $criticalItems = $products->filter(fn($p) => $p->current_stock <= $p->safety_stock)->take(10);
                        @endphp
                        @forelse($criticalItems as $p)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $p->name }}</p>
                                <p class="text-[10px] text-slate-400">SKU: {{ $p->sku }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-slate-700">{{ $p->current_stock }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-500 font-medium">
                                {{ $p->safety_stock }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($p->current_stock == 0)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-alert/10 text-alert">HABIS TOTAL</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-alert/10 text-alert">KRITIS</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                <i class="fa-solid fa-check-circle text-primary mr-2"></i>
                                Semua stok dalam kondisi aman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($criticalItems->count() > 0)
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-center text-[10px] text-slate-400 font-medium">
                Hanya menampilkan maksimal 10 item paling kritis
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <div class="rounded-2xl border-2 border-solid border-info p-6 text-center bg-white">
                <p class="text-sm font-bold text-primary">Manajemen Data & Operasional</p>
                <p class="mt-1 text-xs text-slate-500">Pilih menu di bawah ini untuk mengelola aset inventaris</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Products -->
                <a href="/products" class="group p-4 rounded-2xl border border-info bg-white hover:bg-primary transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 group-hover:bg-white/20 flex items-center justify-center text-primary group-hover:text-white transition">
                            <i class="fa-solid fa-box-open text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-white transition">Data Produk</p>
                            <p class="text-[10px] text-slate-500 group-hover:text-white/70 transition">Atur katalog barang</p>
                        </div>
                    </div>
                </a>

                <!-- Categories -->
                <a href="/categories" class="group p-4 rounded-2xl border border-info bg-white hover:bg-secondary transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-secondary/10 group-hover:bg-white/20 flex items-center justify-center text-secondary group-hover:text-white transition">
                            <i class="fa-solid fa-tags text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-white transition">Kategori</p>
                            <p class="text-[10px] text-slate-500 group-hover:text-white/70 transition">Kelompokkan barang</p>
                        </div>
                    </div>
                </a>

                <!-- Suppliers -->
                <a href="/suppliers" class="group p-4 rounded-2xl border border-info bg-white hover:bg-tertiary transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-tertiary/10 group-hover:bg-white/20 flex items-center justify-center text-tertiary group-hover:text-white transition">
                            <i class="fa-solid fa-handshake text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-white transition">Supplier</p>
                            <p class="text-[10px] text-slate-500 group-hover:text-white/70 transition">Daftar mitra vendor</p>
                        </div>
                    </div>
                </a>

                <!-- Purchases -->
                <a href="/purchases" class="group p-4 rounded-2xl border border-info bg-white hover:bg-accent transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-accent/10 group-hover:bg-white/20 flex items-center justify-center text-accent group-hover:text-white transition">
                            <i class="fa-solid fa-truck-loading text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-white transition">Input Pembelian</p>
                            <p class="text-[10px] text-slate-500 group-hover:text-white/70 transition">Catat stok masuk</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
