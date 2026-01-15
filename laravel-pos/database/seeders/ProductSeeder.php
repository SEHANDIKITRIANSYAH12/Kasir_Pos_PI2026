<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Paku 5 cm', 'sku' => 'PK-005', 'price' => 15000, 'stock' => 500],
            ['name' => 'Semen 40kg', 'sku' => 'SM-040', 'price' => 65000, 'stock' => 200],
            ['name' => 'Cat Tembok 5kg', 'sku' => 'CT-005', 'price' => 90000, 'stock' => 100],
            ['name' => 'Kayu Balok 2x4', 'sku' => 'KY-24', 'price' => 35000, 'stock' => 150],
            ['name' => 'Besi Beton 10mm', 'sku' => 'BB-010', 'price' => 75000, 'stock' => 120],
            ['name' => 'Triplek 3mm', 'sku' => 'TP-003', 'price' => 45000, 'stock' => 80],
            ['name' => 'Keramik 40x40', 'sku' => 'KR-4040', 'price' => 60000, 'stock' => 60],
            ['name' => 'Kuas Cat 2 inch', 'sku' => 'KC-002', 'price' => 12000, 'stock' => 300],
            ['name' => 'Pasir 1 Sak', 'sku' => 'PS-001', 'price' => 25000, 'stock' => 100],
            ['name' => 'Batu Bata', 'sku' => 'BT-001', 'price' => 800, 'stock' => 2000],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
