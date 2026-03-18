<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkshopResource;
use App\Http\Resources\WorkshopStatsResource;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController extends Controller
{
    public function __construct(private readonly WorkshopRepositoryInterface $workshopRepository) {}

    public function index(): Response
    {
        $mostPopular = $this->workshopRepository->getMostPopular();

        $enrollmentStats = $this->workshopRepository->getEnrollmentStats();

        return Inertia::render('Admin/Statistics/Index', [
            'most_popular' => $mostPopular ? (new WorkshopResource($mostPopular))->resolve() : null,
            'workshop_stats' => WorkshopStatsResource::collection($enrollmentStats)->resolve(),
            'upcoming_count' => $this->workshopRepository->getUpcomingCount(),
            'total_enrollments' => $this->workshopRepository->getTotalEnrollmentsCount(),
        ]);
    }
}
