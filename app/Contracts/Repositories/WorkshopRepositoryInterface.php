<?php

namespace App\Contracts\Repositories;

use App\Models\Workshop;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface WorkshopRepositoryInterface
{
    public function all(): Collection;

    public function upcoming(): Collection;

    public function find(int $id): Workshop;

    public function create(array $data): Workshop;

    public function update(Workshop $workshop, array $data): Workshop;

    public function delete(Workshop $workshop): bool;

    public function getWorkshopsForDate(Carbon $date): Collection;

    public function getMostPopular(): ?Workshop;

    public function getEnrollmentStats(): Collection;

    public function getUpcomingCount(): int;

    public function getTotalEnrollmentsCount(): int;
}
