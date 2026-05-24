<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === "administrator") {
            // Kasir Dashboard: Data penjualan bulanan & grafik per hari
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $salesThisMonth = StockMovement::where("type", "out")
                ->whereBetween("movement_date", [$startOfMonth, $endOfMonth])
                ->get();

            $totalItemSold = $salesThisMonth->sum("quantity");

            // Persiapkan data grafik (Tanggal 1 - 31)
            $chartLabels = [];
            $chartData = [];
            $daysInMonth = Carbon::now()->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $chartLabels[] = "Tgl $i";
                $dateString =
                    Carbon::now()->format("Y-m-") .
                    str_pad($i, 2, "0", STR_PAD_LEFT);

                $soldOnDay = $salesThisMonth
                    ->where("movement_date", ">=", $dateString . " 00:00:00")
                    ->where("movement_date", "<=", $dateString . " 23:59:59")
                    ->sum("quantity");
                $chartData[] = $soldOnDay;
            }

            return view(
                "kasir.dashboard.kasir",
                compact("totalItemSold", "chartLabels", "chartData"),
            );
        } elseif ($user->role === "gudang") {
            // Gudang Dashboard: Menggunakan GudangController agar sinkron dengan filter dan paginasi
            return app(\App\Http\Controllers\GudangController::class)->index(request());
        } elseif ($user->role === "admin") {
            // Admin Dashboard: Ringkasan stok & Analisis mendalam
            $products = Product::with("stockMovements")->get();

            $totalProducts = $products->count();
            $totalStockValue = $products->sum(function ($p) {
                return $p->current_stock * $p->cost_price;
            });

            // Perhitungan Analisis untuk Dashboard (Decision Support)
            $threeMonthsAgo = Carbon::now()->subMonths(3);
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $analysisData = $products->map(function ($p) use ($threeMonthsAgo) {
                $totalOutLast3Months = $p->stockMovements
                    ->where("type", "out")
                    ->where("movement_date", ">=", $threeMonthsAgo)
                    ->sum("quantity");

                $avgMonthlySales = round($totalOutLast3Months / 3);

                // Logika Status (Sesuai algoritma yang ada)
                $status = "Normal";
                if ($totalOutLast3Months == 0) {
                    $status = "Deadstock";
                } elseif ($p->current_stock < $avgMonthlySales * 1.5) {
                    $status = "Fast-Moving";
                } else {
                    $status = "Slow-Moving";
                }

                // Target stok bulan depan = avg + safety stock
                $targetStock = ceil($avgMonthlySales + $p->safety_stock);
                $suggestedBuy = max(0, $targetStock - $p->current_stock);
                if ($status == "Deadstock") {
                    $suggestedBuy = 0;
                }

                return [
                    "product" => $p,
                    "avg_monthly_sales" => $avgMonthlySales,
                    "status" => $status,
                    "suggested_buy" => $suggestedBuy,
                ];
            });

            // 1. Tiga barang dengan rata-rata tertinggi (Top Performers)
            $top3Products = $analysisData
                ->sortByDesc("avg_monthly_sales")
                ->take(3);

            // 2. Jumlah barang yang jadi prioritas untuk dibeli (suggested_buy > 0)
            $priorityBuyCount = $analysisData
                ->where("suggested_buy", ">", 0)
                ->count();

            // 3. Item Urgent (Highlight): Fast-Moving stok tipis atau Deadstock
            $urgentItems = $analysisData
                ->filter(function ($item) {
                    return ($item["status"] == "Fast-Moving" &&
                        $item["product"]->current_stock <=
                            $item["product"]->safety_stock) ||
                        $item["status"] == "Deadstock";
                })
                ->take(5);

            // 4. Grafik Penjualan Harian (Track harian terjual)
            // Data diambil secara asinkron (AJAX) untuk optimasi performa dan mendukung filter dinamis
            $chartLabels = [];
            $chartData = [];

            // Stats Dasar
            $totalSoldAllTime = StockMovement::where("type", "out")->sum(
                "quantity",
            );
            $totalRemainingStock = $products->sum("current_stock");
            $criticalStockItems = $products
                ->filter(function ($p) {
                    return $p->current_stock <= $p->safety_stock;
                })
                ->count();

            return view(
                "admin.dashboard.admin",
                compact(
                    "totalProducts",
                    "totalStockValue",
                    "criticalStockItems",
                    "totalSoldAllTime",
                    "totalRemainingStock",
                    "top3Products",
                    "priorityBuyCount",
                    "urgentItems",
                    "chartLabels",
                    "chartData",
                ),
            );
        } else {
            return abort(403, "Unauthorized role.");
        }
    }

    /**
     * AJAX endpoint — data grafik penjualan dengan berbagai periode.
     * GET /dashboard/chart-data
     *   ?period = today | week | month | custom_month | year
     *   &year   = 2026   (untuk custom_month & year)
     *   &month  = 5      (untuk custom_month)
     */
    public function chartData(Request $request): JsonResponse
    {
        $period = $request->get("period", "month");
        $year = max(
            2000,
            min(
                (int) $request->get("year", Carbon::now()->year),
                Carbon::now()->year,
            ),
        );
        $month = max(
            1,
            min((int) $request->get("month", Carbon::now()->month), 12),
        );

        $labels = [];
        $data = [];
        $title = "";
        $chartType = "line";

        switch ($period) {
            /* ---- Hari Ini: penjualan per jam 00:00 - 23:59 (line) ---------- */
            case "today":
                $rows = StockMovement::where("type", "out")
                    ->whereDate("movement_date", Carbon::today())
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->format("H");
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                for ($h = 0; $h < 24; $h++) {
                    $labels[] = str_pad($h, 2, "0", STR_PAD_LEFT) . ':00';
                    $data[] = (int) ($rows[str_pad($h, 2, "0", STR_PAD_LEFT)] ?? 0);
                }

                $title =
                    "Hari Ini — " .
                    Carbon::today()->translatedFormat("d F Y");
                $chartType = "line";
                break;

            /* ---- Minggu Ini: per hari Sen–Min (bar) --------------------- */
            case "week":
                $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

                $rows = StockMovement::where("type", "out")
                    ->whereBetween("movement_date", [
                        $startOfWeek->copy()->startOfDay(),
                        $endOfWeek->copy()->endOfDay(),
                    ])
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->toDateString();
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                $dayNames = ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"];
                for ($i = 0; $i < 7; $i++) {
                    $day = $startOfWeek->copy()->addDays($i);
                    $labels[] = $dayNames[$i] . " " . $day->format("d/m");
                    $data[] = (int) ($rows[$day->toDateString()] ?? 0);
                }

                $title =
                    "Minggu Ini (" .
                    $startOfWeek->format("d") .
                    "-" .
                    $endOfWeek->format("d M Y") .
                    ")";
                $chartType = "bar";
                break;

            /* ---- Bulan Ini: per hari (line) ----------------------------- */
            case "month":
                $now = Carbon::now();
                $startOfMonth = $now->copy()->startOfMonth()->toDateString();
                $endOfMonth = $now->copy()->endOfMonth()->toDateString();
                $daysInMonth = $now->daysInMonth;

                $rows = StockMovement::where("type", "out")
                    ->whereBetween("movement_date", [
                        $now->copy()->startOfMonth()->startOfDay(),
                        $now->copy()->endOfMonth()->endOfDay(),
                    ])
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->format("d");
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $labels[] = (string) $i;
                    $data[] =
                        (int) ($rows[str_pad($i, 2, "0", STR_PAD_LEFT)] ?? 0);
                }

                $title = "Bulan " . $now->translatedFormat("F Y");
                $chartType = "line";
                break;

            /* ---- Pilih Bulan: per hari, bulan & tahun bebas (line) ------ */
            case "custom_month":
                $date = Carbon::createFromDate($year, $month, 1);
                $startOfMonth = $date->copy()->startOfMonth()->toDateString();
                $endOfMonth = $date->copy()->endOfMonth()->toDateString();
                $daysInMonth = $date->daysInMonth;

                $rows = StockMovement::where("type", "out")
                    ->whereBetween("movement_date", [
                        $date->copy()->startOfMonth()->startOfDay(),
                        $date->copy()->endOfMonth()->endOfDay(),
                    ])
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->format("d");
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $labels[] = (string) $i;
                    $data[] =
                        (int) ($rows[str_pad($i, 2, "0", STR_PAD_LEFT)] ?? 0);
                }

                $title = $date->translatedFormat("F Y");
                $chartType = "line";
                break;

            /* ---- Pilih Tahun: per bulan Jan-Des (bar) ------------------- */
            case "year":
                $monthNames = [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "Mei",
                    "Jun",
                    "Jul",
                    "Agu",
                    "Sep",
                    "Okt",
                    "Nov",
                    "Des",
                ];

                $rows = StockMovement::where("type", "out")
                    ->whereYear("movement_date", $year)
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->format("m");
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = $monthNames[$m - 1];
                    $data[] =
                        (int) ($rows[str_pad($m, 2, "0", STR_PAD_LEFT)] ?? 0);
                }

                $title = "Tahun " . $year;
                $chartType = "bar";
                break;

            /* ---- Dekade: penjualan 10 tahun (bar) ----------------------- */
            case "decade":
                $startDecade = $year - ($year % 10);
                $endDecade = $startDecade + 9;

                $startBound = Carbon::createFromDate($startDecade, 1, 1)->startOfDay()->toDateString();
                $endBound = Carbon::createFromDate($endDecade, 12, 31)->endOfDay()->toDateString();

                $rows = StockMovement::where("type", "out")
                    ->whereBetween("movement_date", [
                        Carbon::createFromDate($startDecade, 1, 1)->startOfDay(),
                        Carbon::createFromDate($endDecade, 12, 31)->endOfDay(),
                    ])
                    ->get(["movement_date", "quantity"])
                    ->groupBy(function ($movement) {
                        return Carbon::parse($movement->movement_date)->format("Y");
                    })
                    ->map(function ($group) {
                        return $group->sum("quantity");
                    });

                for ($y = $startDecade; $y <= $endDecade; $y++) {
                    $labels[] = (string) $y;
                    $data[] = (int) ($rows[(string) $y] ?? 0);
                }

                $title = "Dekade " . $startDecade . " - " . $endDecade;
                $chartType = "bar";
                break;

            default:
                $labels = [];
                $data = [];
                $title = "";
                $chartType = "line";
        }

        return response()->json([
            "labels" => $labels,
            "data" => $data,
            "title" => $title,
            "chartType" => $chartType,
        ]);
    }
}
