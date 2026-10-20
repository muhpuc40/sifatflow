<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ComposeSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $table = match ($this->input('recipient_type')) {
            'instructor' => 'instructors',
            'affiliate' => 'affiliates',
            default => 'students',
        };

        return [
            'recipient_type' => ['required', Rule::in(['student', 'instructor', 'affiliate'])],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => [
                'integer',
                'distinct',
                Rule::exists($table, 'id')->where(fn($query) => $query
                    ->where('status', 'active')
                    ->whereNull('deleted_at')
                    ->whereNotNull('phone')),
            ],
            'custom_numbers' => ['nullable', 'string', 'max:10000'],
            'message' => ['required', 'string', 'max:1530'],   // 10 SMS parts at 153 chars each
        ];
    }

    public function messages(): array
    {
        return [
            'message.max' => 'The message is too long (max 10 SMS parts).',
        ];
    }

    /** Phone numbers typed directly, normalized to 8801XXXXXXXXX. */
    public function customNumbers(): array
    {
        return collect(preg_split('/[,;\r\n]+/', (string) $this->input('custom_numbers'), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn($n) => preg_replace('/\D+/', '', $n))
            ->map(fn($n) => str_starts_with($n, '01') ? '88' . $n : $n)
            ->filter(fn($n) => preg_match('/^8801[3-9]\d{8}$/', $n))
            ->unique()
            ->values()
            ->all();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $typed = collect(preg_split('/[,;\r\n]+/', (string) $this->input('custom_numbers'), -1, PREG_SPLIT_NO_EMPTY))->filter();
            $valid = count($this->customNumbers());

            if ($typed->count() > $valid) {
                $validator->errors()->add('custom_numbers', 'One or more numbers are not valid Bangladeshi mobile numbers.');
            }

            $userIds = $this->input('user_ids');

            if ((!is_array($userIds) || count($userIds) === 0) && $valid === 0) {
                $validator->errors()->add('user_ids', 'Select at least one recipient or enter a custom number.');
            }
        });
    }
}