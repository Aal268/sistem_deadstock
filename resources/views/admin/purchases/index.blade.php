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
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h5 class="font-bold text-slate-800">10 Histori Penerimaan Terakhir</h5>
            </div>
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
        </div>
    </div>
</div>
@endsection
