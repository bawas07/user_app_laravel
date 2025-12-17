<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_login_invalid_email(): void
    {
        $response = $this->postJson('/api/login', [
            "email" => "invalid@email.com",
            "password" => "password",
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid email or password']);
    }

    public function test_login_invalid_password(): void
    {
        $user = User::factory()->create([
            "email" => "data@email.com"
        ]);
        $response = $this->postJson('/api/login', [
            "email" => $user['email'],
            "password" => "passworda",
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Invalid email or password']);
    }

    public function test_login(): void
    {
        $user = User::factory()->create([
            "email" => "success@email.com"
        ]);
        $response = $this->postJson('/api/login', [
            "email" => $user['email'],
            "password" => "password",
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'message',
            'data' => [
                    'user' => ['id', 'email', 'role', 'active', 'created_at'],
                    'token'
                ]
            ]);
    }
}
