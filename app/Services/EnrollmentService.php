<?php

namespace App\Services;

use App\Contracts\Repositories\EnrollmentRepositoryInterface;
use App\Enums\EnrollmentStatus;
use App\Models\User;
use App\Models\Workshop;

class EnrollmentService
{
    public function __construct(
        private readonly EnrollmentRepositoryInterface $enrollmentRepository
    ) {}

    /**
     * @return array{status: string, message?: string}
     */
    public function enroll(User $user, Workshop $workshop): array
    {
        $existing = $this->enrollmentRepository->findByUserAndWorkshop($user->id, $workshop->id);

        if ($existing !== null) {
            return ['status' => 'already_enrolled'];
        }

        $enrolledCount = $this->enrollmentRepository->getEnrolledCount($workshop->id);

        if ($enrolledCount < $workshop->capacity) {
            $this->enrollmentRepository->createEnrollment([
                'user_id' => $user->id,
                'workshop_id' => $workshop->id,
                'status' => EnrollmentStatus::Enrolled,
            ]);

            return ['status' => 'enrolled'];
        }

        return ['status' => 'full', 'message' => 'Nessun posto disponibile.'];
    }

    public function cancel(User $user, Workshop $workshop): void
    {
        $enrollment = $this->enrollmentRepository->findByUserAndWorkshop($user->id, $workshop->id);

        if ($enrollment === null) {
            return;
        }

        $this->enrollmentRepository->deleteEnrollment($enrollment);
    }
}
