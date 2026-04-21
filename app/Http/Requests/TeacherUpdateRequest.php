<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherUpdateRequest extends FormRequest
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
            'email' => 'nullable|email',
            'sub_domain' => 'nullable|string',
            'password'    => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'stage_id' => 'nullable|exists:stages,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ];
    }
}


