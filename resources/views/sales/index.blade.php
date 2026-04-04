@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Kasir Penjualan</h2>
        <p class="text-muted">Proses transaksi kasir dan kurangi stok barang yang terjual.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Input Penjualan</h5>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="/sales" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Pilih Barang yang Terjual</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Cari / Pilih Barang --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Sisa: {{ $p->current_stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Jumlah (Pcs)</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Tanggal Trx</label>
                            <input type="date" name="movement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-cart-check"></i> Proses Penjualan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">10 Transaksi Terakhir</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Barang</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td class="ps-3 text-muted small">{{ \Carbon\Carbon::parse($sale->movement_date)->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $sale->product->sku ?? '-' }}</strong><br>
                                    <span class="small">{{ $sale->product->name ?? 'Produk Dihapus' }}</span>
                                </td>
                                <td class="text-center fw-bold text-danger">-{{ $sale->quantity }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada transaksi keluar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
