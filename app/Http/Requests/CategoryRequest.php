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
        $id = $this->route('category')->id ?? "NULL";

        return [
            'name'      => 'required|string|max:255|unique:categories,id,'. $id,
            'status'    => 'required|in:1,2,3',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            successResponse('Validation errors', $validator->errors(), 422)
        );
    }
}
