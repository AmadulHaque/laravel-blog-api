<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'                 => 'required|string|max:255|unique:posts,title,' . $this->route('post'),
            'slug'                  => 'nullable|string|max:255|unique:posts,slug,'  . $this->route('post'),
            'short_description'     => 'nullable|string|max:400',
            'content'               => 'nullable|string|max:5000',
            'tags'                  => 'nullable|array',
            'category_id.*'         => 'required|integer|exists:categories,id',
            'allow_comments'        => 'required|integer|in:1,0',
            'is_featured'           => 'required|integer|in:1,0',
            'status'                => 'required|integer|in:1,2,3',
            'thumbnail'             => 'nullable|image|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            errorResponse('Validation errors', $validator->errors(), 422)
        );
    }

    public function messages()
    {
        return [
            'category_id.*.required' => 'Each category ID is required.',
            'category_id.*.integer' => 'Each category ID must be an integer.',
            'category_id.*.exists' => 'The selected category ID is invalid.',
        ];
    }

}
