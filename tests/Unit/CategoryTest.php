<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    
    public $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to authenticate requests
        $this->user = User::factory()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_index()
    {
        // Arrange
        Category::factory()->count(2)->create();

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/categories');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'categories',
                         'totalPages',
                     ],
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_store()
    {
        // Arrange
        $data = [
            'name' => 'New Category',
            'status' => '1',
        ];

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/categories', $data);

        // Assert
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Category created successfully']);
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'status' => '1', // Check for the correct status value
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_show()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/categories/' . $category->id);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Category details']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_update()
    {
        // Arrange
        $category = Category::factory()->create();
        $data = [
            'name' => 'Updated Category',
            'status' => '2',
        ];

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->putJson('/api/categories/' . $category->id, $data);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Category updated successfully']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'status' => '2',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_destroy()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->deleteJson('/api/categories/' . $category->id);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Category deleted successfully']);
    }
}
