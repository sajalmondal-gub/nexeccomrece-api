<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ComboOffer;
use App\Models\User;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComboOfferTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $brand;
    private $category;
    private $product1;
    private $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->brand = Brand::create([
            'name' => 'Brand Test',
            'slug' => 'brand-test',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Category Test',
            'slug' => 'category-test',
            'is_active' => true,
        ]);

        $this->product1 = Product::create([
            'name' => 'Gaming Mouse',
            'slug' => 'gaming-mouse',
            'description' => 'Fast mouse',
            'base_price' => 50.00,
            'stock_qty' => 10,
            'sku' => 'MOUSE-1',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'name' => 'Gaming Keyboard',
            'slug' => 'gaming-keyboard',
            'description' => 'Mechanical keyboard',
            'base_price' => 100.00,
            'stock_qty' => 5,
            'sku' => 'KEYBOARD-1',
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test active combo offers are listed correctly via the API.
     */
    public function test_can_fetch_active_combo_offers_via_api(): void
    {
        $combo = ComboOffer::create([
            'name' => 'Gamer Duo pack',
            'slug' => 'gamer-duo-pack',
            'description' => 'Get mouse & keyboard bundle',
            'price' => 120.00, // Original combined is 150, so save 30
            'image' => 'uploads/combos/gamer.png',
            'is_active' => true,
        ]);

        $combo->products()->attach($this->product1->id, ['quantity' => 1]);
        $combo->products()->attach($this->product2->id, ['quantity' => 1]);

        $response = $this->getJson('/api/combo-offers');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Gamer Duo pack')
            ->assertJsonPath('data.0.savings_amount', 30)
            ->assertJsonCount(2, 'data.0.products');
    }

    /**
     * Test the home screen aggregator includes active combo offers.
     */
    public function test_home_aggregator_includes_active_combo_offers(): void
    {
        $combo = ComboOffer::create([
            'name' => 'Gamer Duo pack',
            'slug' => 'gamer-duo-pack',
            'price' => 120.00,
            'is_active' => true,
        ]);

        $combo->products()->attach($this->product1->id, ['quantity' => 1]);

        $response = $this->getJson('/api/home');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'combo_offers'
                ]
            ])
            ->assertJsonCount(1, 'data.combo_offers');
    }

    /**
     * Test stock validations when adding a combo offer to the cart.
     */
    public function test_adding_combo_offer_to_cart_performs_stock_checks(): void
    {
        $combo = ComboOffer::create([
            'name' => 'Gamer Duo pack',
            'slug' => 'gamer-duo-pack',
            'price' => 120.00,
            'is_active' => true,
        ]);

        $combo->products()->attach($this->product1->id, ['quantity' => 1]);
        $combo->products()->attach($this->product2->id, ['quantity' => 1]);

        // 1. Adding with sufficient stock (Mouse has 10, Keyboard has 5, adding qty 2)
        $response = $this->actingAs($this->user)
            ->postJson('/api/cart', [
                'combo_offer_id' => $combo->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success');

        // Check it exists in cart
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'combo_offer_id' => $combo->id,
            'quantity' => 2,
        ]);

        // 2. Trying to add more than keyboard stock (Keyboard stock is 5, we already added 2, now adding 4 more -> total 6, which exceeds 5)
        $response = $this->actingAs($this->user)
            ->postJson('/api/cart', [
                'combo_offer_id' => $combo->id,
                'quantity' => 4,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', "Insufficient stock. Only 5 left for product 'Gaming Keyboard' which is part of this combo bundle.");
    }
}
