<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Exports\SalesReportTemplateExport;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->where('current_stock', '>', 0)->get();
        
        $query = StockMovement::with('product', 'user')->where('type', 'out');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $recentSales = $query->orderBy('movement_date', 'desc')
                        ->orderBy('id', 'desc')
                        ->paginate(10)
                        ->withQueryString();

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

    public function export(Request $request)
    {
        try {
            $allSales = $this->buildSalesQuery($request)
                            ->orderBy('movement_date', 'desc')
                            ->orderBy('id', 'desc')
                            ->get();

            $fileName = 'riwayat-penjualan-' . now()->format('Ymd-His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->streamDownload(function () use ($allSales) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for proper Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Headings
                fputcsv($file, [
                    'Waktu',
                    'Kasir',
                    'SKU',
                    'Produk',
                    'Kategori',
                    'Catatan',
                    'Qty',
                    'Harga Satuan',
                    'Total',
                    'Status',
                ]);

                // Data
                foreach ($allSales as $sale) {
                    $subtotal = $sale->quantity * ($sale->price_at_transaction ?? $sale->product->unit_price);
                    fputcsv($file, [
                        \Carbon\Carbon::parse($sale->movement_date)->format('Y-m-d H:i:s'),
                        $sale->user->name ?? '-',
                        $sale->product->sku ?? '-',
                        $sale->product->name ?? 'Produk Dihapus',
                        $sale->product->category->name ?? '-',
                        $sale->note ?? '-',
                        $sale->quantity,
                        $sale->price_at_transaction ?? $sale->product->unit_price,
                        $subtotal,
                        $sale->status ?? 'Success',
                    ]);
                }

                fclose($file);
            }, $fileName, $headers);
        } catch (\Throwable $e) {
            file_put_contents(storage_path('logs/export-error.log'), $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    public function downloadImportTemplate()
    {
        return Excel::download(
            new SalesReportTemplateExport(),
            'template-import-laporan-penjualan.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new \App\Imports\SalesReportImport(), $request->file('file'));

            return back()->with('success', 'Laporan penjualan berhasil diimpor.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
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

    private function buildSalesQuery(Request $request, array $relations = ['product.category', 'user'])
    {
        $query = StockMovement::with($relations)->where('type', 'out');

        if ($request->filled('start_date')) {
            $query->whereDate('movement_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('movement_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($productQuery) use ($search) {
                $productQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
