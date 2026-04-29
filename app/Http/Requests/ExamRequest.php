<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:assignment,exam',
            'course_detail_id' => 'nullable|exists:course_details,id',
            'stage_id' => 'nullable|exists:stages,id',
            'teacher_id' => 'nullable|exists:teachers,id',

            'total_marks' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',

            'active' => 'nullable|boolean',
        ];
    }
}
