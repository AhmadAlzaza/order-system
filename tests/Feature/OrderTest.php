<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;


class OrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/orders');
        $response->assertStatus(200);
    }
    public function test_user_can_create_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson(
            '/api/orders',
            [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => rand(1, 4)
                    ]
                ]

            ]
        );
        $response->assertJsonStructure([
            'data' => ['user_name', 'status', 'total_price']
        ]);
        $response->assertStatus(201);
    }
    public function test_user_can_update_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->putJson('api/orders/' . $order->id, [
            'status' => 'processing'
        ]);
        $response->assertJsonStructure([
            'data' => ['id', 'user_name', 'status', 'total_price']
        ]);
        $response->assertStatus(200);
    }
    public function test_user_can_cancel_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders/' . $order->id . '/cancel');
        $response->assertJsonStructure([
            'data' => ['id', 'user_name', 'status', 'total_price']
        ]);
        $response->assertStatus(200);
    }
    public function test_user_can_delete_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/orders/' . $order->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
}
