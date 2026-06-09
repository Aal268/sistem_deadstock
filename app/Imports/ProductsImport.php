<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new \RuntimeException('File import kosong.');
        }

        DB::transaction(function () use ($rows) {
            // Dapatkan ID berikutnya untuk auto-generate SKU
            $lastProduct = Product::orderBy('id', 'desc')->lockForUpdate()->first();
            $nextId = $lastProduct ? $lastProduct->id + 1 : 1;

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $rowData = $this->normalizeRow($row);

                $sku = isset($rowData['sku']) ? trim((string) $rowData['sku']) : '';
                $name = isset($rowData['nama_barang']) ? trim((string) $rowData['nama_barang']) : '';
                if ($name === '') {
                    // Coba fallback ke nama_produk atau nama
                    $name = trim((string) $this->firstValue($rowData, ['nama_produk', 'produk', 'name', 'nama']));
                }

                $categoryName = trim((string) $this->firstValue($rowData, ['kategori', 'category']));
                $supplierName = trim((string) $this->firstValue($rowData, ['supplier', 'pemasok']));

                $currentStock = $this->firstValue($rowData, ['stok_sekarang', 'stok', 'current_stock', 'quantity', 'qty']);
                $safetyStock = $this->firstValue($rowData, ['stok_aman', 'safety_stock', 'limit_stok']);
                $costPrice = $this->firstValue($rowData, ['harga_beli', 'cost_price', 'harga_modal']);
                $unitPrice = $this->firstValue($rowData, ['harga_jual', 'unit_price', 'harga_eceran']);

                if ($name === '' || $categoryName === '') {
                    throw new \RuntimeException("Baris {$rowNumber} wajib berisi Nama Barang dan Kategori.");
                }

                // Carilah kategori
                $category = Category::where('name', $categoryName)->first();
                if (!$category) {
                    $category = Category::create(['name' => $categoryName]);
                }

                // Carilah supplier (jika diisi)
                $supplier = null;
                if ($supplierName !== '') {
                    $supplier = Supplier::where('name', $supplierName)->first();
                    if (!$supplier) {
                        $supplier = Supplier::create([
                            'name' => $supplierName,
                            'contact_person' => '-',
                            'phone' => '-',
                            'address' => '-'
                        ]);
                    }
                }

                // Cast values
                $currentStock = is_numeric($currentStock) ? (int) $currentStock : 0;
                $safetyStock = is_numeric($safetyStock) ? (int) $safetyStock : 0;
                $costPrice = is_numeric($costPrice) ? (float) $costPrice : 0.0;
                $unitPrice = is_numeric($unitPrice) ? (float) $unitPrice : 0.0;

                // Cari barang berdasarkan SKU
                $product = null;
                if ($sku !== '') {
                    $product = Product::where('sku', $sku)->lockForUpdate()->first();
                }

                if ($product) {
                    // Update produk yang sudah ada
                    $product->update([
                        'name' => $name,
                        'category_id' => $category->id,
                        'supplier_id' => $supplier ? $supplier->id : null,
                        'current_stock' => $currentStock,
                        'safety_stock' => $safetyStock,
                        'cost_price' => $costPrice,
                        'unit_price' => $unitPrice,
                    ]);
                } else {
                    // Buat SKU jika belum diisi
                    if ($sku === '') {
                        $sku = 'PRD-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
                        $nextId++;
                    }

                    Product::create([
                        'sku' => $sku,
                        'name' => $name,
                        'category_id' => $category->id,
                        'supplier_id' => $supplier ? $supplier->id : null,
                        'current_stock' => $currentStock,
                        'safety_stock' => $safetyStock,
                        'cost_price' => $costPrice,
                        'unit_price' => $unitPrice,
                    ]);
                }
            }
        });
    }

    private function normalizeRow(mixed $row): array
    {
        return collect($row)
            ->mapWithKeys(function ($value, $key) {
                $normalizedKey = strtolower(trim((string) $key));
                $normalizedKey = preg_replace('/^\x{FEFF}/u', '', $normalizedKey) ?? $normalizedKey;
                return [$normalizedKey => $value];
            })
            ->all();
    }

    private function firstValue(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }
        return null;
    }
}
