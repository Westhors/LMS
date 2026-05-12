<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemesterRequest extends FormRequest
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
            'name'         => 'nullable|string|max:255',
            'name_ar'         => 'nullable|string|max:255',
            'discount'         => 'nullable|numeric|min:0|max:100',
            'price'         => 'nullable|numeric|min:0',
            'teacher_id'         => 'nullable|exists:teachers,id',
            'subject_id'         => 'required|exists:subjects,id',
        ];
    }
}

