@extends('layouts.app')

@section('content')
<div class="space-y-6 font-sans pb-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-[#13505B]">Dashboard Utama</h2>
            <p class="mt-1 text-sm font-medium text-slate-500">Wawasan algoritma dan performa inventaris terkini.</p>
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

        <div class="rounded-2xl bg-[#13505B] p-5 text-white shadow-lg shadow-[#13505B]/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Sisa Stok Total</p>
                <p class="mt-2 text-4xl font-black">{{ number_format($totalRemainingStock, 0, ',', '.') }} <span class="text-lg font-medium">Pcs</span></p>
                <p class="mt-1 text-xs text-white/60">Ketersediaan fisik</p>
            </div>
            <i class="fa-solid fa-warehouse absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>

        <div class="rounded-2xl bg-secondary p-5 text-white shadow-lg shadow-secondary/20 relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Prioritas Beli</p>
                <p class="mt-2 text-4xl font-black">{{ $priorityBuyCount }} <span class="text-lg font-medium">Item</span></p>
                <p class="mt-1 text-xs text-white/60">Berdasarkan velocity</p>
            </div>
            <i class="fa-solid fa-cart-shopping absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>

        <div class="rounded-2xl bg-danger p-5 text-white shadow-lg shadow-danger/20 relative overflow-hidden" style="background: #B76E79">
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-wider text-white/80">Stok Kritis</p>
                <p class="mt-2 text-4xl font-black">{{ $criticalStockItems }} <span class="text-lg font-medium">Item</span></p>
                <p class="mt-1 text-xs text-white/60">Segera restock</p>
            </div>
            <i class="fa-solid fa-triangle-exclamation absolute -right-4 -bottom-4 text-7xl text-white/10"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 rounded-2xl border border-info bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-primary"></i>
                    <h3 class="font-bold text-slate-800" id="chartTitle">Pelacakan Penjualan Harian</h3>
                </div>
                
                <div class="flex flex-wrap items-center gap-2">
                    <div id="decadeNav" class="hidden items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5">
                        <button id="prevDecade" class="px-2 py-1 text-slate-500 hover:text-primary hover:bg-slate-50 rounded shadow-sm text-xs transition"><i class="fa-solid fa-chevron-left"></i></button>
                        <span id="decadeLabel" class="text-xs font-bold px-2 text-slate-700">2020 - 2029</span>
                        <button id="nextDecade" class="px-2 py-1 text-slate-500 hover:text-primary hover:bg-slate-50 rounded shadow-sm text-xs transition"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>

                    <select id="salesFilter" class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-3 py-1.5 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer">
                        <option value="today">Hari Ini</option>
                        <option value="month" selected>Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                        <option value="decade">Dekade</option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="h-[300px] relative">
                    <canvas id="adminSalesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 3 Performers -->
        <div class="rounded-2xl border border-info bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-crown text-warning"></i>
                    <h3 class="font-bold text-slate-800">Top 3 Rata-rata Tertinggi</h3>
                </div>
            </div>
            <div class="p-4 space-y-4">
                @forelse($top3Products as $item)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-100">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $item['product']->name }}</p>
                        <p class="text-xs text-slate-500">{{ $item['product']->category->name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-secondary">{{ round($item['avg_monthly_sales']) }}</p>
                        <p class="text-[10px] uppercase font-bold text-slate-400">Pcs/Bulan</p>
                    </div>
                </div>
                @empty
                <p class="text-center py-10 text-sm text-slate-400">Data belum tersedia</p>
                @endforelse
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <p class="text-[10px] text-center text-slate-400 font-medium italic">*Berdasarkan rata-rata penjualan 3 bulan terakhir</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Urgent Highlights -->
        <div class="rounded-2xl border border-info bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bolt-lightning text-danger"></i>
                    <h3 class="font-bold text-slate-800">Status Urgent & Rekomendasi</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Barang</th>
                            <th class="px-6 py-3">Stok</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($urgentItems as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $item['product']->name }}</p>
                                <p class="text-[10px] text-slate-400">SKU: {{ $item['product']->sku }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-slate-700">{{ $item['product']->current_stock }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item['status'] == 'Deadstock')
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-danger/10 text-danger">DEADSTOCK</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-success/10 text-success">FAST-MOVING</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($item['status'] == 'Deadstock')
                                    <span class="text-xs font-bold text-danger">Obral / Promo</span>
                                @else
                                    <span class="text-xs font-bold text-primary">Beli +{{ $item['suggested_buy'] }} Pcs</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">Tidak ada item urgent saat ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions & Capacity -->
        <div class="space-y-6">
            <div class="rounded-2xl border-2 border-dashed border-info p-6 text-center bg-white">
                <p class="text-sm font-bold text-primary">Estimasi Nilai Stok Saat Ini</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-secondary">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/analysis" class="group p-4 rounded-2xl border border-info bg-white hover:bg-primary transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 group-hover:bg-white/20 flex items-center justify-center text-primary group-hover:text-white transition">
                            <i class="fa-solid fa-magnifying-glass-chart text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800 group-hover:text-white transition">Detail Analisis</p>
                            <p class="text-[10px] text-slate-500 group-hover:text-white/70 transition">Lihat semua rekomendasi</p>
                        </div>
                    </div>
                </a>
                <a href="/purchases" class="group p-4 rounded-2xl border border-info bg-white hover:bg-secondary transition shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-secondary/10 group-hover:bg-white/20 flex items-center justify-center text-secondary group-hover:text-white transition">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('adminSalesChart').getContext('2d');
        let adminSalesChart = null;

        const filterSelect = document.getElementById('salesFilter');
        const decadeNav = document.getElementById('decadeNav');
        const decadeLabel = document.getElementById('decadeLabel');
        const prevDecadeBtn = document.getElementById('prevDecade');
        const nextDecadeBtn = document.getElementById('nextDecade');
        const chartTitle = document.getElementById('chartTitle');

        let currentDecadeYear = new Date().getFullYear();

        function createChart(labels, data, type) {
            if (adminSalesChart) {
                adminSalesChart.destroy();
            }

            adminSalesChart = new Chart(ctx, {
                type: type || 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Produk Terjual (Pcs)',
                        data: data,
                        backgroundColor: 'rgba(12, 116, 137, 0.1)',
                        borderColor: 'rgba(12, 116, 137, 1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(12, 116, 137, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderRadius: type === 'bar' ? 4 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function fetchChartData() {
            const period = filterSelect.value;
            let url = `/dashboard/chart-data?period=${period}`;
            
            if (period === 'decade') {
                url += `&year=${currentDecadeYear}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    createChart(data.labels, data.data, data.chartType);
                    
                    if (data.title) {
                        chartTitle.innerText = data.title;
                    }

                    if (period === 'decade') {
                        decadeNav.classList.remove('hidden');
                        decadeNav.classList.add('flex');
                        const start = currentDecadeYear - (currentDecadeYear % 10);
                        decadeLabel.innerText = `${start} - ${start + 9}`;
                    } else {
                        decadeNav.classList.add('hidden');
                        decadeNav.classList.remove('flex');
                    }
                })
                .catch(err => console.error("Error fetching chart data:", err));
        }

        filterSelect.addEventListener('change', () => {
            if (filterSelect.value === 'decade') {
                currentDecadeYear = new Date().getFullYear(); // Reset ke tahun saat ini jika dekade dipilih
            }
            fetchChartData();
        });

        prevDecadeBtn.addEventListener('click', () => {
            currentDecadeYear -= 10;
            fetchChartData();
        });

        nextDecadeBtn.addEventListener('click', () => {
            currentDecadeYear += 10;
            fetchChartData();
        });

        // Load data pertama kali sesuai default filter (Bulan Ini)
        fetchChartData();
    });
</script>
@endpush

