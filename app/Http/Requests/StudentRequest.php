<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'phone' => 'required|unique:students,phone',
            'password' => 'required|min:6',
            'code_parent' => 'nullable|string',
            'region' => 'nullable|string',
            'phone_parent' => 'nullable|unique:students,phone_parent',
            'type_of_attendance' => 'nullable|in:center,online',
            'gender' => 'nullable|in:male,female',
            'type_of_study' => 'nullable|in:general,azhar',
            'governorate' => 'nullable|string',
            'school_name' => 'nullable|string',

            'teacher_id' => 'nullable|exists:teachers,id',
            'stage_id' => 'nullable|exists:stages,id',
            'center_hour_id' => 'nullable|exists:center_hours,id',
        ];
    }
}


