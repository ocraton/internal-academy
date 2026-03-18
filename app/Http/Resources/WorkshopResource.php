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
        $waitlistCount = $this->waitlistedParticipants()->count();
        $availableSpots = $this->capacity - $enrolledCount;
        $isFull = $availableSpots <= 0;

        $currentUserEnrollmentStatus = null;
        $currentUserWaitlistPosition = null;

        if (auth()->check()) {
            $currentUserEnrollment = $this->enrollments()
                ->where('user_id', auth()->id())
                ->first();

            if ($currentUserEnrollment !== null) {
                $currentUserEnrollmentStatus = $currentUserEnrollment->status->value;
                if ($currentUserEnrollment->status === EnrollmentStatus::Waitlisted) {
                    $currentUserWaitlistPosition = $currentUserEnrollment->position;
                }
            }
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
            'waitlist_count' => $waitlistCount,
            'available_spots' => $availableSpots,
            'is_full' => $isFull,
            'current_user_enrollment_status' => $currentUserEnrollmentStatus,
            'current_user_waitlist_position' => $currentUserWaitlistPosition,
        ];
    }
}
