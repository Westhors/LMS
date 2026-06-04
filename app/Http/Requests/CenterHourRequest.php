<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CenterHourRequest extends FormRequest
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
            'title'         => 'nullable|string|max:255',
            'date'         => 'nullable|date',
            'hours_start'         => 'nullable|string|max:255',
            'hours_end'         => 'nullable|string|max:255',
            'address'         => 'nullable|string|max:255',
            'note'         => 'nullable|string',
            'teacher_id'       => 'nullable|exists:teachers,id',
        ];
    }
}


