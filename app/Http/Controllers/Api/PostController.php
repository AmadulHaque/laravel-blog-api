<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use Illuminate\Http\JsonResponse;
use App\Services\PostService;
use Illuminate\Http\Request;


class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :JsonResponse
    {
        $posts = $this->postService->getFilteredPosts($request->query());
        return successResponse('All Post', $posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request) :JsonResponse
    {
        $post = $this->postService->createPost($request->validated());
        return successResponse('Post created successfully', $post,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = $this->postService->getPostById($id);
        return successResponse('Post Details', $post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id) :JsonResponse
    {
        $post = $this->postService->updatePost($id, $request->validated());
        return successResponse('Post updated successfully', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->postService->deletePost($id);
        return successResponse('Category deleted successfully');
    }
}
