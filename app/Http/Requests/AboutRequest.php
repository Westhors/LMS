<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'name_ar' => 'required|string',
            'description_ar' => 'required|string',
            'google_meta' => 'nullable|string',
            'facebook_meta' => 'nullable|string',
            'tiktok_meta' => 'nullable|string',
            'you_tube_meta' => 'nullable|string',

            'facebook_count' => 'nullable|string',
            'google_count' => 'nullable|string',
            'tiktok_count' => 'nullable|string',
            'you_tube_count' => 'nullable|string',

            'teacher_id' => 'required|exists:teachers,id',
        ];
    }
}


