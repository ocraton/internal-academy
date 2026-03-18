<?php

namespace App\Repositories;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkshopRepository implements WorkshopRepositoryInterface
{
    public function all(): Collection
    {
        return Workshop::query()->orderBy('starts_at')->get();
    }

    public function upcoming(): Collection
    {
        return Workshop::query()->upcoming()->orderBy('starts_at')->get();
    }

    public function find(int $id): Workshop
    {
        return Workshop::query()->findOrFail($id);
    }

    public function create(array $data): Workshop
    {
        return Workshop::query()->create($data);
    }

    public function update(Workshop $workshop, array $data): Workshop
    {
        $workshop->update($data);

        return $workshop->fresh();
    }

    public function delete(Workshop $workshop): bool
    {
        return $workshop->delete();
    }

    public function getWorkshopsForDate(Carbon $date): Collection
    {
        return Workshop::query()
            ->whereDate('starts_at', $date->toDateString())
            ->with(['enrollments' => fn ($q) => $q->where('status', EnrollmentStatus::Enrolled)->with('user')])
            ->get();
    }

    public function getMostPopular(): ?Workshop
    {
        return Workshop::query()
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q->where('status', EnrollmentStatus::Enrolled)])
            ->orderBy('enrolled_count', 'desc')
            ->first();
    }

    public function getEnrollmentStats(): Collection
    {
        return Workshop::query()
            ->withCount([
                'enrollments as enrolled_count' => fn ($q) => $q->where('status', EnrollmentStatus::Enrolled),
                'enrollments as waitlisted_count' => fn ($q) => $q->where('status', EnrollmentStatus::Waitlisted),
            ])
            ->orderBy('enrolled_count', 'desc')
            ->get();
    }

    public function getUpcomingCount(): int
    {
        return Workshop::query()->where('starts_at', '>', now())->count();
    }

    public function getTotalEnrollmentsCount(): int
    {
        return Enrollment::query()->where('status', EnrollmentStatus::Enrolled)->count();
    }
}
