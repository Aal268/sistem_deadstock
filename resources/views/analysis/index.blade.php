@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Analisis & Rekomendasi Restock</h2>
        <p class="text-muted">Daftar rekomendasi ini dihasilkan dengan membandingkan rata-rata penjualan per bulan terhadap sisa stok saat ini.</p>
    </div>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">SKU - Nama Barang</th>
                        <th>Supplier</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-center">Safety Stok</th>
                        <th class="text-center">Rata-rata Terjual/Bulan</th>
                        <th class="text-center">Status Velocity</th>
                        <th class="text-center pe-4">Saran Pembelian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysis as $item)
                    <tr>
                        <td class="ps-4">
                            <strong>{{ $item['product']->sku }}</strong><br>
                            {{ $item['product']->name }}
                        </td>
                        <td>{{ $item['product']->supplier->name ?? '-' }}</td>
                        <td class="text-center">
                            @if($item['product']->current_stock <= $item['product']->safety_stock)
                                <span class="text-danger fw-bold">{{ $item['product']->current_stock }}</span>
                            @else
                                {{ $item['product']->current_stock }}
                            @endif
                        </td>
                        <td class="text-center text-muted">{{ $item['product']->safety_stock }}</td>
                        <td class="text-center fw-bold">{{ $item['avg_monthly_sales'] }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item['bg_color'] }}">{{ $item['status'] }}</span>
                        </td>
                        <td class="text-center pe-4">
                            @if($item['suggested_buy'] > 0)
                                <span class="badge bg-success py-2 px-3 fs-6">Beli +{{ $item['suggested_buy'] }} Pcs</span>
                                <div class="text-muted small mt-1">{{ $item['recommendation_text'] }}</div>
                            @elseif($item['status'] == 'Deadstock')
                                <span class="badge bg-danger py-2 px-3">STOP PEMBELIAN</span>
                                <div class="text-muted small mt-1">Stok Mandek (Obral)</div>
                            @else
                                <span class="badge bg-secondary py-2 px-3">TIDAK PERLU</span>
                                <div class="text-muted small mt-1">Stok Masih Cukup</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
