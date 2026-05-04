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
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        /* Pastikan wrapper full width */
        .ts-wrapper {
            width: 100% !important;
        }

        /* Input search langsung di dalam control bar */
        .ts-wrapper .ts-control input {
            font-size: 0.875rem !important;
            color: #1e293b !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: 100% !important;
        }

        /* Placeholder */
        .ts-wrapper .ts-control .placeholder {
            color: #94a3b8 !important;
            font-size: 0.875rem !important;
        }

        /* SKU badge di dropdown */
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

        /* Animasi fade-in untuk product info */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.25s ease-out;
        }

        /* Clear button - disabled */
        /*
            .ts-wrapper .clear-button {
                color: #94a3b8 !important;
                font-size: 1.1rem !important;
                opacity: 1 !important;
                transition: color 0.15s;
            }
            .ts-wrapper .clear-button:hover {
                color: #ef4444 !important;
            }
            */

        /* Sembunyikan panah dropdown agar benar-benar terlihat seperti search field */
        .ts-wrapper .ts-control::after {
            display: none !important;
        }

        /* Hilangkan padding tambahan agar teks tidak terpotong */
        .ts-wrapper .ts-control {
            padding-right: 1rem !important;
        }

        /* Sembunyikan select asli secara aman agar tidak bentrok dengan Tom Select */
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
        <p class="text-sm text-slate-500">Proses transaksi kasir dan kurangi stok barang yang terjual secara real-time.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Form Input Penjualan -->
        <div class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden sticky top-8">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
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
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Cari
                                Produk / SKU</label>
                            <select id="product-select" name="product_id" class="w-full" required>
                            </select>

                            <!-- Area Info Produk (Muncul setelah produk dipilih) -->
                            <div id="product-info"
                                class="mt-4 hidden space-y-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="flex justify-between text-[10px] font-bold tracking-widest">
                                    <span class="text-slate-400">Kategori: <span id="info-category"
                                            class="text-secondary">-</span></span>
                                    <span class="text-slate-400">Stok: <span id="info-stock"
                                            class="text-emerald-600">0</span></span>
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 tracking-widest">
                                    Harga Satuan: <span class="text-slate-900">Rp <span id="info-price">0</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah
                                    (Pcs)</label>
                                <input type="number" id="input-qty" name="quantity" min="1" value="1"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Tanggal</label>
                                <input type="date" name="movement_date" value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10"
                                    required>
                            </div>
                        </div>

                        <div id="subtotal-area" class="hidden space-y-4 pt-2">
                            <div class="rounded-xl bg-secondary/5 p-4 border border-secondary/20">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase text-slate-500">Estimasi Total</span>
                                    <span class="text-lg font-black text-secondary">Rp <span
                                            id="display-subtotal">0</span></span>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Catatan
                                    Penjualan (Opsional)</label>
                                <input type="text" name="note" placeholder="Contoh: Titipan pelanggan, dll"
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm transition focus:border-secondary focus:bg-white focus:ring-4 focus:ring-secondary/10">
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
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('product-select');
            const qtyInput = document.getElementById('input-qty');
            const productInfo = document.getElementById('product-info');
            const subtotalArea = document.getElementById('subtotal-area');
            const infoCategory = document.getElementById('info-category');
            const infoStock = document.getElementById('info-stock');
            const infoPrice = document.getElementById('info-price');
            const displaySubtotal = document.getElementById('display-subtotal');

            const formatRupiah = (n) => new Intl.NumberFormat('id-ID').format(n);

            // Ambil data produk langsung dari variabel PHP ke JS
            const productData = {!! $products->mapWithKeys(
                    fn($p) => [
                        $p->id => [
                            'id' => $p->id,
                            'price' => (int) $p->unit_price,
                            'stock' => (int) $p->current_stock,
                            'category' => $p->category->name ?? '-',
                            'sku' => $p->sku,
                            'name' => $p->name,
                            'text' => $p->sku . ' - ' . $p->name,
                        ],
                    ],
                )->toJson() !!};

            // Konversi ke array untuk Tom Select options
            const productOptions = Object.values(productData);

            function showProductInfo(value) {
                const d = productData[value];
                if (!d) {
                    productInfo.classList.add('hidden');
                    subtotalArea.classList.add('hidden');
                    return;
                }

                infoCategory.textContent = d.category;
                infoStock.textContent = formatRupiah(d.stock) + ' Pcs';
                infoPrice.textContent = formatRupiah(d.price);

                // Warna stok kritis
                infoStock.className = d.stock < 5 ?
                    'text-red-600 font-black' :
                    'text-emerald-600 font-bold';

                productInfo.classList.remove('hidden');
                productInfo.classList.add('animate-fade-in');
                subtotalArea.classList.remove('hidden');
                subtotalArea.classList.add('animate-fade-in');

                updateTotal(d.price);
            }

            function updateTotal(price) {
                const qty = Math.max(1, parseInt(qtyInput.value) || 0);
                displaySubtotal.textContent = formatRupiah(price * qty);
            }

            // Inisialisasi Tom Select
            const ts = new TomSelect('#product-select', {
                options: productOptions,
                valueField: 'id',
                labelField: 'text',
                searchField: ['name', 'sku'],
                create: false,
                maxOptions: 50,
                placeholder: 'Ketik nama produk atau SKU...',
                openOnFocus: false, // <--- Ini yang membuat tidak langsung drop down saat diklik
                // plugins     : ['clear_button'],
                render: {
                    option: function(data, escape) {
                        const sku = escape(data.sku || '');
                        const name = escape(data.name || data.text || '');
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
                        const raw = productData[data.value];
                        const sku = raw ? escape(raw.sku) : '';
                        const name = raw ? escape(raw.name) : escape(data.text);
                        return `<div style="font-size:0.875rem; font-weight:600; color:#334155;">${sku} – ${name}</div>`;
                    },
                    no_results: function() {
                        return `<div style="padding:0.75rem 1rem; font-size:0.875rem; color:#94a3b8; font-style:italic;">Produk tidak ditemukan...</div>`;
                    }
                },
                onChange: function(value) {
                    if (!value) {
                        productInfo.classList.add('hidden');
                        subtotalArea.classList.add('hidden');
                        return;
                    }
                    showProductInfo(value);
                }
            });

            // Update subtotal real-time saat qty berubah
            qtyInput.addEventListener('input', function() {
                const value = ts.getValue();
                if (!value || !productData[value]) return;
                updateTotal(productData[value].price);
            });

            // Validasi stok saat qty diubah
            qtyInput.addEventListener('change', function() {
                const value = ts.getValue();
                if (!value || !productData[value]) return;
                const maxStock = productData[value].stock;
                if (parseInt(this.value) > maxStock) {
                    alert(`Stok tidak mencukupi! Tersedia: ${maxStock} pcs.`);
                    this.value = maxStock > 0 ? maxStock : 1;
                    updateTotal(productData[value].price);
                }
                if (parseInt(this.value) < 1) this.value = 1;
            });
        });
    </script>
@endpush
