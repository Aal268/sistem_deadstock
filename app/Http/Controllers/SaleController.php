<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    public function index()
    {
        $products = Product::where('current_stock', '>', 0)->get();
        // Load riwayat penjualan terbaru
        $recentSales = StockMovement::with('product')
                        ->where('type', 'out')
                        ->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->limit(10)
                        ->get();

        return view('kasir.sales.index', compact('products', 'recentSales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'required|date'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->current_stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi untuk penjualan ini!');
        }

        // Gabungkan tanggal input dengan waktu sekarang untuk detail timestamp
        $movementDateTime = Carbon::parse($request->movement_date)->setTimeFrom(now());

        // Catat penjualan
        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'status' => 'success',
            'quantity' => $request->quantity,
            'price_at_transaction' => $product->unit_price,
            'movement_date' => $movementDateTime,
            'note' => $request->note ?? 'Penjualan oleh Kasir'
        ]);

        // Kurangi stok
        $product->decrement('current_stock', $request->quantity);

        return back()->with('success', 'Transaksi penjualan berhasil dicatat!');
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load('product.category');
        return view('kasir.sales.detail-sales', compact('stockMovement'));
    }

    public function history()
    {
        $allSales = StockMovement::with('product.category')
                        ->where('type', 'out')
                        ->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        return view('kasir.histori-sales.index', compact('allSales'));
    }
}
