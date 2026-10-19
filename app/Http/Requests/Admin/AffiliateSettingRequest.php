<?php

namespace App\Http\Requests\Admin;

use App\Enums\AmountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AffiliateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'auto_approve' => $this->boolean('auto_approve'),
        ]);
    }

    public function rules(): array
    {
        return [
            'is_active' => ['boolean'],
            'auto_approve' => ['boolean'],
            'commission_type' => ['required', Rule::enum(AmountType::class)],
            'commission_value' => ['required', 'numeric', 'min:0', 'max:999999'],
            'cookie_days' => ['required', 'integer', 'min:1', 'max:365'],
            'hold_days' => ['required', 'integer', 'min:0', 'max:365'],
            'min_payout_amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'payout_day' => ['required', 'integer', 'min:1', 'max:28'],
        ];
    }

    public function messages(): array
    {
        return [
            'payout_day.max' => 'Pick a day from 1 to 28, so it exists in every month.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->input('commission_type') === AmountType::Percent->value && (float) $this->input('commission_value') > 100) {
                $v->errors()->add('commission_value', 'A percent cannot be more than 100.');
            }
        });
    }
}
