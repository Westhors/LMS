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
            'content_link' => 'nullable|url',



            'titles' => 'nullable|array',
            'titles.*' => 'nullable|string|max:255',

            'titles_ar' => 'nullable|array',
            'titles_ar.*' => 'nullable|string|max:255',

            'link_video' => 'nullable|array',
            'link_video.*' => 'nullable|url',



            'lession_date' => 'nullable|date',
            'lession_time' => 'nullable',
            'price'         => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
        ];
    }

}
