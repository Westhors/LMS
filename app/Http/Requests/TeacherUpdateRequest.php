<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->id ?? $this->id;

        return [
            'name' => 'nullable|string|max:255',

            'email' => 'nullable|email|unique:teachers,email,' . $teacherId,

            'sub_domain' => 'nullable|string|unique:teachers,sub_domain,' . $teacherId,

            'password' => 'nullable|string|min:6',

            'phone' => 'nullable|string|max:20',

            'stage' => 'nullable|array',
            'stage.*.stage_id' => 'required_with:stage|exists:stages,id',
            'stage.*.image' => 'nullable|exists:media,id',

            'subject' => 'nullable|array',
            'subject.*.subject_id' => 'required_with:subject|exists:subjects,id',
        ];
    }
}
