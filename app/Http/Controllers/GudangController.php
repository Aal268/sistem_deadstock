<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        $totalProducts = $products->count();
        $totalRemainingStock = $products->sum('current_stock');
        $criticalStockItems = $products->filter(function($p) {
            return $p->current_stock <= $p->safety_stock;
        })->count();

        return view('admin.gudang.gudang', compact('products', 'totalProducts', 'totalRemainingStock', 'criticalStockItems'));
    }
}
