<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code'         => 'nullable|unique:payment_codes,code',
            'amount'         => 'nullable|numeric|min:0',
            'teacher_id'       => 'nullable|exists:teachers,id',
            'is_used'      => 'nullable|boolean',
            'student_id'            => 'nullable|exists:students,id',
            'used_at'         => 'nullable|string|max:255',
            'expires_at'      => 'nullable|date',
            'active'            => 'nullable|string|max:255',
            'note'   => 'nullable|string',
        ];
    }
}


