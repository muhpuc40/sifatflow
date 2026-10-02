<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],      // email or phone
            'password' => ['required', 'string'],
            'device_id' => ['required', 'string', 'max:100'],  // created by the frontend, stored in the browser
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:50'],
            'browser' => ['nullable', 'string', 'max:50'],
            'revoke_device_id' => ['nullable', 'integer'],     // replace an old device when the limit is reached
        ];
    }
}
