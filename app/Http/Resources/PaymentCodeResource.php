<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentCodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code ?? null,
            'amount' => $this->amount ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'is_used' => $this->is_used ?? null,
            'student_id' => $this->student_id ?? null,
            'used_at' => $this->used_at ?? null,
            'expires_at' => $this->expires_at ?? null,
            'active' => $this->active ?? null,
            'type_code' => $this->type_code ?? null,
            'note' => $this->note ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

