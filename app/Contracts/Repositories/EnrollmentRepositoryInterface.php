<?php

namespace App\Contracts\Repositories;

use App\Models\Enrollment;

interface EnrollmentRepositoryInterface
{
    public function findByUserAndWorkshop(int $userId, int $workshopId): ?Enrollment;

    public function getEnrolledCount(int $workshopId): int;

    public function createEnrollment(array $data): Enrollment;

    public function deleteEnrollment(Enrollment $enrollment): bool;

    public function getFirstWaitlisted(int $workshopId): ?Enrollment;

    public function getNextWaitlistPosition(int $workshopId): int;

    public function hasOverlappingEnrollment(int $userId, string $startsAt, string $endsAt, ?int $excludeWorkshopId = null): bool;

    public function promoteFromWaitlist(Enrollment $enrollment): void;

    public function reorderWaitlist(int $workshopId): void;
}
