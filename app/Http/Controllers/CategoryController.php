<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        Category::create($request->all());

        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang masih memiliki barang.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
