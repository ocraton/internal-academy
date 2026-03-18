<?php

namespace App\Http\Resources;

use App\Enums\EnrollmentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $enrolledCount = $this->enrolledParticipants()->count();
        $availableSpots = $this->capacity - $enrolledCount;
        $isFull = $availableSpots <= 0;

        $isEnrolledByCurrentUser = false;
        if (auth()->check()) {
            $isEnrolledByCurrentUser = $this->enrollments()
                ->where('user_id', auth()->id())
                ->where('status', EnrollmentStatus::Enrolled)
                ->exists();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'capacity' => $this->capacity,
            'created_at' => $this->created_at->toIso8601String(),
            'enrolled_count' => $enrolledCount,
            'available_spots' => $availableSpots,
            'is_full' => $isFull,
            'is_enrolled_by_current_user' => $isEnrolledByCurrentUser,
        ];
    }
}
