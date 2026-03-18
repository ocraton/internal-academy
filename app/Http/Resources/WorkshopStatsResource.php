<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopStatsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'starts_at' => $this->starts_at->toIso8601String(),
            'capacity' => $this->capacity,
            'enrolled_count' => $this->enrolled_count,
            'waitlisted_count' => $this->waitlisted_count,
            'fill_percentage' => $this->capacity > 0
                ? round(($this->enrolled_count / $this->capacity) * 100)
                : 0,
        ];
    }
}
