<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function getFilteredPosts(array $filters): LengthAwarePaginator
    {
        $query = Post::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function createPost(array $data): Post
    {
        return DB::transaction(function() use ($data) {
            return Post::create($data);
        });
    }

    public function getPostById(string $id): Post
    {
        return Post::findOrFail($id);
    }

    public function updatePost(string $id, array $data): Post
    {
        $post = Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function deletePost(string $id): void
    {
        $post = Post::findOrFail($id);
        $post->delete();
    }
}
