<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('products')->paginate(10);
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
