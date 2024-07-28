<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_user()
    {
        // Arrange: Prepare the data needed for the test
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '01712345678',
            'address' => 'dhaka',
            'password' => bcrypt('password'),
        ];

        // Act: Perform the action
        $user = User::create($userData);

        // Assert: Verify the outcome
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    /** @test */
    public function it_fails_to_create_user_with_existing_email()
    {
        // Arrange: Prepare the data and create a user
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '01712345678',
            'address' => 'dhaka',
            'password' => bcrypt('password'),
        ];

        User::create($userData);

        // Act: Try to create another user with the same email
        try {
            User::create($userData);
        } catch (\Exception $e) {
            $exception = $e;
        }

        // Assert: Verify that the exception was thrown
        $this->assertNotNull($exception);
        $this->assertStringContainsString('SQLSTATE[23000]: Integrity constraint violation', $exception->getMessage());

    }

}
