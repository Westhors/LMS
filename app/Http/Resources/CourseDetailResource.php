<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'content_link' => $this->must_pass_to_unlock
                ? $this->checkStudentPassedExam()
                    ? $this->content_link
                    : 'You must pass the exam first'
                : $this->content_link,
            'lession_date' => $this->lession_date,
            'lession_time' => $this->lession_time,
            'price' => $this->price,
            'must_pass_to_unlock' => (bool) $this->must_pass_to_unlock,
            'exams' => ExamResource::collection(
                $this->whenLoaded('exams')
            ),
            'discount' => $this->discount ?? null,

            'assignments' => ExamResource::collection(
                $this->whenLoaded('assignments')
            ),

            'students' => StudentResource::collection(
                $this->whenLoaded('students')
            ),

         'attended' => (bool) (
                $this->attendances
                    ->where('student_id', auth()->id())
                    ->first()?->attended
            ),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
