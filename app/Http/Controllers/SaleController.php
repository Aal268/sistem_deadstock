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

        return view('sales.index', compact('products', 'recentSales'));
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

        // Catat penjualan
        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => $request->quantity,
            'movement_date' => $request->movement_date,
            'note' => $request->note ?? 'Penjualan oleh Kasir'
        ]);

        // Kurangi stok
        $product->decrement('current_stock', $request->quantity);

        return back()->with('success', 'Transaksi penjualan berhasil dicatat!');
    }
}
