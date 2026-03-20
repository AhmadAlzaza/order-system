<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register(): void
    {
        $user = User::factory()->make();
        $response = $this->postJson('/api/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => Str::random(12)
        ]);
        $response->assertJsonStructure(['token']);
        $response->assertStatus(201);
    }
    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => '123456789']);
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123456789'
        ]);
        $response->assertJsonStructure(['token']);
        $response->assertStatus(200);
    }
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $response = $this->postJson('api/logout', [], ['Authorization' => 'Bearer ' . $token]);
        $response->assertJsonStructure(['message']);
        $response->assertStatus(200);
    }
}
