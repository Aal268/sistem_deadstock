@extends('layouts.app')

@section('content')
<div class="mb-8 flex items-center gap-4">
    <a href="/riwayat" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition hover:bg-slate-50 hover:text-slate-600">
        <i class="bi bi-arrow-left text-lg"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Edit Transaksi Penjualan</h2>
        <p class="text-sm text-slate-500">Ubah data riwayat transaksi dan penyesuaian stok otomatis.</p>
    </div>
</div>

<div class="mx-auto max-w-3xl">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-5">
            <h3 class="font-bold text-slate-800">Form Edit Transaksi #{{ str_pad($stockMovement->id, 6, '0', STR_PAD_LEFT) }}</h3>
        </div>
        
        <form action="{{ route('riwayat.update', $stockMovement->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Info Waktu (Readonly) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Waktu Transaksi</label>
                    <input type="text" readonly value="{{ \Carbon\Carbon::parse($stockMovement->movement_date)->format('d F Y, H:i') }} WIB" class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500 cursor-not-allowed outline-none">
                </div>

                <!-- Pilihan Produk -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="product_id" required class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $stockMovement->product_id == $p->id ? 'selected' : '' }}>
                                {{ $p->sku }} - {{ $p->name }} (Stok saat ini: {{ $p->current_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kuantitas -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Jumlah / Qty <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" min="1" required value="{{ old('quantity', $stockMovement->quantity) }}" class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                    <p class="mt-1 text-[10px] text-amber-600 font-medium italic">* Mengubah Qty akan memengaruhi jumlah stok riil di gudang.</p>
                    @error('quantity')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan</label>
                    <textarea name="note" rows="3" class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">{{ old('note', $stockMovement->note) }}</textarea>
                    @error('note')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="/riwayat" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-xl bg-secondary px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-secondary/30 transition hover:bg-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @if (session('success') || session('error'))
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                const message = @json(session('success') ?? session('error'));
                const type = @json(session('success') ? 'success' : 'error');

                const toast = document.createElement('div');
                toast.className = 'fixed top-6 right-6 z-50 max-w-sm rounded-xl border px-4 py-3 text-sm font-semibold shadow-lg transition-all duration-300';

                if (type === 'success') {
                    toast.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                } else {
                    toast.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                }

                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(function() {
                    toast.classList.add('opacity-0', 'translate-y-1');
                    setTimeout(function() {
                        toast.remove();
                    }, 300);
                }, 2500);
            });
        </script>
    @endif
@endpush
