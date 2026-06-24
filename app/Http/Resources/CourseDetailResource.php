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
            'titles' => $this->titles,
            'titles_ar' => $this->titles_ar,
            'available_watch_count' => $this->available_watch_count,
            'link_video' => $this->must_pass_to_unlock
                ? $this->checkStudentPassedExam()
                    ? $this->link_video
                    : 'You must pass the exam first'
                : $this->link_video,

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
            'must_solve_assignment_to_unlock' => (bool) $this->must_solve_assignment_to_unlock,
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

            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),

            'pdfUrl' => $this->getFirstMediaUrl('pdf'),
            'pdf' => new MediaResource($this->getFirstMedia('pdf')),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

