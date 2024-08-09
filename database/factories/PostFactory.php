<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title'             => $this->faker->sentence,
            'short_description' => $this->faker->paragraph,
            'content'           => $this->faker->text,
            'allow_comments'    => 1,
            'is_featured'       => 1,
            'status'            => 1,
        ];
    }
}
