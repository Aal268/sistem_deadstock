@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Analisis & Rekomendasi Restock</h2>
    <p class="text-sm text-slate-500">Daftar rekomendasi ini dihasilkan dengan membandingkan rata-rata penjualan per bulan terhadap sisa stok saat ini.</p>
</div>

<!-- Filter Box -->
<form method="GET" action="/analysis" class="mb-6 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex-1 min-w-[200px]">
        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Pencarian Produk</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau SKU..." class="w-full rounded-xl border border-primary px-4 py-2.5 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Filter Supplier</label>
        <select name="supplier_id" class="w-full rounded-xl border border-primary px-4 py-2.5 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
            <option value="">Semua Supplier</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Status Velocity</label>
        <select name="status" class="w-full rounded-xl border border-primary px-4 py-2.5 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
            <option value="">Semua Status</option>
            <option value="Fast-Moving" {{ request('status') == 'Fast-Moving' ? 'selected' : '' }}>Fast-Moving</option>
            <option value="Slow-Moving" {{ request('status') == 'Slow-Moving' ? 'selected' : '' }}>Slow-Moving</option>
            <option value="Deadstock" {{ request('status') == 'Deadstock' ? 'selected' : '' }}>Deadstock</option>
            <option value="Normal" {{ request('status') == 'Normal' ? 'selected' : '' }}>Normal</option>
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-secondary px-5 text-sm font-bold text-white transition hover:bg-primary shadow-sm">
            <i class="bi bi-funnel"></i> Filter
        </button>
        @if(request()->anyFilled(['search', 'supplier_id', 'status']))
            <a href="/analysis" class="inline-flex h-[42px] items-center justify-center rounded-xl bg-slate-100 px-4 text-sm font-bold text-slate-500 transition hover:bg-slate-200">
                Reset
            </a>
        @endif
    </div>
</form>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-4">SKU - Nama Barang</th>
                    <th class="px-6 py-4">Supplier</th>
                    <th class="px-6 py-4 text-center">Sisa Stok</th>
                    <th class="px-6 py-4 text-center">Safety Stok</th>
                    <th class="px-6 py-4 text-center">Rata-rata Terjual/Bulan <br>(dalam Desimal)</th>
                    <th class="px-6 py-4 text-center">Status Velocity</th>
                    <th class="px-6 py-4 text-center">Saran Pembelian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($analysis as $item)
                @php
                    // Map Bootstrap classes to Tailwind
                    $statusColor = 'bg-slate-100 text-slate-600';
                    if (str_contains($item['bg_color'], 'bg-success')) $statusColor = 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                    if (str_contains($item['bg_color'], 'bg-primary')) $statusColor = 'bg-blue-50 text-blue-700 border border-blue-100';
                    if (str_contains($item['bg_color'], 'bg-warning')) $statusColor = 'bg-amber-50 text-amber-700 border border-amber-100';
                    if (str_contains($item['bg_color'], 'bg-danger')) $statusColor = 'bg-red-50 text-red-700 border border-red-100';
                @endphp
                <tr class="transition hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <div class="font-mono text-xs text-slate-500">{{ $item['product']->sku }}</div>
                        <div class="font-bold text-slate-900">{{ $item['product']->name }}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $item['product']->supplier->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center font-bold">
                        @if($item['product']->current_stock <= $item['product']->safety_stock)
                            <span class="text-red-600">{{ $item['product']->current_stock }}</span>
                        @else
                            <span class="text-slate-700">{{ $item['product']->current_stock }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-slate-400 font-medium">{{ $item['product']->safety_stock }}</td>
                    <td class="px-6 py-4 text-center font-black text-slate-900">{{ round($item['avg_monthly_sales']) }} Pcs / Bulan</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-tighter {{ $statusColor }}">
                            {{ $item['status'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($item['suggested_buy'] > 0)
                            <div class="inline-flex flex-col items-center">
                                <span class="rounded-lg bg-emerald-600 px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                                    BELI +{{ $item['suggested_buy'] }} PCS
                                </span>
                                <div class="mt-1 text-[10px] font-medium text-slate-500 uppercase tracking-tight">{{ $item['recommendation_text'] }}</div>
                            </div>
                        @elseif($item['status'] == 'Deadstock')
                            <div class="inline-flex flex-col items-center">
                                <span class="rounded-lg bg-red-600 px-4 py-1.5 text-[10px] font-bold text-white shadow-sm uppercase tracking-widest">
                                    STOP PEMBELIAN
                                </span>
                                <div class="mt-1 text-[10px] font-bold text-red-500 uppercase">Stok Mandek (Obral)</div>
                            </div>
                        @else
                            <div class="inline-flex flex-col items-center">
                                <span class="rounded-lg bg-slate-100 px-4 py-1.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                    TIDAK PERLU
                                </span>
                                <div class="mt-1 text-[10px] font-medium text-slate-400 uppercase">Stok Masih Cukup</div>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 bg-white px-6 py-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
