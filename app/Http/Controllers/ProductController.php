<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Exports\ProductsTemplateExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

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

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_stock' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Generate SKU otomatis
        // Ambil ID terakhir, jika kosong jadikan 0
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $generatedSku = 'PRD-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        Product::create([
            'sku' => $generatedSku,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'current_stock' => $request->current_stock,
            'safety_stock' => $request->safety_stock,
            'cost_price' => $request->cost_price,
            'unit_price' => $request->unit_price,
        ]);

        return redirect('/products')->with('success', "Barang baru berhasil ditambahkan dengan SKU: $generatedSku");
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'safety_stock' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $product->update($request->only([
            'name', 'category_id', 'supplier_id', 
            'safety_stock', 'cost_price', 'unit_price'
        ]));

        return redirect('/products')->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->stockMovements()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus barang yang sudah memiliki histori transaksi.');
        }

        $product->delete();
        return back()->with('success', 'Barang berhasil dihapus!');
    }

    public function downloadImportTemplate()
    {
        return Excel::download(
            new ProductsTemplateExport(),
            'template-import-barang.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new ProductsImport(), $request->file('file'));

            return back()->with('success', 'Data barang berhasil diimpor.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
