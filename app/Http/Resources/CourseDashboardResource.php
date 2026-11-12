<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseDashboardResource extends JsonResource
{
    public function toArray($request)
    {

        $isPurchased = false;
        $offerDiscount = $this->offer?->offer_discount ?? 0;
        $discountAmount = ($this->price * $offerDiscount) / 100;
        return [
            'id' => $this->id,
            'isPurchased' => $isPurchased,
            'teacher_id' => $this->teacher_id,
            'stage_id' => $this->stage_id,
            'subject_id' => $this->subject_id,
            'semester_id' => $this->semester_id,


            'original_price' => $this->price,
            'price' => $this->price - $discountAmount,
            'discount' => $discountAmount,
            'offer_id' => $this->offer_id,
            'offer_discount' => $offerDiscount,
            // بيانات العرض
            'offer_start_date' => $this->offer?->start_date,
            'offer_end_date' => $this->offer?->end_date,

           
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'about' => $this->about,
            'about_ar' => $this->about_ar,
            'hour_time_course' => $this->hour_time_course,
            'type' => $this->type,
            'count_student' => $this->count_student,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'active' => $this->active ?? null,
            'star' => $this->star ?? null,
            'link_video' => $this->link_video,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
           
            'time_duration' => $this->time_duration ?? null,
            'createdAt' => optional($this->created_at)->format('d F, Y'),
        ];
    }
}
