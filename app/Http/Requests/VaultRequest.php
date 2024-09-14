<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VaultRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'user_id' => ['required', 'exists:users'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
