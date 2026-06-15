<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    public function toArray($request)
    {
            $offerPercent = $this->offer?->offer_discount ?? 0;

            $discountAmount = ($this->price * $offerPercent) / 100;
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'name_ar' => $this->name_ar ?? null,
            'active' => $this->active ?? null,

            'original_price' => $this->price,
            'price' => $this->price - $discountAmount,
            'discount' => $discountAmount,
            'offer_discount' => $offerPercent,
            
            'teacher_id' => $this->teacher_id ?? null,
            'subject_id' => $this->subject_id ?? null,
            'offer_id'=> $this->offer_id ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
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

