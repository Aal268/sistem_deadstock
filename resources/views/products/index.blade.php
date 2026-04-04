@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h2>Kelola Master Barang</h2>
            <p class="text-muted">Daftar semua varian barang yang terdaftar di dalam sistem.</p>
        </div>
        <a href="/products/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Barang Baru
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">SKU</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Sisa Stok</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td class="ps-4 font-monospace small">{{ $p->sku }}</td>
                        <td class="fw-bold">{{ $p->name }}<br><span class="fw-normal small text-muted">{{ $p->supplier->name ?? 'Tanpa Supplier' }}</span></td>
                        <td><span class="badge bg-secondary">{{ $p->category->name ?? '-' }}</span></td>
                        <td class="text-center">
                            @if($p->current_stock <= $p->safety_stock)
                                <span class="text-danger fw-bold">{{ $p->current_stock }}</span>
                            @else
                                <span class="text-success">{{ $p->current_stock }}</span>
                            @endif
                        </td>
                        <td class="text-end">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($p->unit_price, 0, ',', '.') }}</td>
                        <td class="text-end pe-4">
                            <a href="/products/{{ $p->id }}/edit" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                            <form action="/products/{{ $p->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus master data barang ini beserta seluruh nilai stoknya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Master Data Barang masih kosong.<br><a href="/products/create" class="btn btn-sm btn-outline-primary mt-2">Daftarkan Sekarang</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
