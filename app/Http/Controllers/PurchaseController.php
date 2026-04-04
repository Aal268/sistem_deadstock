<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $recentPurchases = StockMovement::with('product')
                        ->where('type', 'in')
                        ->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->limit(10)
                        ->get();

        return view('purchases.index', compact('products', 'recentPurchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'movement_date' => 'required|date'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Catat pembelian
        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => $request->quantity,
            'movement_date' => $request->movement_date,
            'note' => $request->note ?? 'Restock Barang Masuk'
        ]);

        // Tambah stok
        $product->increment('current_stock', $request->quantity);

        return back()->with('success', 'Transaksi pembelian/restock berhasil dicatat!');
    }
}
