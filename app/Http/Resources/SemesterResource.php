<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'name_ar' => $this->name_ar ?? null,
            'active' => $this->active ?? null,
            'price' => $this->price ?? null,
            'discount' => $this->discount ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'subject_id' => $this->subject_id ?? null,

             // 🌐 علاقة المادة
            'subject' => new SubjectResource(
                $this->whenLoaded('subject')
            ),

            // 📚 Courses داخل الترم
            'courses' => CourseResource::collection(
                $this->whenLoaded('courses')
            ),

            'students' => StudentResource::collection(
                $this->whenLoaded('students')
            ),

            'createdAt' => $this->created_at?->format('d F, Y'),
        ];
    }
}

