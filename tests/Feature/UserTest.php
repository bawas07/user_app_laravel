<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_user_without_token(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_get_users(): void
    {
        User::factory()
            ->count(5)
            ->has(Order::factory()->count(5), 'orders')
            ->create();
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;
        $response = $this->getJson('/api/users', ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'page',
                'users' => [
                    '*' => [
                        'id',
                        'email',
                        'name',
                        'role',
                        'created_at',
                        'orders_count',
                        'can_edit',
                    ],
                ],
            ],
        ]);
    }

    public function test_create_users(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;
        $response = $this->postJson(
            '/api/users',
            [
                'email' => 'testing@email.com',
                'password' => 'password',
                'name' => 'test user'
            ],
            ['Authorization' => 'Bearer '.$token]
        );

        $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'email',
                'name',
                'created_at',
            ],
        ]);
    }
}
