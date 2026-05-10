<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MassiveDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data dalam jumlah besar...');

        // 1. Seed Categories (20 categories)
        $this->command->info('Seeding Categories...');
        $categories = [
            'Kaos Pria', 'Kemeja Pria', 'Celana Panjang Pria', 'Jaket Pria', 'Sepatu Pria',
            'Blus Wanita', 'Dress Wanita', 'Rok Wanita', 'Cardigan Wanita', 'Sepatu Wanita',
            'Pakaian Anak Laki-Laki', 'Pakaian Anak Perempuan', 'Aksesoris Fashion', 'Topi', 'Tas Ransel',
            'Tas Selempang', 'Dompet', 'Pakaian Olahraga', 'Pakaian Dalam', 'Jam Tangan'
        ];
        $categoriesMap = [];
        foreach ($categories as $cat) {
            $category = Category::create([
                'name' => $cat,
                'description' => 'Kategori untuk berbagai macam produk ' . $cat
            ]);
            $categoriesMap[$cat] = $category->id;
        }

        // 2. Seed Suppliers (30 suppliers)
        $this->command->info('Seeding Suppliers...');
        $supplierNames = [
            'PT Maju Bersama', 'CV Karya Mandiri', 'Sumber Rejeki Grosir', 'Toko Kain Makmur', 'Pusat Konveksi Jaya',
            'Global Fashion Supply', 'PT Nusantara Apparel', 'Indo Prima Garment', 'Sentosa Textil', 'Bintang Busana',
            'Mega Bintang Supply', 'Pakaian Kita Vendor', 'Pusat Sepatu Indo', 'CV Sepatu Bandung', 'Tas Kulit Asli',
            'Grosir Tas Batam', 'PT Artha Mandiri', 'Makmur Sentosa Trading', 'Sinar Jaya Grosir', 'PT Berkah Abadi',
            'Vendor Kaos Polos', 'CV Kemeja Keren', 'Grosir Pakaian Anak', 'Baby Kids Supply', 'Grosir Topi Jakarta',
            'PT Aksesoris Keren', 'CV Jam Tangan Murah', 'Sportindo Vendor', 'Underwear Grosir', 'Indo Denim Supply'
        ];
        $supplierIds = [];
        foreach ($supplierNames as $sup) {
            $supplier = Supplier::create([
                'name' => $sup,
                'contact_person' => 'Bpk/Ibu ' . Str::random(5),
                'phone' => '08' . rand(1000000000, 9999999999),
                'address' => 'Jl. ' . Str::random(10) . ' No. ' . rand(1, 100) . ', Jakarta'
            ]);
            $supplierIds[] = $supplier->id;
        }

        // 3. Seed Products (150 products)
        $this->command->info('Seeding Products...');
        $productIds = [];
        $productsToInsert = [];
        
        $brandsAndTypes = [
            'Kaos Pria' => ['Kaos Oblong Erigo', 'T-Shirt Uniqlo', 'Kaos Polos Gildan', 'Polo Shirt Ralph Lauren', 'Kaos Lengan Panjang Zara'],
            'Kemeja Pria' => ['Kemeja Flanel Alisan', 'Kemeja Batik Keris', 'Kemeja Slim Fit H&M', 'Kemeja Oxford Pull&Bear', 'Kemeja Putih Polos'],
            'Celana Panjang Pria' => ['Celana Chino Polo', 'Jeans Slim Fit Levi\'s', 'Celana Bahan Cardinal', 'Jogger Pants Adidas', 'Celana Cargo Eiger'],
            'Jaket Pria' => ['Jaket Bomber', 'Hoodie H&M', 'Jaket Denim Levi\'s', 'Windbreaker Nike', 'Varsity Jacket Puma'],
            'Sepatu Pria' => ['Sneakers Converse', 'Sepatu Lari Nike', 'Loafers Hush Puppies', 'Sepatu Pantofel Kickers', 'Boots Timberland'],
            'Blus Wanita' => ['Blus Batik', 'Blus Korea Style', 'Blus Satin Zara', 'Kemeja Tunik Mango', 'Blus Ruffle Cotton On'],
            'Dress Wanita' => ['Maxi Dress Berrybenka', 'Midi Dress Floral', 'Gaun Malam Elle', 'Dress Rajut Uniqlo', 'Summer Dress H&M'],
            'Rok Wanita' => ['Rok Plisket Mocca', 'Rok Denim Zara', 'Rok Mini Tartan', 'Rok Span Kerja', 'Rok Panjang Chiffon'],
            'Cardigan Wanita' => ['Cardigan Rajut', 'Outer Kardigan', 'Cardigan Crop H&M', 'Cardigan Basic Uniqlo', 'Cardigan Motif'],
            'Sepatu Wanita' => ['Heels Charles & Keith', 'Flat Shoes Melissa', 'Sneakers Vans', 'Sepatu Kets Skechers', 'Wedges Bata'],
            'Pakaian Anak Laki-Laki' => ['Kaos Superhero', 'Celana Pendek Anak', 'Jaket Anak Laki', 'Setelan Baju Tidur', 'Kemeja Anak'],
            'Pakaian Anak Perempuan' => ['Dress Princess', 'Rok Tutu Anak', 'Kaos Karakter Unicorn', 'Legging Anak', 'Setelan Piyama'],
            'Aksesoris Fashion' => ['Kacamata Hitam Ray-Ban', 'Sabuk Kulit', 'Syal Musim Dingin', 'Bandana Cantik', 'Ikat Pinggang Levi\'s'],
            'Topi' => ['Topi Baseball NY', 'Topi Kupluk Beanie', 'Topi Fedora', 'Bucket Hat Champion', 'Topi Polo'],
            'Tas Ransel' => ['Ransel Eiger', 'Ransel Sekolah JanSport', 'Ransel Laptop', 'Backpack Adidas', 'Ransel Consina'],
            'Tas Selempang' => ['Sling Bag Converse', 'Tas Selempang Kulit', 'Tas Kurir', 'Waist Bag Eiger', 'Tas Selempang Kanvas'],
            'Dompet' => ['Dompet Kulit Braun Buffel', 'Dompet Lipat Fossil', 'Dompet Wanita', 'Card Holder', 'Dompet Panjang'],
            'Pakaian Olahraga' => ['Jersey Sepakbola', 'Legging Olahraga Nike', 'Kaos Dry-Fit Adidas', 'Celana Training Puma', 'Sports Bra'],
            'Pakaian Dalam' => ['Boxer Calvin Klein', 'Briefs Rider', 'Singlet GT Man', 'Bra Wacoal', 'Panties Victoria\'s Secret'],
            'Jam Tangan' => ['Casio G-Shock', 'Seiko 5', 'Smartwatch', 'Jam Tangan Fossil', 'Daniel Wellington']
        ];

        for ($i = 1; $i <= 150; $i++) {
            $costPrice = rand(2, 15) * 10000;
            $randomCategoryName = array_rand($categoriesMap);
            $categoryId = $categoriesMap[$randomCategoryName];
            $availableNames = $brandsAndTypes[$randomCategoryName] ?? ['Produk Fashion'];
            $baseName = $availableNames[array_rand($availableNames)];
            $productName = $baseName . ' Model ' . chr(rand(65, 90)) . rand(1, 99);
            
            $productsToInsert[] = [
                'category_id' => $categoryId,
                'supplier_id' => $supplierIds[array_rand($supplierIds)],
                'name' => $productName,
                'sku' => 'SKU-' . strtoupper(Str::random(4)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'current_stock' => rand(10, 500),
                'safety_stock' => rand(5, 50),
                'cost_price' => $costPrice,
                'unit_price' => $costPrice + (rand(1, 10) * 10000), // unit_price instead of selling_price
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Product::insert($productsToInsert);
        $productIds = Product::pluck('id')->toArray();

        // 4. Seed Stock Movements (Massive data from 2023)
        $this->command->info('Seeding Stock Movements...');
        $userId = User::first()->id ?? 1; // Assuming a user exists
        
        $startDate = Carbon::createFromDate(2023, 1, 1);
        $endDate = Carbon::now();
        $daysDiff = $startDate->diffInDays($endDate);

        $movements = [];
        $batchSize = 2000;
        $totalMovements = 0;

        // Create random sales (type: out) every day
        for ($d = 0; $d <= $daysDiff; $d++) {
            $currentDate = $startDate->copy()->addDays($d);
            
            // Generate 10 to 50 transactions per day
            $transactionsToday = rand(10, 50);
            
            for ($t = 0; $t < $transactionsToday; $t++) {
                // Random time between 08:00 and 22:00
                $movementDate = $currentDate->copy()->setHour(rand(8, 21))->setMinute(rand(0, 59))->setSecond(rand(0, 59));
                
                // Usually type 'out' for sales, sometimes 'in' for restock
                $type = (rand(1, 100) > 10) ? 'out' : 'in'; // 90% sales, 10% restock
                
                $qty = ($type === 'out') ? rand(1, 5) : rand(10, 50);
                
                $movements[] = [
                    'product_id' => $productIds[array_rand($productIds)],
                    'user_id' => $userId,
                    'type' => $type,
                    'status' => 'success',
                    'quantity' => $qty,
                    'price_at_transaction' => rand(5, 25) * 10000,
                    'movement_date' => $movementDate,
                    'reference_id' => ($type === 'out' ? 'INV-' : 'PO-') . $movementDate->format('Ymd') . '-' . rand(1000, 9999),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalMovements++;

                if (count($movements) >= $batchSize) {
                    DB::table('stock_movements')->insert($movements);
                    $movements = [];
                }
            }
        }

        // Insert remaining
        if (count($movements) > 0) {
            DB::table('stock_movements')->insert($movements);
        }

        $this->command->info("Seeding selesai! Total {$totalMovements} riwayat pergerakan stok (stock_movements) telah ditambahkan.");
    }
}
