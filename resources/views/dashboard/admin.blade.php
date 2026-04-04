@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Dashboard Admin</h2>
        <p class="text-muted">Ringkasan inventaris dan performa gudang.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-normal">Varian Produk</h5>
                <h2 class="display-5 fw-bold">{{ $totalProducts }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-normal">Sisa Total Stok</h5>
                <h2 class="display-5 fw-bold">{{ $totalRemainingStock }} <span class="fs-5">Pcs</span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-dark h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-normal">Total Terjual</h5>
                <h2 class="display-5 fw-bold">{{ $totalSoldAllTime }} <span class="fs-5">Pcs</span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-normal">Stok Kritis</h5>
                <h2 class="display-5 fw-bold">{{ $criticalStockItems }} <span class="fs-5">Item</span></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body text-center py-4">
                <h5 class="text-muted mb-1">Nilai Kapasitas Stok Terkini</h5>
                <h1 class="display-4 text-success fw-bold">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-graph-up-arrow display-4 text-primary"></i>
                <h5 class="mt-3">Analisis Restock</h5>
                <p class="text-muted small">Algoritma analisis velocity barang / deteksi deadstock.</p>
                <a href="/analysis" class="btn btn-primary w-100">Lihat Rekomendasi</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-box-arrow-in-down display-4 text-success"></i>
                <h5 class="mt-3">Catat Pembelian Masuk</h5>
                <p class="text-muted small">Update stok barang setelah menerima barang dari supplier.</p>
                <a href="/purchases" class="btn btn-success w-100">Form Pembelian</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-people display-4 text-secondary"></i>
                <h5 class="mt-3">Kelola Akun Karyawan</h5>
                <p class="text-muted small">Tambah akses login untuk Kasir atau Admin tambahan.</p>
                <a href="/users" class="btn btn-secondary w-100">Manajemen Akun</a>
            </div>
        </div>
    </div>
</div>
@endsection
