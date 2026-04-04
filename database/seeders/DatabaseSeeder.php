<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default Users
        User::create([
            'name' => 'Manager Pemilik',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Kasir Toko',
            'email' => 'kasir@test.com',
            'password' => Hash::make('password'),
            'role' => 'administrator'
        ]);

        $categories = [
            Category::create(['name' => 'Elektronik']),
            Category::create(['name' => 'Pakaian']),
            Category::create(['name' => 'Makanan Ringan'])
        ];

        $suppliers = [
            Supplier::create(['name' => 'PT Makmur Jaya']),
            Supplier::create(['name' => 'CV Abadi Sentosa']),
        ];

        // Seeding Products
        // 1. Fast Moving Product
        $p1 = Product::create([
            'category_id' => $categories[0]->id,
            'supplier_id' => $suppliers[0]->id,
            'sku' => 'ELK-001',
            'name' => 'Kabel Data Type C',
            'current_stock' => 20, // Low stock, fast moving
            'safety_stock' => 50,
            'cost_price' => 10000,
            'unit_price' => 25000
        ]);

        // 2. Slow Moving Product
        $p2 = Product::create([
            'category_id' => $categories[1]->id,
            'supplier_id' => $suppliers[1]->id,
            'sku' => 'PKN-001',
            'name' => 'Kemeja Polos Pria',
            'current_stock' => 150, // High stock, slow moving
            'safety_stock' => 20,
            'cost_price' => 50000,
            'unit_price' => 100000
        ]);

        // 3. Deadstock Product
        $p3 = Product::create([
            'category_id' => $categories[2]->id,
            'supplier_id' => $suppliers[0]->id,
            'sku' => 'MKN-001',
            'name' => 'Keripik Singkong Level 10',
            'current_stock' => 300, // Very high stock, no sales
            'safety_stock' => 10,
            'cost_price' => 5000,
            'unit_price' => 12000
        ]);

        // Seeding Stock Movements (Last 3 months)
        $now = Carbon::now();

        // 1. Fast Moving (High sales)
        for ($i = 0; $i < 30; $i++) {
            StockMovement::create([
                'product_id' => $p1->id,
                'type' => 'out',
                'quantity' => rand(5, 15), // Total terjual banyak
                'movement_date' => $now->copy()->subDays(rand(1, 90)),
                'note' => 'Penjualan'
            ]);
        }

        // 2. Slow Moving (Low sales)
        for ($i = 0; $i < 5; $i++) {
            StockMovement::create([
                'product_id' => $p2->id,
                'type' => 'out',
                'quantity' => rand(1, 3), // Total terjual sedikit
                'movement_date' => $now->copy()->subDays(rand(1, 90)),
                'note' => 'Penjualan'
            ]);
        }

        // 3. Deadstock (No sales in last 3 months)
        // No 'out' movements created
        
        // Buat data masuk (In) awal untuk logikanya
        StockMovement::create(['product_id' => $p1->id, 'type' => 'in', 'quantity' => 200, 'movement_date' => $now->copy()->subDays(95), 'note' => 'Stok Awal']);
        StockMovement::create(['product_id' => $p2->id, 'type' => 'in', 'quantity' => 160, 'movement_date' => $now->copy()->subDays(95), 'note' => 'Stok Awal']);
        StockMovement::create(['product_id' => $p3->id, 'type' => 'in', 'quantity' => 300, 'movement_date' => $now->copy()->subDays(95), 'note' => 'Stok Awal']);
    }
}
