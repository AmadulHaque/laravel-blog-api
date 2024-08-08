<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostStoreTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to authenticate requests
        $this->user     = User::factory()->create();
        $this->category = Category::factory()->create();
    }




    #[\PHPUnit\Framework\Attributes\Test]
    public function test_store()
    {
        // Arrange
        $data = [
            "title"             =>  "title",
            "short_description" =>  "test-desc",
            "content"           =>  "test-content",
            "tags"              =>  ["test-1", 'test-2'],
            "allow_comments"    =>  1,
            "category_id"       =>  [$this->category->id],
            "is_featured"       =>  1,
            "status"            =>  1,
        ];

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/posts', $data);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'short_description',
                'content',
                'tags',
                'allow_comments',
                'is_featured',
                'status',
                'thumbnail',
            ],
        ]);
    }


}
