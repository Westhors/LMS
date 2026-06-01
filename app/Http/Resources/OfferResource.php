<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'description' => $this->description ?? null,
            'offer_price' => $this->offer_price ?? null,
            'start_date' => $this->start_date ?? null,
            'end_date' => $this->end_date ?? null,
            'active' => $this->active ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
