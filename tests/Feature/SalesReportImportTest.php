<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Imports\SalesReportImport;
use App\Exports\SalesReportExport;
use App\Exports\SalesReportTemplateExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SalesReportImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_export_contains_new_headings(): void
    {
        $export = new SalesReportTemplateExport();
        $this->assertEquals([
            'tanggal_waktu',
            'nama_produk',
            'qty',
            'catatan',
        ], $export->headings());

        $array = $export->array();
        $this->assertEquals('Kabel Data Type C', $array[0][1]);
        $this->assertEquals('Kemeja Polos Pria', $array[1][1]);
    }

    public function test_import_using_product_name_successfully(): void
    {
        // 1. Setup data
        $user = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
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

        // 2. Prepare import data
        $rows = new Collection([
            [
                'tanggal_waktu' => '2026-06-04 10:00:00',
                'nama_produk' => 'Kabel Data Type C',
                'qty' => 3,
                'catatan' => 'Test import by name',
            ]
        ]);

        // 3. Process import
        $importer = new SalesReportImport();
        $importer->collection($rows);

        // 4. Assert database states
        $product->refresh();
        $this->assertEquals(7, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 3,
            'note' => 'Test import by name',
        ]);
    }

    public function test_import_with_fallback_to_sku_successfully(): void
    {
        // 1. Setup data
        $user = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
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

        // 2. Prepare import data with sku instead of nama_produk
        $rows = new Collection([
            [
                'tanggal_waktu' => '2026-06-04 10:00:00',
                'sku' => 'PRD-00001',
                'qty' => 2,
                'catatan' => 'Test import by sku fallback',
            ]
        ]);

        // 3. Process import
        $importer = new SalesReportImport();
        $importer->collection($rows);

        // 4. Assert database states
        $product->refresh();
        $this->assertEquals(8, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
            'note' => 'Test import by sku fallback',
        ]);
    }

    public function test_import_fails_when_stock_insufficient(): void
    {
        // 1. Setup data
        $user = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
        ]);
        $this->actingAs($user);

        $category = Category::create(['name' => 'Elektronik']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-00001',
            'name' => 'Kabel Data Type C',
            'current_stock' => 5,
            'safety_stock' => 2,
            'cost_price' => 5000,
            'unit_price' => 10000,
        ]);

        // 2. Prepare import data with qty > stock
        $rows = new Collection([
            [
                'tanggal_waktu' => '2026-06-04 10:00:00',
                'nama_produk' => 'Kabel Data Type C',
                'qty' => 6,
                'catatan' => 'Stok not enough',
            ]
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Baris 2: stok Kabel Data Type C tidak mencukupi.");

        $importer = new SalesReportImport();
        $importer->collection($rows);
    }

    public function test_admin_can_download_sales_report(): void
    {
        $user = User::create([
            'name' => 'Admin Owner',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('histori-sales.export'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'attachment; filename=riwayat-penjualan-')
        );
    }

    public function test_kasir_can_download_sales_report(): void
    {
        $user = User::create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@test.com',
            'password' => bcrypt('password'),
            'role' => 'administrator',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('histori-sales.export'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'attachment; filename=riwayat-penjualan-')
        );
    }
}
