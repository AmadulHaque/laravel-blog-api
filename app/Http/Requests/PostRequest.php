<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $this->route('post'),
            'status' => 'required|integer|in:1,2,3',
            'thumbnail' => 'nullable|image|max:2048',
        ];
    }
}
