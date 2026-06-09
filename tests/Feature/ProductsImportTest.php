<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Imports\ProductsImport;
use App\Exports\ProductsTemplateExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProductsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_export_contains_correct_headings(): void
    {
        $export = new ProductsTemplateExport();
        $this->assertEquals([
            'sku',
            'nama_barang',
            'kategori',
            'supplier',
            'stok_sekarang',
            'stok_aman',
            'harga_beli',
            'harga_jual',
        ], $export->headings());

        $array = $export->array();
        $this->assertEquals('Kabel Data Type C', $array[0][1]);
        $this->assertEquals('Kemeja Polos Pria', $array[1][1]);
    }

    public function test_import_new_product_and_create_category_supplier(): void
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($user);

        // Prepare row without SKU, it should auto-generate
        $rows = new Collection([
            [
                'nama_barang' => 'Charger SuperVOOC 65W',
                'kategori' => 'Aksesoris HP',
                'supplier' => 'PT. Oppo Manufacturing',
                'stok_sekarang' => 15,
                'stok_aman' => 3,
                'harga_beli' => 120000,
                'harga_jual' => 199000,
            ]
        ]);

        $importer = new ProductsImport();
        $importer->collection($rows);

        // Assert database category and supplier created
        $this->assertDatabaseHas('categories', ['name' => 'Aksesoris HP']);
        $this->assertDatabaseHas('suppliers', ['name' => 'PT. Oppo Manufacturing']);

        // Assert product created with auto SKU
        $product = Product::where('name', 'Charger SuperVOOC 65W')->first();
        $this->assertNotNull($product);
        $this->assertEquals('PRD-00001', $product->sku);
        $this->assertEquals(15, $product->current_stock);
        $this->assertEquals(3, $product->safety_stock);
        $this->assertEquals(120000, $product->cost_price);
        $this->assertEquals(199000, $product->unit_price);
    }

    public function test_import_updates_existing_product_when_sku_matches(): void
    {
        $category = Category::create(['name' => 'Elektronik']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-99999',
            'name' => 'Old Product Name',
            'current_stock' => 5,
            'safety_stock' => 1,
            'cost_price' => 1000,
            'unit_price' => 2000,
        ]);

        $rows = new Collection([
            [
                'sku' => 'PRD-99999',
                'nama_barang' => 'New Product Name',
                'kategori' => 'Elektronik',
                'supplier' => '',
                'stok_sekarang' => 50,
                'stok_aman' => 10,
                'harga_beli' => 1500,
                'harga_jual' => 3000,
            ]
        ]);

        $importer = new ProductsImport();
        $importer->collection($rows);

        $product->refresh();
        $this->assertEquals('New Product Name', $product->name);
        $this->assertEquals(50, $product->current_stock);
        $this->assertEquals(10, $product->safety_stock);
        $this->assertEquals(1500, $product->cost_price);
        $this->assertEquals(3000, $product->unit_price);
        $this->assertNull($product->supplier_id);
    }

    public function test_import_fails_on_missing_required_fields(): void
    {
        $rows = new Collection([
            [
                'nama_barang' => '', // Name is missing
                'kategori' => 'Elektronik',
            ]
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Baris 2 wajib berisi Nama Barang dan Kategori.");

        $importer = new ProductsImport();
        $importer->collection($rows);
    }
}
