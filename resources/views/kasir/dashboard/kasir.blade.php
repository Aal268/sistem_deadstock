@extends('layouts.app')

@section('content')
    <div class="mb-8">
        {{-- <h2 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Kasir</h2> --}}
        <p class="text-secondary text-2xl">Selamat bertugas, <span class="font-bold text-tertiary text-2xl">{{ auth()->user()->name }}. </span>
        <p class="text-primary font-bold text-xl">Jangan Lupa report dan rekap penjualan harian</span></p>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Stat Card -->
        <div class="lg:col-span-4">
            <div class="relative overflow-hidden rounded-2xl bg-secondary p-8 text-white shadow-xl shadow-secondary/20">
                <div class="relative z-10">
                    <p class="text-xs font-bold tracking-widest text-white/60">Terjual Bulan Ini</p>
                    <div class="mt-4 flex items-baseline gap-2">
                        <h2 class="text-5xl font-black">{{ $totalItemSold }}</h2>
                        <span class="text-lg font-medium text-white/80">Pcs</span>
                    </div>
                    <p class="mt-4 text-sm text-white/70">Total akumulasi transaksi keluar.</p>
                </div>
                <!-- Decorative Icon -->
                <i class="bi bi-cart-check absolute -right-4 -top-4 text-9xl text-white/10"></i>
            </div>

            <!-- Action Card -->
            <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-white">
                    <i class="fa-solid fa-cart-plus text-xl"></i>
                </div>
                <h4 class="mt-4 text-lg font-bold text-slate-900">Input Penjualan</h4>
                <p class="mt-2 text-sm text-slate-500 line-clamp-2 px-4">Catat barang keluar saat ada transaksi pembelian
                    pelanggan.</p>
                <a href="/sales"
                    class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-primary px-6 py-3 text-sm font-bold text-primary transition hover:bg-primary hover:text-white">
                    Buka Form Kasir
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Chart Card -->
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden h-full">
                <div class="border-b border-slate-100 bg-white px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-secondary">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 class="font-bold text-slate-800">Grafik Penjualan Harian</h5>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative h-[300px]">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Jumlah Barang Terjual (Pcs)',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(12, 116, 137, 0.1)',
                        borderColor: 'rgba(12, 116, 137, 1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(12, 116, 137, 1)',
                        pointRadius: 4
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 10
                        }
                    }
                }
            });
        });
    </script>
@endpush
