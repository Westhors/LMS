<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $this->id,
            'sub_domain' => 'required|string|unique:teachers,sub_domain,' . $this->id,
            'password'    => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'stage_id' => 'required|exists:stages,id',
            'subject_id' => 'required|exists:subjects,id',
        ];
    }
}


