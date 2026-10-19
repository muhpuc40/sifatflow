<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->email)),
            'phone' => preg_replace('/\s+/', '', (string) $this->phone),
        ]);
    }

    public function rules(): array
    {
        $table = $this->table();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique($table, 'email')],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/', Rule::unique($table, 'phone')],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'device_id' => ['required', 'string', 'max:100'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:50'],
            'browser' => ['nullable', 'string', 'max:50'],
        ];
    }

    /** register route is v1/{type}/register, type = student | affiliate */
    private function table(): string
    {
        return $this->route('type') === 'affiliate' ? 'affiliates' : 'students';
    }
}