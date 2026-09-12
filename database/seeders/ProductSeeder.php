<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 16',
                'description' => 'High-end workstation for developers',
                'price' => 1500.00,
                'stock' => 10,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic mouse with silent clicks',
                'price' => 45.99,
                'stock' => 50,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit with blue switches',
                'price' => 89.00,
                'stock' => 5, // Low stock to test logic
            ],
            [
                'name' => 'UltraWide Monitor',
                'description' => '34-inch curved gaming monitor',
                'price' => 450.00,
                'stock' => 2, // Very low stock
            ],
            [
                'name' => 'Out of Stock Item',
                'description' => 'This product cannot be ordered',
                'price' => 10.00,
                'stock' => 0, // Out of stock to test validation
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}