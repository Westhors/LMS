<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FooterUpdateRequest extends FormRequest
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
            'name_ar' => 'nullable|string|max:255',
            'facebook_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'tiktok_link' => 'nullable|url',
            'whatsapp_link' => 'nullable|url',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'active' => 'boolean',
            'teacher_id' => 'nullable|exists:teachers,id',
        ];
    }
}
