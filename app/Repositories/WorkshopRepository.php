<?php

namespace App\Repositories;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Enums\EnrollmentStatus;
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
}
