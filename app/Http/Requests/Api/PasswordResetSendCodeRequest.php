<?php

namespace App\Http\Requests\Api;

use App\Enums\MessageChannel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasswordResetSendCodeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'string', 'size:64'],
            'channel' => ['required', Rule::enum(MessageChannel::class)],
        ];
    }
}
