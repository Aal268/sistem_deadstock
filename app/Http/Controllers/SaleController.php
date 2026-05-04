<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('current_stock', '>', 0)->get();
        // Load riwayat penjualan terbaru
        $recentSales = StockMovement::with('product', 'user')
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
            'user_id' => auth()->id(),
            'movement_date' => $movementDateTime,
            'note' => $request->note ?? 'Penjualan oleh Kasir'
        ]);

        // Kurangi stok
        $product->decrement('current_stock', $request->quantity);

        return back()->with('success', 'Transaksi penjualan berhasil dicatat!');
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load('product.category', 'user');
        
        if (auth()->user()->role === 'admin') {
            return view('admin.riwayat.detail-riwayat', compact('stockMovement'));
        }
        
        return view('kasir.histori-sales.detail-histori', compact('stockMovement'));
    }

    public function history(Request $request)
    {
        $query = StockMovement::with('product.category', 'user')->where('type', 'out');

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

        $allSales = $query->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20)
                        ->withQueryString();

        return view('kasir.histori-sales.index', compact('allSales'));
    }

    public function adminHistory(Request $request)
    {
        $query = StockMovement::with('product.category', 'user')->where('type', 'out');

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

        $allSales = $query->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(20)
                        ->withQueryString();

        return view('admin.riwayat.index', compact('allSales'));
    }

    public function edit(StockMovement $stockMovement)
    {
        // Only allow editing out movements
        if ($stockMovement->type !== 'out') {
            return redirect('/riwayat')->with('error', 'Hanya transaksi penjualan yang dapat diedit.');
        }
        
        $products = Product::all();
        return view('admin.riwayat.edit', compact('stockMovement', 'products'));
    }

    public function update(Request $request, StockMovement $stockMovement)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        if ($stockMovement->type !== 'out') {
            return redirect('/riwayat')->with('error', 'Hanya transaksi penjualan yang dapat diedit.');
        }

        try {
            DB::transaction(function () use ($request, $stockMovement) {
                $oldProduct = Product::findOrFail($stockMovement->product_id);
                $newProduct = Product::findOrFail($request->product_id);
                
                $oldQuantity = $stockMovement->quantity;
                $newQuantity = $request->quantity;

                // 1. Revert old transaction (add back stock)
                $oldProduct->increment('current_stock', $oldQuantity);

                // 2. Refresh new product stock in case it's the same product
                $newProduct->refresh();

                // 3. Check if new product has enough stock
                if ($newProduct->current_stock < $newQuantity) {
                    throw new \Exception('Stok produk tidak mencukupi untuk jumlah yang baru.');
                }

                // 4. Deduct new stock
                $newProduct->decrement('current_stock', $newQuantity);

                // 5. Update StockMovement record
                $stockMovement->update([
                    'product_id' => $newProduct->id,
                    'quantity' => $newQuantity,
                    'price_at_transaction' => $newProduct->unit_price,
                    'note' => $request->note ?? 'Diedit oleh Admin'
                ]);
            });

            return redirect('/riwayat')->with('success', 'Riwayat penjualan dan stok berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
