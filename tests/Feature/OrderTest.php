<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_order_out_of_stock_product()
    {
        // 1. Create a user (User has factory by default, so this is fine)
        $user = User::factory()->create();

        // 2. Create a product DIRECTLY (No factory needed)
        $product = Product::create([
            'name' => 'Out of Stock Pro',
            'description' => 'Test product',
            'price' => 500,
            'stock' => 0,
        ]);

        // 3. Act as user and place the order
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        // 4. Assert that the API blocked it (Status 400)
        $response->assertStatus(400);
        
        // 5. Assert the error message we wrote in the OrderController
        $response->assertJsonFragment([
            'message' => "Stock unavailable for: Out of Stock Pro"
        ]);
    }
}