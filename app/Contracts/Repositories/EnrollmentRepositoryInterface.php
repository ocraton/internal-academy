<?php

namespace App\Contracts\Repositories;

use App\Models\Enrollment;

interface EnrollmentRepositoryInterface
{
    public function findByUserAndWorkshop(int $userId, int $workshopId): ?Enrollment;

    public function getEnrolledCount(int $workshopId): int;

    public function createEnrollment(array $data): Enrollment;

    public function deleteEnrollment(Enrollment $enrollment): bool;
}
