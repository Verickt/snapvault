<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:4096',
            'deleted_images' => 'sometimes|array',
            'deleted_images.*' => 'string',
        ];

        // Only require title and description when updating (not during initial image upload)
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['title'] = 'required|string|max:255';
            $rules['description'] = 'required|string';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
