<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetVerifyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['challenge_id' => ['required', 'string', 'size:64'], 'code' => ['required', 'string', 'digits:6']];
    }
}
