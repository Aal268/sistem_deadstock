@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Dashboard Kasir</h2>
        <p class="text-muted">Selamat bertugas, {{ auth()->user()->name }}.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Barang Terjual (Bulan Ini)</h5>
                <h2 class="display-4">{{ $totalItemSold }} <small class="fs-5">Pcs</small></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <i class="bi bi-graph-up text-primary me-2"></i> Grafik Penjualan Bulan Ini
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center py-4">
                <i class="bi bi-cart-plus display-4 text-success mb-3"></i>
                <h4>Input Penjualan</h4>
                <p class="text-muted">Catat barang keluar saat ada transaksi pembelian oleh pelanggan.</p>
                <a href="/sales" class="btn btn-outline-success">Buka Form Kasir</a>
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
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, suggestedMax: 10 }
                }
            }
        });
    });
</script>
@endpush
