<?php

namespace App\Http\Requests\Admin;

use App\Enums\AmountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PaymentVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'title' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'alpha_dash:ascii', 'max:40', Rule::unique('payment_vouchers', 'code')],
            'amount_type' => ['required', Rule::enum(AmountType::class)],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'expires_at' => ['required', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string', 'max:1000'],
            'notify' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'This code is already used by another voucher.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($this->input('amount_type') === AmountType::Percent->value && (float) $this->input('amount') > 100) {
                $v->errors()->add('amount', 'A percent cannot be more than 100.');
            }
        });
    }
}
