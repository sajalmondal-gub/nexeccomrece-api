<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the banners API endpoint returns active banners in correct order.
     */
    public function test_banners_api_returns_active_banners(): void
    {
        // Create active and inactive banners
        Banner::create([
            'title' => 'Banner 2',
            'image' => 'banners/banner2.png',
            'link' => 'https://example.com/2',
            'order' => 2,
            'status' => true,
        ]);

        Banner::create([
            'title' => 'Banner 1',
            'image' => 'banners/banner1.png',
            'link' => 'https://example.com/1',
            'order' => 1,
            'status' => true,
        ]);

        Banner::create([
            'title' => 'Inactive Banner',
            'image' => 'banners/banner3.png',
            'link' => 'https://example.com/3',
            'order' => 3,
            'status' => false,
        ]);

        // Call API
        $response = $this->getJson('/api/banners');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'Banner 1') // Ordered by 'order' asc
            ->assertJsonPath('data.1.title', 'Banner 2');

        // Check absolute image URL format
        $data = $response->json('data');
        $this->assertStringContainsString('storage/banners/banner1.png', $data[0]['image_url']);
    }

    /**
     * Test the home screen aggregator endpoint returns correct data sections.
     */
    public function test_home_api_aggregates_banners_featured_categories_and_deals(): void
    {
        // 1. Create Banners
        Banner::create([
            'title' => 'Promo Banner',
            'image' => 'banners/promo.png',
            'link' => 'https://example.com',
            'order' => 1,
            'status' => true,
        ]);

        // 2. Create Featured and Regular Categories
        $featuredCategory = Category::create([
            'name' => 'Featured Cat',
            'slug' => 'featured-cat',
            'icon' => 'star',
            'is_active' => true,
            'is_featured' => true,
        ]);

        Category::create([
            'name' => 'Regular Cat',
            'slug' => 'regular-cat',
            'icon' => 'tag',
            'is_active' => true,
            'is_featured' => false,
        ]);

        // 3. Create Brand
        $brand = Brand::create([
            'name' => 'AuraTech',
            'slug' => 'auratech',
            'logo' => 'logo.png',
            'is_active' => true,
        ]);

        // 4. Create Products under different campaigns
        // Flash Deal Product
        Product::create([
            'name' => 'Flash Product',
            'slug' => 'flash-product',
            'description' => 'Fast deal',
            'short_description' => 'Fast',
            'base_price' => 100.00,
            'sale_price' => 80.00,
            'stock_qty' => 10,
            'stock_status' => 'in_stock',
            'sku' => 'FLASH-1',
            'category_id' => $featuredCategory->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_featured' => false,
            'deal_type' => 'flash',
            'image' => 'flash.png',
            'images' => json_encode(['flash.png']),
        ]);

        // Weekly Deal Product
        Product::create([
            'name' => 'Weekly Product',
            'slug' => 'weekly-product',
            'description' => 'Week deal',
            'short_description' => 'Week',
            'base_price' => 200.00,
            'sale_price' => 150.00,
            'stock_qty' => 5,
            'stock_status' => 'in_stock',
            'sku' => 'WEEKLY-1',
            'category_id' => $featuredCategory->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_featured' => false,
            'deal_type' => 'weekly',
            'image' => 'weekly.png',
            'images' => json_encode(['weekly.png']),
        ]);

        // Monthly Deal Product
        Product::create([
            'name' => 'Monthly Product',
            'slug' => 'monthly-product',
            'description' => 'Month deal',
            'short_description' => 'Month',
            'base_price' => 300.00,
            'sale_price' => 250.00,
            'stock_qty' => 20,
            'stock_status' => 'in_stock',
            'sku' => 'MONTHLY-1',
            'category_id' => $featuredCategory->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_featured' => false,
            'deal_type' => 'monthly',
            'image' => 'monthly.png',
            'images' => json_encode(['monthly.png']),
        ]);

        // Featured Product (standard carousel)
        Product::create([
            'name' => 'Featured Product',
            'slug' => 'featured-product',
            'description' => 'Premium item',
            'short_description' => 'Premium',
            'base_price' => 500.00,
            'stock_qty' => 15,
            'stock_status' => 'in_stock',
            'sku' => 'FEAT-1',
            'category_id' => $featuredCategory->id,
            'brand_id' => $brand->id,
            'is_active' => true,
            'is_featured' => true,
            'deal_type' => null,
            'image' => 'featured.png',
            'images' => json_encode(['featured.png']),
        ]);

        // Call the API
        $response = $this->getJson('/api/home');

        // Assert success and correct format
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'banners',
                    'featured_categories',
                    'flash_deals',
                    'weekly_deals',
                    'monthly_deals',
                    'featured_products',
                ]
            ]);

        // Verify counts and allocations
        $response->assertJsonCount(1, 'data.banners')
            ->assertJsonCount(1, 'data.featured_categories')
            ->assertJsonCount(1, 'data.flash_deals')
            ->assertJsonCount(1, 'data.weekly_deals')
            ->assertJsonCount(1, 'data.monthly_deals')
            ->assertJsonCount(1, 'data.featured_products');

        // Verify specific values
        $response->assertJsonPath('data.featured_categories.0.slug', 'featured-cat')
            ->assertJsonPath('data.flash_deals.0.slug', 'flash-product')
            ->assertJsonPath('data.weekly_deals.0.slug', 'weekly-product')
            ->assertJsonPath('data.monthly_deals.0.slug', 'monthly-product')
            ->assertJsonPath('data.featured_products.0.slug', 'featured-product');
    }
}
