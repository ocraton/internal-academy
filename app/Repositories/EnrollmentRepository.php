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
}
