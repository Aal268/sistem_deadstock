@extends('layouts.app')

@section('content')
<div class="space-y-8 font-sans">
    <div>
        <h2 class="text-3xl font-black tracking-tight text-[#13505B]">Dashboard Gudang</h2>
        <p class="mt-1 text-sm font-medium text-slate-500">Ringkasan inventaris dan stok barang saat ini.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-br from-[#13505B] to-[#0C7489] p-6 text-white shadow-lg shadow-[#13505B]/20">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white/90">Total Varian Barang</h3>
            <p class="mt-3 text-4xl font-black leading-none">{{ $totalProducts }}</p>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-[#119DA4] to-emerald-600 p-6 text-white shadow-lg shadow-emerald-600/20">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white/90">Nilai Asset Stok (Modal)</h3>
            <p class="mt-3 text-3xl font-black leading-tight">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-amber-200 to-orange-300 p-6 text-slate-900 shadow-lg shadow-amber-500/20">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-700">Stok Kritis</h3>
            <p class="mt-3 text-5xl font-black leading-none">{{ $criticalStockItems }} <span class="align-middle text-base font-semibold text-slate-700">Item</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mx-auto flex max-w-md flex-col items-center text-center">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#13505B]/10 text-[#13505B]">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8">
                        <path d="M22 12l-4-4v3h-8v2h8v3l4-4Zm-14.5 7A6.5 6.5 0 0 1 1 12.5C1 8.91 3.91 6 7.5 6H14V4H7.5A8.5 8.5 0 0 0 7.5 21H14v-2H7.5Z" />
                    </svg>
                </div>
                <h4 class="mt-4 text-xl font-bold tracking-tight text-slate-900">Analisis Restock Cerdas</h4>
                <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan sistem analisis untuk mengevaluasi velocity/kecepatan penjualan barang untuk rekomendasi belanja bulan depan.</p>
                <a href="/analysis" class="mt-5 inline-flex items-center rounded-lg bg-[#13505B] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0C7489]">Buka Halaman Analisis</a>
            </div>
        </div>
    </div>
</div>
@endsection
