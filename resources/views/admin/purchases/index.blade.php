@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Pembelian Barang (Restock)</h2>
    <p class="text-sm text-slate-500">Catat barang masuk / restock dari supplier untuk menambah stok gudang.</p>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <!-- Form Penerimaan Barang -->
    <div class="lg:col-span-5">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-emerald-50/50 px-6 py-4">
                <h5 class="font-bold text-emerald-800">Form Kedatangan Barang</h5>
            </div>
            <div class="p-6">
                <form action="/purchases" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Pilih Barang yang Dibeli</label>
                        <select name="product_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Stok: {{ $p->current_stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Kuantitas (Pcs)</label>
                            <input type="number" name="quantity" min="1" value="1" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal Datang</label>
                            <input type="date" name="movement_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" required>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Catatan / Invoice Supplier</label>
                        <input type="text" name="note" class="w-full rounded-xl border border-primary px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10" placeholder="Opsional...">
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                        <i class="bi bi-box-arrow-in-down text-base"></i> 
                        Simpan Stok Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Histori Penerimaan -->
    <div class="lg:col-span-7">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h5 class="font-bold text-slate-800">Histori Penerimaan Barang Masuk</h5>
            </div>
            <!-- Filter Box -->
            <form method="GET" action="/purchases" class="border-b border-slate-100 bg-slate-50/50 p-5 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[140px]">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="SKU/Nama..." class="w-full rounded-xl border border-primary px-3 py-2 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <div class="flex-1 min-w-[130px]">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border border-primary px-3 py-2 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <div class="flex-1 min-w-[130px]">
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border border-primary px-3 py-2 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-secondary px-4 text-sm font-bold text-white transition hover:bg-primary shadow-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                        <a href="/purchases" class="inline-flex h-[42px] items-center justify-center rounded-xl bg-slate-200 px-3 text-sm font-bold text-slate-600 transition hover:bg-slate-300">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Barang</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentPurchases as $purchase)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($purchase->movement_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $purchase->product->sku ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $purchase->product->name ?? 'Produk Dihapus' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-emerald-600">+{{ $purchase->quantity }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600 italic">
                                {{ $purchase->note ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <i class="bi bi-clock-history text-4xl block mb-2 opacity-20"></i>
                                Belum ada histori barang masuk.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentPurchases->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">
                {{ $recentPurchases->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
