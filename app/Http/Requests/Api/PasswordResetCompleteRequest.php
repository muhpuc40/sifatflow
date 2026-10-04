<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetCompleteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'string', 'size:64'],
            'reset_token' => ['required', 'string', 'size:64'],
            'password' => ['required', 'string', 'max:128', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }
}
