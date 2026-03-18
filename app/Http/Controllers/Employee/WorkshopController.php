<?php

namespace App\Http\Controllers\Employee;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function index(WorkshopRepositoryInterface $repo): Response
    {
        return Inertia::render('Employee/Workshops/Index', [
            'workshops' => WorkshopResource::collection($repo->upcoming()),
        ]);
    }

    public function show(Workshop $workshop): Response
    {
        return Inertia::render('Employee/Workshops/Show', [
            'workshop' => new WorkshopResource($workshop),
        ]);
    }
}
