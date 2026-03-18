<?php

namespace App\Services;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Models\Workshop;

class WorkshopService
{
    public function __construct(private readonly WorkshopRepositoryInterface $workshopRepository) {}

    public function create(array $data): Workshop
    {
        return $this->workshopRepository->create($data);
    }

    public function update(Workshop $workshop, array $data): Workshop
    {
        return $this->workshopRepository->update($workshop, $data);
    }

    public function delete(Workshop $workshop): bool
    {
        return $this->workshopRepository->delete($workshop);
    }
}
