<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'vault_id' => ['required', 'exists:vaults'],
            'user_id' => ['required', 'exists:users'],
            'token' => ['required'],
            'is_public' => ['boolean'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
