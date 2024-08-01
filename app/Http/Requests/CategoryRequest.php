<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Retrieve the category ID from the route
        $category = $this->route('category');
        $id = $category ? $category->id : null;

        $field = $id ? 'id' : 'name';
        return [
            'name'      => "required|string|max:255|unique:categories,$field,". $id,
            'status'    => 'required|in:1,2,3',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            errorResponse('Validation errors', $validator->errors(), 422)
        );
    }
}
