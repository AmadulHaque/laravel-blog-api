<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'user_id',
                             'name',
                             'slug',
                             'status',
                             'image',
                             'created_at',
                             'updated_at',
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        // $user = User::factory()->create();
        // $this->actingAs($user);

        $data = [
            'name' => 'New Category',
            'slug' => 'new-category',
            'status' => 1,
            'image' => null,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category created successfully',
                     'data' => [
                         'name' => 'New Category'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    /** @test */
    public function it_can_show_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category details',
                     'data' => [
                         'id' => $category->id,
                         'name' => $category->name,
                         'slug' => $category->slug,
                         'status' => $category->status,
                         'image' => $category->image,
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'status' => 2,
            'image' => null, // Adjust this if you want to test image uploads
        ];

        $response = $this->putJson('/api/categories/' . $category->id, $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category updated successfully',
                     'data' => [
                         'name' => 'Updated Category'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category deleted successfully',
                 ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
