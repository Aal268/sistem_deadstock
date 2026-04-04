<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function($p) {
            return $p->current_stock * $p->cost_price;
        });

        // Cari item yang stoknya kritis (kurang dari safety_stock)
        $criticalStockItems = $products->filter(function($p) {
            return $p->current_stock <= $p->safety_stock;
        })->count();

        // Dalam dunia nyata, hitung deadstock via StockMovement. 
        // Untuk dashboard sederhana (demo), kita asumsikan jika stok >= 100 dan bukan fast moving.
        // Tapi kita panggil logic analisis restock sekilas jika diperlukan, atau buat quick query.
        
        return view('dashboard', compact('totalProducts', 'totalStockValue', 'criticalStockItems'));
    }
}
