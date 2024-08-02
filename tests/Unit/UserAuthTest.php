<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_register()
    {
        // Arrange
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'address' => '123 Test St',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/auth/register', $data);

        // Assert
        $response->assertStatus(201)
                 ->assertJson(['message' => 'User registration in successfully']);

        $this->assertDatabaseHas('users', [
            'name'      => 'Test User',
            'email'     => 'testuser@example.com',
            'address'   => '123 Test St',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_login()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123')
        ]);

        $data = [
            'email' => 'testuser@example.com',
            'password' => 'password123'
        ];

        // Act
        $response = $this->postJson('/api/auth/login', $data);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['message' => 'User login in successfully'])
                 ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email', 'token']]]);

        $this->assertAuthenticatedAs($user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_logout()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $this->actingAs($user, 'sanctum');

        // Assert
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logged out successfully']);
    }
}
