<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('products')->paginate(10);
        $query = Supplier::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Supplier::create($request->all());

        return back()->with('success', 'Pemasok baru berhasil ditambahkan!');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $supplier->update($request->all());

        return redirect('/suppliers')->with('success', 'Data pemasok berhasil diperbarui!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus pemasok yang masih menyediakan barang.');
        }

        $supplier->delete();
        return back()->with('success', 'Data pemasok berhasil dihapus!');
    }
}
