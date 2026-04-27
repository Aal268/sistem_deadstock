<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'administrator') {
            // Kasir Dashboard: Data penjualan bulanan & grafik per hari
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $salesThisMonth = StockMovement::where('type', 'out')
                ->whereBetween('movement_date', [$startOfMonth, $endOfMonth])
                ->get();

            $totalItemSold = $salesThisMonth->sum('quantity');

            // Persiapkan data grafik (Tanggal 1 - 31)
            $chartLabels = [];
            $chartData = [];
            $daysInMonth = Carbon::now()->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartLabels[] = "Tgl $i";
                $dateString = Carbon::now()->format('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                $soldOnDay = $salesThisMonth->where('movement_date', '>=', $dateString . ' 00:00:00')
                                          ->where('movement_date', '<=', $dateString . ' 23:59:59')
                                          ->sum('quantity');
                $chartData[] = $soldOnDay;
            }

            return view('kasir.dashboard.kasir', compact('totalItemSold', 'chartLabels', 'chartData'));

        } else {
            // Admin Dashboard: Ringkasan stok
            $products = Product::all();
            
            $totalProducts = $products->count();
            $totalStockValue = $products->sum(function($p) {
                return $p->current_stock * $p->cost_price;
            });
            $criticalStockItems = $products->filter(function($p) {
                return $p->current_stock <= $p->safety_stock;
            })->count();

            // Total Produk Terjual All time (atau 30 hari terakhir)
            $totalSoldAllTime = StockMovement::where('type', 'out')->sum('quantity');
            $totalRemainingStock = $products->sum('current_stock');

            return view('admin.dashboard.admin', compact(
                'totalProducts', 'totalStockValue', 'criticalStockItems', 
                'totalSoldAllTime', 'totalRemainingStock'
            ));
        }
    }
}
