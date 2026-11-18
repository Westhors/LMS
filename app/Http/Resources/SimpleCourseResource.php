<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Enrollment;
class SimpleCourseResource extends JsonResource
{
    public function toArray($request)
    {
        $studentId = auth('sanctum')->id() ?? auth()->id();
        if ($studentId) {
            $isPurchased = Enrollment::where('student_id', $studentId)
                ->where(function ($query) {
                    // اشترى الكورس نفسه
                    $query->where(function ($q) {
                        $q->where('type', 'course')
                            ->where('course_id', $this->id);
                    })

                        // أو اشترى الـ Semester
                        ->orWhere(function ($q) {
                            $q->where('type', 'semester')
                                ->where('semester_id', $this->semester_id);
                        });
                })
                ->exists();
        }
        return [
            'id' => $this->id,

            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'students' => StudentResource::collection(
                $this->whenLoaded('students')
            ),

        ];
    }
}
