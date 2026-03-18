<?php

namespace App\Repositories;

use App\Contracts\Repositories\EnrollmentRepositoryInterface;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function findByUserAndWorkshop(int $userId, int $workshopId): ?Enrollment
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->where('workshop_id', $workshopId)
            ->first();
    }

    public function getEnrolledCount(int $workshopId): int
    {
        return Enrollment::query()
            ->where('workshop_id', $workshopId)
            ->where('status', EnrollmentStatus::Enrolled)
            ->count();
    }

    public function createEnrollment(array $data): Enrollment
    {
        return Enrollment::query()->create($data);
    }

    public function deleteEnrollment(Enrollment $enrollment): bool
    {
        return $enrollment->delete();
    }

    public function getFirstWaitlisted(int $workshopId): ?Enrollment
    {
        return Enrollment::query()
            ->where('workshop_id', $workshopId)
            ->where('status', EnrollmentStatus::Waitlisted)
            ->orderBy('position', 'asc')
            ->first();
    }

    public function getNextWaitlistPosition(int $workshopId): int
    {
        return (Enrollment::query()
            ->where('workshop_id', $workshopId)
            ->where('status', EnrollmentStatus::Waitlisted)
            ->max('position') ?? 0) + 1;
    }

    public function hasOverlappingEnrollment(int $userId, string $startsAt, string $endsAt, ?int $excludeWorkshopId = null): bool
    {
        return Enrollment::query()
            ->join('workshops', 'enrollments.workshop_id', '=', 'workshops.id')
            ->where('enrollments.user_id', $userId)
            ->where('enrollments.status', EnrollmentStatus::Enrolled)
            ->where('workshops.starts_at', '<', $endsAt)
            ->where('workshops.ends_at', '>', $startsAt)
            ->when($excludeWorkshopId !== null, fn ($q) => $q->where('workshops.id', '!=', $excludeWorkshopId))
            ->exists();
    }

    public function promoteFromWaitlist(Enrollment $enrollment): void
    {
        $enrollment->update(['status' => EnrollmentStatus::Enrolled, 'position' => null]);
    }

    public function reorderWaitlist(int $workshopId): void
    {
        $waitlisted = Enrollment::query()
            ->where('workshop_id', $workshopId)
            ->where('status', EnrollmentStatus::Waitlisted)
            ->orderBy('position', 'asc')
            ->get();

        $position = 1;
        foreach ($waitlisted as $enrollment) {
            $enrollment->update(['position' => $position++]);
        }
    }
}
