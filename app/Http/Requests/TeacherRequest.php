<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->id ?? $this->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacherId,
            'sub_domain' => 'required|string|unique:teachers,sub_domain,' . $teacherId,
            'password' => 'nullable|string|min:6',
            'phone' => 'required|string|max:20',

            'stage' => 'required|array|min:1',
            'stage.*.stage_id' => 'required|exists:stages,id',
            'stage.*.image' => 'nullable|exists:media,id',

            'subject' => 'required|array|min:1',
            'subject.*.subject_id' => 'required|exists:subjects,id',
        ];
    }
}
