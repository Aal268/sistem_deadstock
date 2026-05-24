<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Penjualan</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>SKU</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Catatan</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allSales as $sale)
                @php
                    $subtotal = $sale->quantity * ($sale->price_at_transaction ?? $sale->product->unit_price);
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->movement_date)->format('d M Y H:i') }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td>{{ $sale->product->sku ?? '-' }}</td>
                    <td>{{ $sale->product->name ?? 'Produk Dihapus' }}</td>
                    <td>{{ $sale->product->category->name ?? '-' }}</td>
                    <td>{{ $sale->note ?? '-' }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ $sale->price_at_transaction ?? $sale->product->unit_price }}</td>
                    <td>{{ $subtotal }}</td>
                    <td>{{ $sale->status ?? 'Success' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>