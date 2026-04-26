<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'    => 'required|exists:courses,id',
            'description'  => 'nullable|string',
            'description_ar' => 'nullable|string',
            'title'        => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'content_link' => 'nullable|url',
            'lession_date' => 'nullable|date',
            'lession_time' => 'nullable|date_format:H:i',
        ];
    }

}
