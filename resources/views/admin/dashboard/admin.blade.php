@extends('layouts.app')

@section('content')
<div class="space-y-5 font-sans">
    <div>
        <h2 class="text-3xl font-black tracking-tight text-[#13505B]">Dashboard Admin</h2>
        <p class="mt-1 text-sm font-medium text-slate-500">Ringkasan inventaris dan performa gudang.</p>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-primary p-4 text-white">
            <p class="text-sm font-medium text-white/90">Varian Produk</p>
            <p class="mt-1.5 text-4xl font-black leading-none">{{ $totalProducts }}</p>
        </div>

        <div class="rounded-xl bg-primary p-4 text-white">
            <p class="text-sm font-medium text-white/90">Sisa Total Stok</p>
            <p class="mt-1.5 text-4xl font-black leading-none">{{ $totalRemainingStock }} <span class="ml-1 text-lg font-bold">Pcs</span></p>
        </div>

        <div class="rounded-xl bg-primary p-4 text-white">
            <p class="text-sm font-medium text-white/90">Total Terjual</p>
            <p class="mt-1.5 text-4xl font-black leading-none">{{ $totalSoldAllTime }} <span class="ml-1 text-lg font-bold">Pcs</span></p>
        </div>

        <div class="rounded-xl bg-primary p-4 text-white">
            <p class="text-sm font-medium text-white/90">Stok Kritis</p>
            <p class="mt-1.5 text-4xl font-black leading-none">{{ $criticalStockItems }} <span class="ml-1 text-lg font-bold">Item</span></p>
        </div>
    </div>

    <div class="rounded-xl border border-info bg-white p-5 text-center shadow-sm mt-3 mb-3">
        <p class="text-xl font-semibold text-primary">Nilai Kapasitas Stok Terkini</p>
        <h1 class="mt-1.5 text-5xl font-black tracking-tight text-secondary">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h1>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-info bg-white p-5 text-center shadow-sm">
            <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-primary">
                <i class="bi bi-graph-up-arrow text-3xl"></i>
            </div>
            <h5 class="mt-3 text-xl font-bold text-slate-800">Analisis Restock</h5>
            <p class="mt-1.5 text-sm text-primary">Algoritma analisis velocity barang / deteksi deadstock.</p>
            <a href="/analysis" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary">Lihat Rekomendasi</a>
        </div>

        <div class="rounded-xl border border-info bg-white p-5 text-center shadow-sm">
            <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <i class="fa-solid fa-box-open text-3xl"></i>
            </div>
            <h5 class="mt-3 text-xl font-bold text-slate-800">Catat Pembelian Masuk</h5>
            <p class="mt-1.5 text-sm text-primary">Update stok barang setelah menerima dari supplier.</p>
            <a href="/purchases" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary">Form Pembelian</a>
        </div>

        <div class="rounded-xl border border-info bg-white p-5 text-center shadow-sm">
            <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-primary">
                <i class="fa-solid fa-users text-3xl"></i>
            </div>
            <h5 class="mt-3 text-xl font-bold text-slate-800">Kelola Akun Karyawan</h5>
            <p class="mt-1.5 text-sm text-primary">Tambah akses login untuk Kasir atau Admin tambahan.</p>
            <a href="/users" class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">Manajemen Akun</a>
        </div>
    </div>
</div>
@endsection
