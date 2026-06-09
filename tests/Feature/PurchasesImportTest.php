<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Imports\PurchasesImport;
use App\Exports\PurchasesTemplateExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PurchasesImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_export_contains_correct_headings(): void
    {
        $export = new PurchasesTemplateExport();
        $this->assertEquals([
            'tanggal',
            'sku',
            'nama_produk',
            'qty',
            'catatan',
        ], $export->headings());

        $array = $export->array();
        $this->assertEquals('Kabel Data Type C', $array[0][2]);
    }

    public function test_import_purchases_using_sku_successfully(): void
    {
        $user = User::create([
            'name' => 'Gudang Test',
            'email' => 'gudang@test.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Elektronik']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-00001',
            'name' => 'Kabel Data Type C',
            'current_stock' => 10,
            'safety_stock' => 2,
            'cost_price' => 5000,
            'unit_price' => 10000,
        ]);

        $rows = new Collection([
            [
                'tanggal' => '2026-06-08',
                'sku' => 'PRD-00001',
                'qty' => 15,
                'catatan' => 'Test restock by SKU',
            ]
        ]);

        $importer = new PurchasesImport();
        $importer->collection($rows);

        $product->refresh();
        $this->assertEquals(25, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 15,
            'note' => 'Test restock by SKU',
        ]);
    }

    public function test_import_purchases_using_name_successfully(): void
    {
        $user = User::create([
            'name' => 'Gudang Test',
            'email' => 'gudang@test.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Elektronik']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-00001',
            'name' => 'Kabel Data Type C',
            'current_stock' => 10,
            'safety_stock' => 2,
            'cost_price' => 5000,
            'unit_price' => 10000,
        ]);

        $rows = new Collection([
            [
                'tanggal' => '2026-06-08',
                'nama_produk' => 'Kabel Data Type C',
                'qty' => 5,
                'catatan' => 'Test restock by Name',
            ]
        ]);

        $importer = new PurchasesImport();
        $importer->collection($rows);

        $product->refresh();
        $this->assertEquals(15, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'note' => 'Test restock by Name',
        ]);
    }

    public function test_import_purchases_fails_when_product_not_found(): void
    {
        $rows = new Collection([
            [
                'tanggal' => '2026-06-08',
                'sku' => 'PRD-XXXXX',
                'qty' => 5,
            ]
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Baris 2: Produk dengan SKU 'PRD-XXXXX' tidak ditemukan.");

        $importer = new PurchasesImport();
        $importer->collection($rows);
    }
}
