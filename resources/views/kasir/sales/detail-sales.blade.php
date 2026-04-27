@extends('layouts.app')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="/sales" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Detail Penjualan</h2>
        <p class="text-sm text-slate-500">Invoice #{{ str_pad($stockMovement->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
    <!-- Invoice Info -->
    <div class="lg:col-span-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-flex rounded bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600 border border-emerald-100 uppercase tracking-wider">
                            {{ $stockMovement->status ?? 'Success' }}
                        </span>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">{{ $stockMovement->product->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $stockMovement->product->sku }} | {{ $stockMovement->product->category->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Waktu Transaksi</p>
                        <p class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($stockMovement->movement_date)->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <div class="overflow-hidden rounded-xl border border-slate-100">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 font-bold text-slate-600">
                            <tr>
                                <th class="px-6 py-4">Item</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Harga Satuan</th>
                                <th class="px-6 py-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $stockMovement->product->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $stockMovement->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-medium">{{ $stockMovement->quantity }} Pcs</td>
                                <td class="px-6 py-4 text-right">Rp {{ number_format($stockMovement->price_at_transaction ?? $stockMovement->product->unit_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    Rp {{ number_format($stockMovement->quantity * ($stockMovement->price_at_transaction ?? $stockMovement->product->unit_price), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-50/50 font-bold">
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-right text-slate-500 uppercase tracking-wider">Total Pembayaran</td>
                                <td class="px-6 py-6 text-right text-xl font-black text-secondary">
                                    Rp {{ number_format($stockMovement->quantity * ($stockMovement->price_at_transaction ?? $stockMovement->product->unit_price), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-8 rounded-xl bg-slate-50 p-6">
                    <h5 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Catatan Kasir</h5>
                    <p class="text-sm text-slate-600 italic">"{{ $stockMovement->note ?? 'Tidak ada catatan' }}"</p>
                </div>
            </div>
            
            <div class="border-t border-slate-100 bg-white px-8 py-6 flex justify-end gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    <i class="bi bi-printer"></i> Cetak Struk
                </button>
                <button class="inline-flex items-center gap-2 rounded-xl bg-secondary px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary">
                    <i class="bi bi-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Info -->
    <div class="lg:col-span-4 space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h5 class="font-bold text-slate-800 mb-4">Informasi Kasir</h5>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary">
                    <i class="bi bi-person-badge text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 uppercase">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </div>
        
        <div class="rounded-2xl border border-red-100 bg-red-50 p-6 shadow-sm">
            <h5 class="font-bold text-red-800 mb-2">Bantuan Transaksi?</h5>
            <p class="text-xs text-red-600 leading-relaxed mb-4">Jika terjadi kesalahan input, harap hubungi Manajer untuk proses pembatalan atau refund sesuai prosedur warehouse.</p>
            <button class="w-full rounded-xl bg-white border border-red-200 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                Laporkan Masalah
            </button>
        </div>
    </div>
</div>
@endsection