<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_index(): void
    {
        Product::factory()->create();
        $response = $this->getJson('/api/products');
        $response->assertJsonStructure(['data' => [['id', 'name', 'description', 'price', 'stock']]]);
        $response->assertStatus(200);
    }
    public function test_user_can_create_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/products', [
            'name' => Str::random(12),
            'description' => Str::random(50),
            'price' => rand(1, 1000),
            'stock' => rand(1, 20)
        ]);
        $response->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'stock']]);
        $response->assertStatus(201);
    }
    public function test_user_can_show_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/products/' . $product->id);
        $response->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'stock']]);
        $response->assertStatus(200);
    }
    public function test_user_can_update_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/products/' . $product->id, [
            'name' => Str::random(12),
            'description' => Str::random(50),
            'price' => rand(1, 1000),
            'stock' => rand(1, 20)
        ]);
        $response->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'stock']]);
        $response->assertStatus(200);
    }
    public function test_user_can_delete_product(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/products/' . $product->id);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
}
