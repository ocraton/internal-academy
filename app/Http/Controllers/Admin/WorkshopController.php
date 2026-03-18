<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\WorkshopRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workshop\StoreWorkshopRequest;
use App\Http\Requests\Workshop\UpdateWorkshopRequest;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkshopController extends Controller
{
    public function __construct(
        private readonly WorkshopService $workshopService,
        private readonly WorkshopRepositoryInterface $workshopRepository,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Workshops/Index', [
            'workshops' => WorkshopResource::collection($this->workshopRepository->all()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Workshops/Create');
    }

    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        $this->workshopService->create($request->validated());

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop creato con successo.');
    }

    public function show(Workshop $workshop): Response
    {
        return Inertia::render('Admin/Workshops/Show', [
            'workshop' => new WorkshopResource($workshop),
        ]);
    }

    public function edit(Workshop $workshop): Response
    {
        return Inertia::render('Admin/Workshops/Edit', [
            'workshop' => new WorkshopResource($workshop),
        ]);
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop): RedirectResponse
    {
        $this->workshopService->update($workshop, $request->validated());

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop aggiornato con successo.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->workshopService->delete($workshop);

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop eliminato con successo.');
    }
}
