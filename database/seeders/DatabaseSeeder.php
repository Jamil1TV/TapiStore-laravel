<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = '$2y$10$eEGsorHO5IH5.JjTmQZpReZMbr8eqa3zP0LJ2pltoYoWqPg7piPTS';

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@tapi.com'],
            ['full_name' => 'Admin User', 'password' => $password, 'role' => 'admin', 'phone' => '+1234567890', 'address' => '123 Admin Street, HQ City']
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'john@example.com'],
            ['full_name' => 'John Customer', 'password' => $password, 'role' => 'customer', 'phone' => '+0987654321', 'address' => '456 Customer Ave, Shopville']
        );

        $categories = [
            'electronics' => ['name' => 'Electronics', 'description' => 'Latest gadgets, devices and electronic accessories for modern living.', 'image' => 'electronics.jpg'],
            'fashion' => ['name' => 'Fashion', 'description' => 'Trendy clothing, shoes and accessories for every style and occasion.', 'image' => 'fashion.jpg'],
            'home-living' => ['name' => 'Home & Living', 'description' => 'Beautiful furniture, decor and essentials to make your house a home.', 'image' => 'home-living.jpg'],
            'sports-outdoors' => ['name' => 'Sports & Outdoors', 'description' => 'Equipment and gear for fitness, sports and outdoor adventures.', 'image' => 'sports-outdoors.jpg'],
        ];

        foreach ($categories as $slug => $category) {
            DB::table('categories')->updateOrInsert(['slug' => $slug], array_merge($category, ['slug' => $slug]));
        }

        $categoryIds = DB::table('categories')->pluck('id', 'slug');

        $products = [
            ['electronics', 'Wireless Bluetooth Headphones', 'wireless-bluetooth-headphones', 'Premium noise-cancelling wireless headphones with 30-hour battery life. Features deep bass, crystal-clear highs, and comfortable over-ear design.', 89.99, 69.99, 45, 'headphones.jpg', 1],
            ['electronics', 'Smart Watch Pro', 'smart-watch-pro', 'Advanced smartwatch with heart rate monitor, GPS tracking, sleep analysis, and 7-day battery. Water resistant to 50m.', 199.99, 179.99, 30, 'smartwatch.jpg', 1],
            ['electronics', 'Portable Bluetooth Speaker', 'portable-bluetooth-speaker', 'Compact waterproof speaker with 360-degree sound, 12-hour playtime, and built-in microphone for hands-free calls.', 49.99, null, 60, 'speaker.jpg', 0],
            ['electronics', '4K Action Camera', '4k-action-camera', 'Ultra HD 4K action camera with electronic image stabilization, waterproof case, and wide-angle lens. Perfect for adventures.', 129.99, 99.99, 25, 'camera.jpg', 1],
            ['fashion', 'Classic Leather Jacket', 'classic-leather-jacket', 'Genuine leather jacket with quilted lining, multiple pockets, and timeless design. Available in black and brown.', 249.99, 199.99, 15, 'jacket.jpg', 1],
            ['fashion', 'Running Sneakers Ultra', 'running-sneakers-ultra', 'Lightweight mesh sneakers with responsive cushioning, breathable upper, and durable rubber outsole. Ideal for daily runs.', 119.99, null, 50, 'sneakers.jpg', 0],
            ['fashion', 'Premium Cotton T-Shirt', 'premium-cotton-tshirt', '100% organic cotton crew-neck t-shirt. Pre-shrunk, soft hand feel, and available in 8 colors.', 29.99, 24.99, 100, 'tshirt.jpg', 0],
            ['home-living', 'Minimalist Desk Lamp', 'minimalist-desk-lamp', 'LED desk lamp with adjustable brightness, color temperature control, and USB charging port. Sleek aluminum design.', 59.99, 49.99, 35, 'lamp.jpg', 1],
            ['home-living', 'Ceramic Plant Pot Set', 'ceramic-plant-pot-set', 'Set of 3 modern ceramic plant pots with bamboo saucers. Drainage holes included. Matte white finish.', 39.99, null, 40, 'pots.jpg', 0],
            ['home-living', 'Luxury Scented Candle', 'luxury-scented-candle', 'Hand-poured soy wax candle with essential oils. 60-hour burn time. Available in Lavender, Vanilla, and Cedar scents.', 24.99, 19.99, 80, 'candle.jpg', 0],
            ['sports-outdoors', 'Yoga Mat Premium', 'yoga-mat-premium', 'Extra thick 6mm yoga mat with alignment lines, non-slip surface, and carrying strap. Eco-friendly TPE material.', 34.99, 29.99, 55, 'yogamat.jpg', 1],
            ['sports-outdoors', 'Stainless Steel Water Bottle', 'stainless-steel-water-bottle', 'Double-wall vacuum insulated bottle. Keeps drinks cold 24hrs or hot 12hrs. 750ml capacity, BPA-free.', 27.99, null, 70, 'bottle.jpg', 0],
        ];

        foreach ($products as [$categorySlug, $name, $slug, $description, $price, $discount, $stock, $image, $featured]) {
            DB::table('products')->updateOrInsert(
                ['slug' => $slug],
                [
                    'category_id' => $categoryIds[$categorySlug],
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'price' => $price,
                    'discount_price' => $discount,
                    'stock_quantity' => $stock,
                    'image' => $image,
                    'is_featured' => $featured,
                    'status' => 'active',
                ]
            );
        }

        if (DB::table('reviews')->count() === 0) {
            $customerId = DB::table('users')->where('email', 'john@example.com')->value('id');
            $productIds = DB::table('products')->pluck('id', 'slug');
            DB::table('reviews')->insert([
                ['product_id' => $productIds['wireless-bluetooth-headphones'], 'user_id' => $customerId, 'rating' => 5, 'comment' => 'Amazing sound quality! The noise cancellation is incredible. Best headphones I have ever owned.'],
                ['product_id' => $productIds['wireless-bluetooth-headphones'], 'user_id' => $customerId, 'rating' => 4, 'comment' => 'Very comfortable for long listening sessions. Battery life is impressive.'],
                ['product_id' => $productIds['smart-watch-pro'], 'user_id' => $customerId, 'rating' => 5, 'comment' => 'This smartwatch exceeded my expectations. The fitness tracking is very accurate.'],
                ['product_id' => $productIds['4k-action-camera'], 'user_id' => $customerId, 'rating' => 4, 'comment' => 'Great camera for the price. Video quality is stunning in 4K mode.'],
                ['product_id' => $productIds['classic-leather-jacket'], 'user_id' => $customerId, 'rating' => 5, 'comment' => 'The leather quality is superb. Fits perfectly and looks amazing.'],
                ['product_id' => $productIds['minimalist-desk-lamp'], 'user_id' => $customerId, 'rating' => 4, 'comment' => 'Beautiful lamp, the USB charging port is a nice bonus.'],
                ['product_id' => $productIds['yoga-mat-premium'], 'user_id' => $customerId, 'rating' => 5, 'comment' => 'Best yoga mat I have used. The alignment lines really help with positioning.'],
            ]);
        }
    }
}