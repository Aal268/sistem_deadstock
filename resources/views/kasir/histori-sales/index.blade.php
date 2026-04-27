@extends('layouts.app')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Penjualan</h2>
    <p class="text-sm text-slate-500">Daftar lengkap seluruh transaksi barang keluar.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">Produk</th>
                    <th class="px-6 py-4">Catatan</th>
                    <th class="px-6 py-4 text-center">Qty</th>
                    <th class="px-6 py-4 text-right">Harga</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($allSales as $sale)
                @php 
                    $subtotal = $sale->quantity * ($sale->price_at_transaction ?? $sale->product->unit_price);
                @endphp
                <tr class="transition hover:bg-slate-50/50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($sale->movement_date)->format('d M Y') }}</div>
                        <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($sale->movement_date)->format('H:i') }} WIB</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900">{{ $sale->product->sku ?? '-' }}</div>
                        <div class="text-[10px] uppercase font-bold text-secondary">{{ $sale->product->category->name ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $sale->product->name ?? 'Produk Dihapus' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-slate-500 italic line-clamp-2 max-w-[150px]">
                            {{ $sale->note ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-red-600">-{{ $sale->quantity }}</td>
                    <td class="px-6 py-4 text-right">Rp {{ number_format($sale->price_at_transaction ?? $sale->product->unit_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right font-black text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 border border-emerald-100">
                            {{ $sale->status ?? 'Success' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('sales.show', $sale->id) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">Belum ada data transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($allSales->hasPages())
    <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">
        {{ $allSales->links() }}
    </div>
    @endif
</div>
@endsection