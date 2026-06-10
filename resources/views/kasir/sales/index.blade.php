@extends('layouts.app')

@push('styles')
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.complete.css" rel="stylesheet">
    <style>
        /* Tom Select Premium Overrides */
        .ts-wrapper .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.625rem 1rem !important;
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 44px;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #119DA4 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(17, 157, 164, 0.1) !important;
        }

        .ts-dropdown {
            border-radius: 0.75rem !important;
            margin-top: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            z-index: 1000 !important;
            overflow: hidden;
            animation: dropdownSlide 0.15s ease-out;
        }

        @keyframes dropdownSlide {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .ts-dropdown .option {
            padding: 0.625rem 1rem !important;
            transition: background 0.15s;
        }

        .ts-dropdown .option:hover,
        .ts-dropdown .active {
            background-color: #f1f5f9 !important;
            color: inherit !important;
        }

        .ts-wrapper { width: 100% !important; }

        .ts-wrapper .ts-control input {
            font-size: 0.875rem !important;
            color: #1e293b !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: 100% !important;
        }

        .ts-wrapper .ts-control .placeholder {
            color: #94a3b8 !important;
            font-size: 0.875rem !important;
        }

        .sku-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #119DA4;
            background: rgba(17, 157, 164, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 3px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in { animation: fadeIn 0.25s ease-out; }

        .ts-wrapper .ts-control::after { display: none !important; }
        .ts-wrapper .ts-control { padding-right: 1rem !important; }

        #product-select {
            position: absolute !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            pointer-events: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Kasir Penjualan</h2>
        <p class="text-sm text-slate-500">Proses transaksi kasir, input banyak barang sekaligus, dan kurangi stok secara real-time.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Form Input Penjualan -->
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden lg:sticky lg:top-8">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h5 class="font-bold text-slate-800">Form Input Penjualan</h5>
                </div>
                <div class="p-6">
                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="/sales" method="POST" id="sales-form" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold tracking-wider text-slate-500">Tanggal</label>
                                <input type="date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10"
                                    required>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-bold tracking-wider text-slate-500">Catatan Penjualan (Opsional)</label>
                                <input type="text" name="note" value="{{ old('note') }}" placeholder="Contoh: Titipan pelanggan, dll"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Daftar Barang</label>
                                    <p class="mt-1 text-xs text-slate-400">Tambahkan banyak item sekaligus seperti POS.</p>
                                </div>
                                <button type="button" id="add-sale-item"
                                    class="inline-flex items-center gap-2 rounded-xl border border-secondary/20 bg-secondary/5 px-4 py-2 text-xs font-bold text-secondary transition hover:bg-secondary/10">
                                    <i class="bi bi-plus-lg"></i>
                                    Tambah Barang
                                </button>
                            </div>

                            <div id="sale-items" style="max-height: 380px; overflow-y: auto; padding-right: 4px;" class="space-y-4"></div>
                        </div>

                        <div class="rounded-xl bg-secondary/5 p-4 border border-secondary/20">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Estimasi Total</span>
                                <span class="text-lg font-black text-secondary">Rp <span id="grand-total">0</span></span>
                            </div>
                        </div>

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-secondary px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition hover:bg-primary active:scale-[0.98]">
                            <i class="bi bi-cart-check text-base"></i>
                            Proses Penjualan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Histori Transaksi -->
        <div class="lg:col-span-7">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 bg-white px-6 py-4 flex justify-between items-center">
                    <h5 class="font-bold text-slate-800">Transaksi Terakhir</h5>
                    <a href="/histori-sales" class="text-xs font-bold text-secondary hover:underline">Lihat Semua Riwayat</a>
                </div>

                <!-- Filter Box -->
                <form method="GET" action="{{ request()->url() }}" class="border-b border-slate-100 bg-slate-50/50 p-5 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="mb-1 block text-xs font-bold tracking-wider text-slate-500">Pencarian Produk</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama barang atau SKU..."
                            class="w-full rounded-xl border border-primary px-3 py-2 text-sm outline-none transition focus:border-secondary focus:ring-1 focus:ring-secondary">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-secondary px-4 text-sm font-bold text-white transition hover:bg-primary shadow-sm">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search']))
                            <a href="{{ request()->url() }}"
                                class="inline-flex h-[42px] items-center justify-center rounded-xl bg-slate-100 px-3 text-sm font-bold text-slate-500 transition hover:bg-slate-200">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-bold tracking-wider text-slate-500">
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
                                    $subtotal = $sale->quantity * ($sale->price_at_transaction ?? $sale->product->unit_price);
                                    $totalDaily += $subtotal;
                                @endphp
                                <tr class="transition hover:bg-slate-50/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-[11px]">
                                        <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($sale->movement_date)->format('d M Y') }}</div>
                                        <div class="text-slate-400">{{ \Carbon\Carbon::parse($sale->movement_date)->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $sale->product->sku ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[200px]">{{ $sale->product->name ?? 'Produk Dihapus' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-red-600">-{{ $sale->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-slate-600">Rp {{ number_format($sale->price_at_transaction ?? $sale->product->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right font-black text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="p-2 text-slate-400 hover:text-secondary transition">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                                        @if(request()->anyFilled(['search']))
                                            Tidak ada transaksi yang sesuai filter.
                                        @else
                                            Belum ada transaksi hari ini.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($recentSales, 'hasPages') && $recentSales->hasPages())
<div class="border-t border-slate-100 bg-slate-50/50 px-6 py-4">
    {{ $recentSales->links() }}
</div>
@endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saleItemsContainer = document.getElementById('sale-items');
            const addItemButton = document.getElementById('add-sale-item');
            const grandTotalEl = document.getElementById('grand-total');

            const formatRupiah = (n) => new Intl.NumberFormat('id-ID').format(n);

            const productData = {!! $products->mapWithKeys(fn($p) => [
                $p->id => [
                    'id'       => $p->id,
                    'price'    => (int) $p->unit_price,
                    'stock'    => (int) $p->current_stock,
                    'category' => $p->category->name ?? '-',
                    'sku'      => $p->sku,
                    'name'     => $p->name,
                    'text'     => $p->sku . ' - ' . $p->name,
                ],
            ])->toJson() !!};

            const productOptions = Object.values(productData);
            const initialItems = @json(old('products', []));
            let rowId = 0;

            const tomSelectConfig = {
                options: productOptions,
                valueField: 'id',
                labelField: 'text',
                searchField: ['name', 'sku'],
                create: false,
                maxOptions: 50,
                placeholder: 'Ketik nama produk atau SKU...',
                openOnFocus: false,
                render: {
                    option: function(data, escape) {
                        const sku   = escape(data.sku || '');
                        const name  = escape(data.name || data.text || '');
                        const stock = parseInt(data.stock) || 0;
                        const stockColor = stock < 5 ? '#ef4444' : '#10b981';
                        return `
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                                <div>
                                    <div style="font-weight:700; font-size:0.875rem; color:#1e293b; line-height:1.4;">${name}</div>
                                    <span class="sku-badge">${sku}</span>
                                </div>
                                <div style="font-size:11px; font-weight:700; color:${stockColor}; white-space:nowrap; text-align:right;">
                                    Stok: ${stock}
                                </div>
                            </div>
                        `;
                    },
                    item: function(data, escape) {
                        const raw  = productData[data.value];
                        const sku  = raw ? escape(raw.sku) : '';
                        const name = raw ? escape(raw.name) : escape(data.text);
                        return `<div style="font-size:0.875rem; font-weight:600; color:#334155;">${sku} – ${name}</div>`;
                    },
                    no_results: function() {
                        return `<div style="padding:0.75rem 1rem; font-size:0.875rem; color:#94a3b8; font-style:italic;">Produk tidak ditemukan...</div>`;
                    }
                },
            };

            function updateGrandTotal() {
                let total = 0;

                saleItemsContainer.querySelectorAll('[data-sale-item]').forEach((row) => {
                    const price = parseInt(row.dataset.price || '0');
                    const quantityInput = row.querySelector('[data-quantity-input]');
                    const subtotalEl = row.querySelector('[data-item-subtotal]');
                    const quantity = Math.max(1, parseInt(quantityInput.value || '1'));
                    const subtotal = price * quantity;

                    subtotalEl.textContent = formatRupiah(subtotal);
                    total += subtotal;
                });

                grandTotalEl.textContent = formatRupiah(total);
            }

            function refreshRowLabels() {
                const rows = saleItemsContainer.querySelectorAll('[data-sale-item]');

                rows.forEach((row, index) => {
                    row.querySelector('[data-row-label]').textContent = `Barang ${index + 1}`;
                    row.querySelector('[data-remove-item]').classList.toggle('hidden', rows.length === 1);
                });
            }

            function updateRowInfo(row, productId) {
                const info = row.querySelector('[data-product-info]');
                const categoryEl = row.querySelector('[data-info-category]');
                const stockEl = row.querySelector('[data-info-stock]');
                const priceEl = row.querySelector('[data-info-price]');
                const qtyInput = row.querySelector('[data-quantity-input]');

                if (!productId || !productData[productId]) {
                    row.dataset.price = '0';
                    row.dataset.stock = '0';
                    info.classList.add('hidden');
                    qtyInput.max = 999999;
                    updateGrandTotal();
                    return;
                }

                const data = productData[productId];
                row.dataset.price = data.price;
                row.dataset.stock = data.stock;

                categoryEl.textContent = data.category;
                stockEl.textContent = formatRupiah(data.stock) + ' Pcs';
                stockEl.className = data.stock < 5 ? 'text-red-600 font-black' : 'text-emerald-600 font-bold';
                priceEl.textContent = formatRupiah(data.price);
                qtyInput.max = data.stock;
                info.classList.remove('hidden');
                info.classList.add('animate-fade-in');

                if (parseInt(qtyInput.value || '1') > data.stock) {
                    qtyInput.value = data.stock > 0 ? data.stock : 1;
                }

                updateGrandTotal();
            }

            function createSaleRow(initialData = {}) {
                const row = document.createElement('div');
                row.dataset.saleItem = 'true';
                row.dataset.price = '0';
                row.dataset.stock = '0';
                row.className = 'rounded-xl border border-slate-200 bg-slate-50/80 p-3 shadow-sm transition hover:border-slate-300';

                const selectId = `sale-product-${rowId}`;
                const quantityId = `sale-quantity-${rowId}`;

                row.innerHTML = `
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-black tracking-wider text-slate-700" data-row-label>Barang</p>
                        </div>
                        <button type="button" data-remove-item
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-500 transition hover:border-red-200 hover:text-red-600">
                            <i class="bi bi-trash"></i>
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                        <div class="md:col-span-8">
                            <label for="${selectId}" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Produk</label>
                            <select id="${selectId}" name="products[${rowId}][product_id]" class="w-full" required></select>

                            <div data-product-info class="mt-2 hidden space-y-1 rounded-xl border border-slate-100 bg-white p-2.5">
                                <div class="flex justify-between text-[10px] font-bold tracking-widest">
                                    <span class="text-slate-400">Kategori: <span data-info-category class="text-secondary">-</span></span>
                                    <span class="text-slate-400">Stok: <span data-info-stock class="text-emerald-600">0</span></span>
                                </div>
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 tracking-widest">
                                    <span>Harga Satuan</span>
                                    <span class="text-slate-900">Rp <span data-info-price>0</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-4">
                            <label for="${quantityId}" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Jumlah</label>
                            <input id="${quantityId}" type="number" data-quantity-input name="products[${rowId}][quantity]" min="1" value="${initialData.quantity ?? 1}"
                                class="w-full rounded-xl border-slate-200 bg-white px-3 py-2 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10"
                                required>

                            <div class="mt-2 rounded-xl bg-slate-100 px-3 py-2 text-[11px] font-bold text-slate-500">
                                Sub: <span class="font-black text-slate-900">Rp <span data-item-subtotal>0</span></span>
                            </div>
                        </div>
                    </div>
                `;

                const selectEl = row.querySelector('select');
                const qtyInput = row.querySelector('[data-quantity-input]');
                const removeButton = row.querySelector('[data-remove-item]');

                const ts = new TomSelect(selectEl, tomSelectConfig);

                selectEl.addEventListener('change', function() {
                    updateRowInfo(row, this.value);
                });

                qtyInput.addEventListener('input', function() {
                    const productId = selectEl.value;

                    if (productId && productData[productId]) {
                        const maxStock = productData[productId].stock;

                        if (parseInt(this.value || '1') > maxStock) {
                            this.value = maxStock > 0 ? maxStock : 1;
                        }
                    }

                    updateGrandTotal();
                });

                qtyInput.addEventListener('change', function() {
                    if (parseInt(this.value || '1') < 1) {
                        this.value = 1;
                    }

                    updateGrandTotal();
                });

                removeButton.addEventListener('click', function() {
                    if (saleItemsContainer.querySelectorAll('[data-sale-item]').length === 1) {
                        ts.clear();
                        qtyInput.value = 1;
                        updateRowInfo(row, '');
                        updateGrandTotal();
                        return;
                    }

                    row.remove();
                    refreshRowLabels();
                    updateGrandTotal();
                });

                if (initialData.product_id) {
                    ts.setValue(String(initialData.product_id), true);
                    updateRowInfo(row, String(initialData.product_id));
                }

                rowId += 1;

                return row;
            }

            function addRow(initialData = {}) {
                saleItemsContainer.appendChild(createSaleRow(initialData));
                refreshRowLabels();
                updateGrandTotal();
            }

            addItemButton.addEventListener('click', function() {
                addRow();
            });

            if (initialItems.length) {
                initialItems.forEach((item) => addRow(item));
            } else {
                addRow();
            }
        });
    </script>
@endpush