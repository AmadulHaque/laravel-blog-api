<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;




    /** @test */
    public function it_registers_a_user()
    {
        // Arrange: Prepare the request data
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Act: Perform the registration
        $response = $this->postJson('/oauth/register', $userData);

        // Assert: Check the response and database
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function it_logs_in_a_user()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Act: Perform the login
        $response = $this->postJson('/oauth/login', $loginData);

        // Assert: Check the response
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function it_fails_to_log_in_with_invalid_credentials()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        // Act: Attempt to login with invalid credentials
        $response = $this->postJson('/oauth/login', $loginData);

        // Assert: Check the response
        $response->assertStatus(401);
        $this->assertArrayHasKey('error', $response->json());
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        // Arrange: Create a user and log in to get a token
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        // Act: Perform the logout
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->postJson('/oauth/logout');

        // Assert: Check the response
        $response->assertStatus(200);
        $this->assertEquals('Logged out successfully', $response->json()['message']);
    }

    /** @test */
    public function it_sends_a_password_reset_link()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $passwordResetData = [
            'email' => 'test@example.com',
        ];

        // Act: Request a password reset link
        $response = $this->postJson('/oauth/forgot-password', $passwordResetData);

        // Assert: Check the response
        $response->assertStatus(200);
        $this->assertEquals('Password reset link sent', $response->json()['message']);
    }



}
