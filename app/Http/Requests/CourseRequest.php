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
            'semester_id'      => 'nullable|exists:semesters,id',
            'offer_id'         => 'nullable|exists:offers,id',
            'title'            => 'nullable|string|max:255',
            'title_ar'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'price'         => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'description_ar'   => 'nullable|string',
            'link_video'      => 'nullable|url',
            'about'      => 'nullable|string',
            'about_ar'      => 'nullable|string',
            'hour_time_course'      => 'nullable|string',
            'type'             => 'nullable|in:online,center',
            'count_student'    => 'nullable|numeric|min:0',
            'start_date'            => 'nullable|date',
            'end_date'         => 'nullable|date',
            'time_duration'         => 'nullable|date_format:H:i:s',
        ];
    }
}


