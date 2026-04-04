@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Pembelian Barang (Restock)</h2>
        <p class="text-muted">Catat barang masuk / restock dari supplier untuk menambah stok gudang.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Form Kedatangan Barang</h5>
            </div>
            <div class="card-body">
                <form action="/purchases" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Pilih Barang yang Dibeli</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Saat ini: {{ $p->current_stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Kuantitas Masuk (Pcs)</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small fw-bold">Tanggal Datang</label>
                            <input type="date" name="movement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Catatan / Invoice Supplier</label>
                        <input type="text" name="note" class="form-control" placeholder="Opsional...">
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-down"></i> Simpan Stok Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">10 Histori Penerimaan Terakhir</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tanggal</th>
                                <th>Barang</th>
                                <th class="text-center">Jumlah Masuk</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchases as $purchase)
                            <tr>
                                <td class="ps-3 text-muted small">{{ \Carbon\Carbon::parse($purchase->movement_date)->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $purchase->product->sku ?? '-' }}</strong><br>
                                    <span class="small">{{ $purchase->product->name ?? 'Produk Dihapus' }}</span>
                                </td>
                                <td class="text-center fw-bold text-success">+{{ $purchase->quantity }}</td>
                                <td class="small">{{ $purchase->note }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada histori barang masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
