<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::orderBy('name')->get();
        
        $query = StockMovement::with('product')->where('type', 'in');

        if ($request->filled('start_date')) {
            $query->whereDate('movement_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('movement_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $recentPurchases = $query->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20)
                        ->withQueryString();

        return view('admin.purchases.index', compact('products', 'recentPurchases'));
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
