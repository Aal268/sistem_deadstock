<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RestockAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $products = $query->get();
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $analysis = $products->map(function (Product $product) use ($threeMonthsAgo) {
            // Hitung total terjual 3 bulan terakhir
            $totalOutLast3Months = $product->stockMovements()
                ->where('type', 'out')
                ->where('movement_date', '>=', $threeMonthsAgo)
                ->sum('quantity');

            // Rata-rata per bulan dibulatkan tanpa desimal
            $avgMonthlySales = round($totalOutLast3Months / 3);

            // Tentukan status
            $status = 'Normal';
            $bgColor = 'bg-secondary';
            $recommendationText = 'Cukup';

            if ($totalOutLast3Months == 0) {
                $status = 'Deadstock';
                $bgColor = 'bg-danger text-white';
                $recommendationText = 'Jangan Beli (Obral)';
            } else if ($product->current_stock < ($avgMonthlySales * 1.5)) {
                $status = 'Fast-Moving';
                $bgColor = 'bg-success text-white';
                $recommendationText = 'Beli Banyak';
            } else {
                $status = 'Slow-Moving';
                $bgColor = 'bg-warning text-dark';
                $recommendationText = 'Beli Sedikit / Tunda';
            }

            // Hitung Saran Kuantitas Pembelian (Suggested Order Quantity)
            // Target stok bulan depan = avg + safety stock
            $targetStock = ceil($avgMonthlySales + $product->safety_stock);
            $suggestedBuy = max(0, $targetStock - $product->current_stock);

            // Jika status deadstock
            if ($status == 'Deadstock') {
                $suggestedBuy = 0;
            }

            return [
                'product' => $product,
                'avg_monthly_sales' => $avgMonthlySales,
                'status' => $status,
                'bg_color' => $bgColor,
                'recommendation_text' => $recommendationText,
                'suggested_buy' => $suggestedBuy
            ];
        });

        if ($request->filled('status')) {
            $statusFilter = $request->status;
            $analysis = $analysis->filter(function($item) use ($statusFilter) {
                return strtolower($item['status']) === strtolower($statusFilter);
            });
        }

        // Urutkan berdasarkan yang paling urgent (Fast Moving dulu)
        $analysis = $analysis->sortByDesc('suggested_buy');

        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('admin.analysis.index', compact('analysis', 'suppliers'));
    }
}
