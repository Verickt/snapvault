<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => ['nullable'],
            'description' => ['nullable'],
            'image_path' => ['nullable'],
            'vault_id' => ['nullable', 'exists:vaults'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
