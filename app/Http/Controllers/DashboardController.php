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

        } elseif ($user->role === 'gudang') {
            // Gudang Dashboard: List barang dan info gudang
            $products = Product::with('category')->get();
            
            $totalProducts = $products->count();
            $totalRemainingStock = $products->sum('current_stock');
            $criticalStockItems = $products->filter(function($p) {
                return $p->current_stock <= $p->safety_stock;
            })->count();

            // Tambahan info Manajemen Data
            $totalCategories = \App\Models\Category::count();
            $totalSuppliers = \App\Models\Supplier::count();

            return view('gudang.dashboard.gudang', compact(
                'products', 'totalProducts', 'totalRemainingStock', 'criticalStockItems',
                'totalCategories', 'totalSuppliers'
            ));
        } elseif ($user->role === 'admin') {
            // Admin Dashboard: Ringkasan stok & Analisis mendalam
            $products = Product::with('stockMovements')->get();
            
            $totalProducts = $products->count();
            $totalStockValue = $products->sum(function($p) {
                return $p->current_stock * $p->cost_price;
            });
            
            // Perhitungan Analisis untuk Dashboard (Decision Support)
            $threeMonthsAgo = Carbon::now()->subMonths(3);
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $analysisData = $products->map(function ($p) use ($threeMonthsAgo) {
                $totalOutLast3Months = $p->stockMovements
                    ->where('type', 'out')
                    ->where('movement_date', '>=', $threeMonthsAgo)
                    ->sum('quantity');

                $avgMonthlySales = round($totalOutLast3Months / 3, 2);
                
                // Logika Status (Sesuai algoritma yang ada)
                $status = 'Normal';
                if ($totalOutLast3Months == 0) {
                    $status = 'Deadstock';
                } else if ($p->current_stock < ($avgMonthlySales * 1.5)) {
                    $status = 'Fast-Moving';
                } else {
                    $status = 'Slow-Moving';
                }

                // Target stok bulan depan = avg + safety stock
                $targetStock = ceil($avgMonthlySales + $p->safety_stock);
                $suggestedBuy = max(0, $targetStock - $p->current_stock);
                if ($status == 'Deadstock') $suggestedBuy = 0;

                return [
                    'product' => $p,
                    'avg_monthly_sales' => $avgMonthlySales,
                    'status' => $status,
                    'suggested_buy' => $suggestedBuy
                ];
            });

            // 1. Tiga barang dengan rata-rata tertinggi (Top Performers)
            $top3Products = $analysisData->sortByDesc('avg_monthly_sales')->take(3);

            // 2. Jumlah barang yang jadi prioritas untuk dibeli (suggested_buy > 0)
            $priorityBuyCount = $analysisData->where('suggested_buy', '>', 0)->count();

            // 3. Item Urgent (Highlight): Fast-Moving stok tipis atau Deadstock
            $urgentItems = $analysisData->filter(function($item) {
                return ($item['status'] == 'Fast-Moving' && $item['product']->current_stock <= $item['product']->safety_stock) 
                    || $item['status'] == 'Deadstock';
            })->take(5);

            // 4. Grafik Penjualan Harian (Track harian terjual)
            $salesThisMonth = StockMovement::where('type', 'out')
                ->whereBetween('movement_date', [$startOfMonth, $endOfMonth])
                ->get();

            $chartLabels = [];
            $chartData = [];
            $daysInMonth = Carbon::now()->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartLabels[] = "$i";
                $dateString = Carbon::now()->format('Y-m-') . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                $soldOnDay = $salesThisMonth->where('movement_date', '>=', $dateString . ' 00:00:00')
                                          ->where('movement_date', '<=', $dateString . ' 23:59:59')
                                          ->sum('quantity');
                $chartData[] = $soldOnDay;
            }

            // Stats Dasar
            $totalSoldAllTime = StockMovement::where('type', 'out')->sum('quantity');
            $totalRemainingStock = $products->sum('current_stock');
            $criticalStockItems = $products->filter(function($p) {
                return $p->current_stock <= $p->safety_stock;
            })->count();

            return view('admin.dashboard.admin', compact(
                'totalProducts', 'totalStockValue', 'criticalStockItems', 
                'totalSoldAllTime', 'totalRemainingStock',
                'top3Products', 'priorityBuyCount', 'urgentItems',
                'chartLabels', 'chartData'
            ));
        } else {
            return abort(403, 'Unauthorized role.');
        }

    }
}
