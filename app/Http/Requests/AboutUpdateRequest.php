<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutUpdateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'name_ar' => 'nullable|string',
            'google_meta' => 'nullable|string',
            'facebook_meta' => 'nullable|string',
            'tiktok_meta' => 'nullable|string',
            'you_tube_meta' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'teacher_id' => 'nullable|exists:teachers,id',
        ];
    }
}
