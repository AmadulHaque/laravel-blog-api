<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $category;
    public $categories;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to authenticate requests
        $this->user         = User::factory()->create();
        $this->category     = Category::factory()->create();
    }



    #[\PHPUnit\Framework\Attributes\Test]
    public function test_list_all_posts()
    {
        // Arrange
        Post::factory()->count(10)->create(['user_id' => $this->user->id]);
    
        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/posts');
    
        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'posts',
                         'totalPages',
                     ],
                 ])
                 ->assertJsonCount(10, 'data.posts');
    }
    


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_store()
    {
        // Arrange
        $data = [
            "title"             =>  "title",
            "short_description" =>  "test-desc",
            "content"           =>  "test-content",
            "tags"              =>  "test-1",
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


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_update()
    {
        // Arrange
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $data = [
            "title"             =>  "updated title",
            "short_description" =>  "updated-desc",
            "content"           =>  "updated-content",
            "tags"              =>  "updated-1",
            "allow_comments"    =>  1,
            "category_id"       =>  [$this->category->id],
            "is_featured"       =>  1,
            "status"            =>  1,
        ];

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/posts/{$post->id}", $data);

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
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


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_show()
    {
        // Arrange
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/posts/{$post->id}");

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
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


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_destroy()
    {
        // Arrange
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Act
        $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/posts/{$post->id}");

        // Assert
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Post deleted successfully',
                ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_posts_by_title()
    {
        // Arrange
        Post::factory()->create(['title' => 'Unique Post Title','user_id' => $this->user->id]);
    
        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/posts?search=Unique');
    
        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'posts',
                         'totalPages',
                     ],
                 ])
                 ->assertJsonCount(1, 'data.posts')
                 ->assertJsonFragment(['title' => 'Unique Post Title']);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function test_filter_posts_by_status()
    {
        // Arrange
        Post::factory()->create(['status' => 1,'user_id' => $this->user->id]);
        Post::factory()->count(9)->create(['status' => 2,'user_id' => $this->user->id]);
    
        // Act
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/posts?status=2');
    
        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'posts',
                         'totalPages',
                     ],
                 ])
                 ->assertJsonCount(9, 'data.posts');
    }



}
