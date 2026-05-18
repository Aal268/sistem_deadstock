@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kasir Penjualan</h2>
        <p class="text-sm text-slate-500">Proses transaksi kasir dan kurangi stok barang yang terjual secara real-time.</p>
    </div>

    <div x-data="posSystem()" class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Form Input Penjualan -->
        <div class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm sticky top-8">
                <div class="rounded-t-2xl border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h5 class="font-bold text-slate-800">Form Input Penjualan</h5>
                </div>
                <div class="p-6">
                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div
                            class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="/sales" method="POST" class="space-y-5">
                        @csrf
                        <div class="relative">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Cari
                                Produk / SKU</label>

                            <div class="relative">
                                <input type="text" x-model="search" @click.away="showDropdown = false"
                                    @focus="showDropdown = true" @keydown.escape="showDropdown = false"
                                    placeholder="Ketik nama atau scan SKU..." autocomplete="off"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                                <input type="hidden" name="product_id" :value="selectedProduct?.id" required>
                            </div>

                            <div x-show="showDropdown && filteredProducts.length > 0" x-cloak
                                class="absolute z-50 mt-2 w-full max-h-60 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl animate-fade-in"
                                x-transition>
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <div @click="selectProduct(product)"
                                        class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-slate-50 border-b border-slate-50 last:border-0">
                                        <div>
                                            <div class="text-sm font-bold text-slate-800" x-text="product.name"></div>
                                            <span
                                                class="inline-block text-[10px] font-extrabold uppercase tracking-tight text-secondary bg-secondary/10 px-1.5 py-0.5 rounded"
                                                x-text="product.sku"></span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[11px] font-bold"
                                                :class="product.stock < 5 ? 'text-red-500' : 'text-emerald-500'">
                                                Stok: <span x-text="product.stock"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="selectedProduct" x-cloak
                            class="mt-4 space-y-2 p-3 rounded-xl bg-slate-50 border border-slate-100 animate-fade-in">
                            <div class="flex justify-between text-[10px] font-bold tracking-widest">
                                <span class="text-slate-400">Kategori: <span class="text-secondary"
                                        x-text="selectedProduct?.category"></span></span>
                                <span class="text-slate-400">Stok: <span
                                        :class="selectedProduct?.stock < 5 ? 'text-red-600' : 'text-emerald-600'"
                                        x-text="selectedProduct?.stock"></span></span>
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 tracking-widest">
                                Harga Satuan: <span class="text-slate-900">Rp <span
                                        x-text="formatRupiah(selectedProduct?.price)"></span></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold tracking-wider text-slate-500">Jumlah
                                    (Pcs)</label>
                                <input type="number" name="quantity" x-model="qty" min="1"
                                    :max="selectedProduct?.stock"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold tracking-wider text-slate-500">Tanggal</label>
                                <input type="date" name="movement_date" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                            </div>
                        </div>

                        <div x-show="selectedProduct" x-cloak class="rounded-xl bg-secondary/5 p-4 border border-secondary/20">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-500">Estimasi Total</span>
                                <span class="text-lg font-black text-secondary">Rp <span
                                        x-text="formatRupiah(totalPrice)"></span></span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold tracking-wider text-slate-500">Catatan Penjualan (Opsional)</label>
                            <input type="text" name="note" placeholder="Contoh: Titipan pelanggan, dll"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                        </div>

                        <button type="submit" :disabled="!selectedProduct || qty > selectedProduct.stock"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary active:scale-[0.98] disabled:opacity-50">
                            <i class="bi bi-cart-check text-base"></i>
                            Proses Penjualan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Histori Transaksi -->
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-white px-6 py-4 flex justify-between items-center">
                    <h5 class="font-bold text-slate-800">Transaksi Terakhir</h5>
                    <a href="/histori-sales" class="text-xs font-bold text-secondary hover:underline">Lihat Semua
                        Riwayat</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Barang</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Harga</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $totalDaily = 0; @endphp
                            @forelse($recentSales as $sale)
                                @php
                                    $subtotal =
                                        $sale->quantity * ($sale->price_at_transaction ?? $sale->product->unit_price);
                                    $totalDaily += $subtotal;
                                @endphp
                                <tr class="transition hover:bg-slate-50/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-[11px]">
                                        <div class="font-bold text-slate-900">
                                            {{ \Carbon\Carbon::parse($sale->movement_date)->format('d M Y') }}</div>
                                        <div class="text-slate-400">
                                            {{ \Carbon\Carbon::parse($sale->movement_date)->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $sale->product->sku ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[200px]">
                                            {{ $sale->product->name ?? 'Produk Dihapus' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-red-600">-{{ $sale->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-slate-600">Rp
                                        {{ number_format($sale->price_at_transaction ?? $sale->product->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-slate-900">Rp
                                        {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sales.show', $sale->id) }}"
                                            class="p-2 text-slate-400 hover:text-secondary transition"><i
                                                class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada
                                        transaksi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        function posSystem() {
            return {
                search: '',
                qty: 1,
                showDropdown: false,
                selectedProduct: null,
                // Ambil data produk dari Laravel
                products: {!! $products->map(
                        fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'sku' => $p->sku,
                            'price' => (int) $p->unit_price,
                            'stock' => (int) $p->current_stock,
                            'category' => $p->category->name ?? '-',
                        ],
                    )->toJson() !!},

                get filteredProducts() {
                    if (this.search === '') return [];
                    return this.products.filter(p =>
                        p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.sku.toLowerCase().includes(this.search.toLowerCase())
                    ).slice(0, 8); // Batasi hasil agar tidak kepanjangan
                },

                get totalPrice() {
                    return this.selectedProduct ? this.selectedProduct.price * this.qty : 0;
                },

                selectProduct(product) {
                    this.selectedProduct = product;
                    this.search = product.sku + ' - ' + product.name;
                    this.showDropdown = false;
                    if (this.qty > product.stock) this.qty = product.stock;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }
    </script>
@endpush
