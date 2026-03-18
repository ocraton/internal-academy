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
     * @return array{status: string, message?: string, position?: int}
     */
    public function enroll(User $user, Workshop $workshop): array
    {
        $existing = $this->enrollmentRepository->findByUserAndWorkshop($user->id, $workshop->id);

        if ($existing !== null) {
            return ['status' => 'already_enrolled'];
        }

        $hasOverlap = $this->enrollmentRepository->hasOverlappingEnrollment(
            $user->id,
            $workshop->starts_at->toDateTimeString(),
            $workshop->ends_at->toDateTimeString(),
            $workshop->id
        );

        if ($hasOverlap) {
            return ['status' => 'overlap', 'message' => 'Hai già un workshop in questo orario.'];
        }

        $enrolledCount = $this->enrollmentRepository->getEnrolledCount($workshop->id);

        if ($enrolledCount < $workshop->capacity) {
            $this->enrollmentRepository->createEnrollment([
                'user_id' => $user->id,
                'workshop_id' => $workshop->id,
                'status' => EnrollmentStatus::Enrolled,
                'position' => null,
            ]);

            return ['status' => 'enrolled'];
        }

        $position = $this->enrollmentRepository->getNextWaitlistPosition($workshop->id);

        $this->enrollmentRepository->createEnrollment([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => EnrollmentStatus::Waitlisted,
            'position' => $position,
        ]);

        return ['status' => 'waitlisted', 'position' => $position];
    }

    public function cancel(User $user, Workshop $workshop): void
    {
        $enrollment = $this->enrollmentRepository->findByUserAndWorkshop($user->id, $workshop->id);

        if ($enrollment === null) {
            return;
        }

        $wasEnrolled = $enrollment->status === EnrollmentStatus::Enrolled;

        $this->enrollmentRepository->deleteEnrollment($enrollment);

        if ($wasEnrolled) {
            $firstWaitlisted = $this->enrollmentRepository->getFirstWaitlisted($workshop->id);

            if ($firstWaitlisted !== null) {
                $this->enrollmentRepository->promoteFromWaitlist($firstWaitlisted);
            }

            $this->enrollmentRepository->reorderWaitlist($workshop->id);
        } else {
            $this->enrollmentRepository->reorderWaitlist($workshop->id);
        }
    }
}
