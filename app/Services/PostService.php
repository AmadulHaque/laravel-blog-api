<?php

namespace App\Services;

use App\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function getFilteredPosts(array $filters): LengthAwarePaginator
    {
        /**
         * Both approaches have their advantages, but the second approach using the when method is generally more concise and flexible. Here’s a comparison to help you decide:
         * First Approach (Commented Out Code)
         *  Pros :
         *      Explicit Logic: It’s straightforward and clear, which makes it easy to understand for developers of all experience levels
         *      Easy to Debug: Because each condition is explicitly defined, it can be easier to debug and modify.
         *  Cons:
         *      Verbosity: The code can become verbose, especially if you have multiple conditions.
         *      Repetition: You might repeat similar logic for each condition, which can lead to more lines of code
         * 
         * Second Approach (Using when)
         *  Pros:
         *      Conciseness: The code is more compact and elegant. This approach avoids repetition and reduces the overall length of the code
         *      Flexibility: The when method allows chaining multiple conditions in a clean way. It’s also easier to add or remove conditions as needed.
         *  Cons :
         *        Complexity: While more concise, it may be less immediately understandable to developers who are less familiar with the when method.
         *        Function Arguments: You need to be careful with the arguments passed to the closure, ensuring they are correctly handled.    
         *
         * Comparison:
         *    Readability: The first approach is more explicit and might be easier to understand for someone new to the codebase, while the second approach is cleaner and more succinct.
         *    Maintainability: The second approach is generally easier to maintain and extend because you can quickly add or remove conditions without modifying much code.
         *    Performance: Both approaches are very similar in terms of performance; however, the second approach might have a slight edge due to less conditional checking if you have many filters .
         * 
        */

        
        

        $query = Post::query()->with(['user:id,name']);
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('slug', 'like', '%' . $filters['search'] . '%');
            });
        }
        return $query->paginate($filters['per_page'] ?? 15);

        #or

        // $post = Post::query()
        // ->when($filters['status'], function ($query) use ($filters) {
        //     return $query->where('status', $filters['status']);
        // })
        // ->when($filters['search'], function ($query) use ($filters) {
        //     return $query->whereAny(['title','slug'], 'like', '%'. $filters['search']. '%');
        // });
    
        // return $post->paginate($filters['per_page'] ?? 15);
    

  
    }

    public function createPost(array $data): Post
    {
        return DB::transaction(function() use ($data) {

            // category
            $postCategory = $data['category_id'];
            unset($data['category_id']);

            $data['user_id'] = Auth::user()->id;

            // create post
            $post = Post::create($data);

            // create  post category
            foreach ($postCategory as $caegoryid) {
                CategoryPost::create([
                    'category_id' => $caegoryid,
                    'post_id'     =>  $post->id
                ]);
            }

            return $post;

        });
    }

    public function getPostById(string $id): Post
    {
        return Post::with('categories')->findOrFail($id);
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
