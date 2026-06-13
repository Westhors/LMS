<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
class CenterHourResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'date' => $this->date ?? null,
            'hours_start' => $this->hours_start ?? null,
            'hours_end' => $this->hours_end ?? null,
            'address' => $this->address ?? null,
            'phone' => $this->phone ?? null,
            'note' => $this->note ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'subject_id' => $this->subject_id ?? null,
            'stage_id' => $this->stage_id ?? null,
            'subject' => DB::table('subjects')
                ->where('id', $this->subject_id)
                ->value('name'),

            'stage' => DB::table('stages')
                ->where('id', $this->stage_id)
                ->value('name'),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}



