<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
            'teacher_id'         => 'nullable|exists:teachers,id',
            'stage_id'         => 'nullable|exists:stages,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'title'            => 'nullable|string|max:255',
            'title_ar'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'description_ar'   => 'nullable|string',
            'about'      => 'nullable|string',
            'about_ar'      => 'nullable|string',
            'hour_time_course'      => 'nullable|string',
            'type'             => 'nullable|in:online,center',
            'count_student'    => 'nullable|numeric|min:0',
            'price'            => 'nullable|string|max:255',
            'start_date'            => 'nullable',
            'end_date'         => 'nullable|',
        ];
    }
}


