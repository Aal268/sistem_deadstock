@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2>Dashboard Gudang</h2>
        <p class="text-muted">Ringkasan inventaris dan stok barang saat ini.</p>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Total Varian Barang</h5>
                <h2 class="display-4">{{ $totalProducts }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Nilai Asset Stok (Modal)</h5>
                <h2 class="display-6 mt-3">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Stok Kritis</h5>
                <h2 class="display-4">{{ $criticalStockItems }} <small class="fs-5">Item</small></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-graph-up-arrow display-1 text-primary"></i>
                <h4 class="mt-3">Analisis Restock Cerdas</h4>
                <p class="text-muted">Gunakan sistem analisis untuk mengevaluasi velocity/kecepatan penjualan barang untuk rekomendasi belanja bulan depan.</p>
                <a href="/analysis" class="btn btn-outline-primary mt-2">Buka Halaman Analisis</a>
            </div>
        </div>
    </div>
</div>
@endsection
