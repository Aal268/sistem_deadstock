<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(10);

        $totalProducts = $products->count();
        $totalRemainingStock = $products->sum('current_stock');
        $criticalStockItems = $products->filter(function($p) {
            return $p->current_stock <= $p->safety_stock;
        })->count();

        return view('admin.gudang.gudang', compact(
            'products', 'totalProducts', 'totalRemainingStock', 'criticalStockItems'
        $allProducts = Product::all();
        $totalProducts = $allProducts->count();
        $totalRemainingStock = $allProducts->sum('current_stock');
        $criticalStockItems = $allProducts->filter(function($p) {
            return $p->current_stock <= $p->safety_stock;
        })->count();

        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'kritis') {
                $query->whereColumn('current_stock', '<=', 'safety_stock');
            } elseif ($request->stock_status === 'aman') {
                $query->whereColumn('current_stock', '>', 'safety_stock');
            }
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        // Tambahan info Manajemen Data
        $totalCategories = \App\Models\Category::count();
        $totalSuppliers = \App\Models\Supplier::count();

        return view('gudang.dashboard.gudang', compact(
            'products', 'totalProducts', 'totalRemainingStock', 'criticalStockItems',
            'totalCategories', 'totalSuppliers', 'categories'
        ));
    }


}
