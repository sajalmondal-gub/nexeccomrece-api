<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. SEED ROLES & PERMISSIONS ---
        $permissions = [
            'manage_products',
            'manage_orders',
            'manage_users',
            'manage_roles',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $supportRole = Role::firstOrCreate(['name' => 'Support']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);

        // Give all permissions to Super Admin
        $superAdminRole->syncPermissions($permissions);

        // Give specific permissions to Admin/Manager/Support
        $adminRole->syncPermissions(['manage_products', 'manage_orders', 'view_reports']);
        $managerRole->syncPermissions(['manage_products', 'manage_orders']);
        $supportRole->syncPermissions(['manage_orders']);

        // --- 2. SEED USERS ---
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@nexcommerce.com'],
            [
                'name' => 'Alex Mercer',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Support
        $support = User::firstOrCreate(
            ['email' => 'support@nexcommerce.com'],
            [
                'name' => 'Sarah Connor',
                'password' => Hash::make('password'),
            ]
        );
        $support->assignRole($supportRole);

        // Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@nexcommerce.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
            ]
        );
        $customer->assignRole($customerRole);


        // --- 3. SEED BRANDS ---
        $auraTech = Brand::firstOrCreate(
            ['slug' => 'auratech'],
            ['name' => 'AuraTech', 'logo' => 'auratech.png', 'is_active' => true]
        );

        $luxeVibe = Brand::firstOrCreate(
            ['slug' => 'luxevibe'],
            ['name' => 'LuxeVibe', 'logo' => 'luxevibe.png', 'is_active' => true]
        );

        $nebula = Brand::firstOrCreate(
            ['slug' => 'nebula'],
            ['name' => 'Nebula', 'logo' => 'nebula.png', 'is_active' => true]
        );


        // --- 4. SEED CATEGORIES ---
        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => 'Electronics', 'icon' => 'bolt', 'is_active' => true]
        );

        $audio = Category::firstOrCreate(
            ['slug' => 'audio'],
            [
                'name' => 'Audio',
                'parent_id' => $electronics->id,
                'icon' => 'music',
                'is_active' => true
            ]
        );

        $wearables = Category::firstOrCreate(
            ['slug' => 'wearables'],
            [
                'name' => 'Wearables',
                'parent_id' => $electronics->id,
                'icon' => 'watch',
                'is_active' => true
            ]
        );

        $fashion = Category::firstOrCreate(
            ['slug' => 'fashion'],
            ['name' => 'Fashion', 'icon' => 'shirt', 'is_active' => true]
        );

        $apparel = Category::firstOrCreate(
            ['slug' => 'apparel'],
            [
                'name' => 'Apparel',
                'parent_id' => $fashion->id,
                'icon' => 'hanger',
                'is_active' => true
            ]
        );

        $homeLiving = Category::firstOrCreate(
            ['slug' => 'home-living'],
            ['name' => 'Home & Living', 'icon' => 'home', 'is_active' => true]
        );


        // --- 5. SEED PRODUCTS ---
        // Product 1: Headphones
        $headphones = Product::firstOrCreate(
            ['slug' => 'nebula-soundpro-headphones'],
            [
                'name' => 'Nebula SoundPro Headphones',
                'description' => 'Experience audio excellence with Nebula SoundPro. Built with custom 40mm drivers, active noise cancellation (ANC), and premium high-fidelity purple neon accents, these headphones deliver luxury sound and look stunning.',
                'short_description' => 'High-end ANC headphones with premium purple neon glow.',
                'brand_id' => $nebula->id,
                'category_id' => $audio->id,
                'base_price' => 199.99,
                'sale_price' => 149.99,
                'sku' => 'NBL-SOUNDPRO-ANC',
                'stock_status' => 'in_stock',
                'stock_qty' => 50,
                'is_featured' => true,
                'is_active' => true,
                'image' => 'headphones_main.jpg',
                'images' => json_encode(['headphones_angle1.jpg', 'headphones_angle2.jpg']),
            ]
        );

        // Product 1 Variants
        ProductVariant::firstOrCreate(
            ['sku' => 'NBL-SP-PUR'],
            [
                'product_id' => $headphones->id,
                'attribute_name' => 'Color',
                'attribute_value' => 'Deep Purple',
                'price_modifier' => 0.00,
                'stock_qty' => 30,
            ]
        );

        ProductVariant::firstOrCreate(
            ['sku' => 'NBL-SP-VIO'],
            [
                'product_id' => $headphones->id,
                'attribute_name' => 'Color',
                'attribute_value' => 'Violet Neon',
                'price_modifier' => 10.00,
                'stock_qty' => 20,
            ]
        );

        // Product 2: Watch
        $watch = Product::firstOrCreate(
            ['slug' => 'aura-fit-smartwatch'],
            [
                'name' => 'Aura Fit Smartwatch',
                'description' => 'A sleek, premium fitness companion. The Aura Fit features a custom dynamic 1.4-inch AMOLED display, integrated health metrics (heart rate, blood oxygen, sleep tracker), and an elegant luxury violet silicone strap.',
                'short_description' => 'Premium AMOLED smartwatch with luxury violet styling.',
                'brand_id' => $auraTech->id,
                'category_id' => $wearables->id,
                'base_price' => 99.99,
                'sale_price' => null,
                'sku' => 'ART-FIT-WATCH',
                'stock_status' => 'in_stock',
                'stock_qty' => 100,
                'is_featured' => true,
                'is_active' => true,
                'image' => 'watch_main.jpg',
                'images' => json_encode(['watch_details1.jpg']),
            ]
        );

        // Product 3: Hoodie
        $hoodie = Product::firstOrCreate(
            ['slug' => 'luxe-violet-velvet-hoodie'],
            [
                'name' => 'Luxe Violet Velvet Hoodie',
                'description' => 'Drape yourself in luxury. The LuxeVibe hoodie is crafted with ultra-soft double-faced premium velvet, dyed in a gorgeous royal purple gradient that shifts in lights. Unisex loose-fit.',
                'short_description' => 'Luxurious oversized velvet hoodie in royal purple gradient.',
                'brand_id' => $luxeVibe->id,
                'category_id' => $apparel->id,
                'base_price' => 79.99,
                'sale_price' => 69.99,
                'sku' => 'LXV-VIOLET-HOODIE',
                'stock_status' => 'in_stock',
                'stock_qty' => 25,
                'is_featured' => true,
                'is_active' => true,
                'image' => 'hoodie_main.jpg',
                'images' => json_encode(['hoodie_back.jpg']),
            ]
        );

        // Product 3 Variants
        ProductVariant::firstOrCreate(
            ['sku' => 'LXV-VH-MED'],
            [
                'product_id' => $hoodie->id,
                'attribute_name' => 'Size',
                'attribute_value' => 'Medium',
                'price_modifier' => 0.00,
                'stock_qty' => 10,
            ]
        );

        ProductVariant::firstOrCreate(
            ['sku' => 'LXV-VH-LRG'],
            [
                'product_id' => $hoodie->id,
                'attribute_name' => 'Size',
                'attribute_value' => 'Large',
                'price_modifier' => 0.00,
                'stock_qty' => 10,
            ]
        );

        ProductVariant::firstOrCreate(
            ['sku' => 'LXV-VH-XL'],
            [
                'product_id' => $hoodie->id,
                'attribute_name' => 'Size',
                'attribute_value' => 'XL',
                'price_modifier' => 5.00,
                'stock_qty' => 5,
            ]
        );

        // Product 4: Lamp
        Product::firstOrCreate(
            ['slug' => 'amethyst-gemstone-lamp'],
            [
                'name' => 'Amethyst Gemstone Lamp',
                'description' => 'Brighten your sanctuary. Sourced from natural high-grade Brazilian Amethyst geodes, this raw crystal lamp projects a calming, ethereal purple glow, perfect for setting a premium bedroom mood.',
                'short_description' => 'Handcrafted Brazilian raw purple amethyst lamp.',
                'brand_id' => $nebula->id,
                'category_id' => $homeLiving->id,
                'base_price' => 49.99,
                'sale_price' => null,
                'sku' => 'NBL-AMETHYST-LMP',
                'stock_status' => 'in_stock',
                'stock_qty' => 10,
                'is_featured' => false,
                'is_active' => true,
                'image' => 'lamp_main.jpg',
                'images' => json_encode([]),
            ]
        );


        // --- 6. SEED COUPONS ---
        Coupon::firstOrCreate(
            ['code' => 'PURPLE20'],
            [
                'type' => 'percentage',
                'value' => 20.00,
                'max_discount' => 50.00,
                'min_order' => 50.00,
                'expires_at' => now()->addDays(30),
                'usage_limit' => 100,
                'used_count' => 0,
            ]
        );

        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'fixed',
                'value' => 10.00,
                'max_discount' => null,
                'min_order' => 30.00,
                'expires_at' => now()->addDays(90),
                'usage_limit' => 200,
                'used_count' => 0,
            ]
        );

        // --- 7. SEED DEFAULT SETTINGS ---
        \App\Models\Setting::set('site_name', 'NexCommerce');
        \App\Models\Setting::set('site_tagline', 'E-Commerce Console Grid');
        \App\Models\Setting::set('support_email', 'support@nexcommerce.com');
        \App\Models\Setting::set('contact_phone', '+1 (555) 019-9000');
        \App\Models\Setting::set('support_address', '742 Evergreen Terrace, Springfield');
        \App\Models\Setting::set('copyright_text', 'NexCommerce E-Commerce Headless Platform');
        \App\Models\Setting::set('meta_description', 'Premium headless retail admin powered by Laravel & purple gradients.');
    }
}
