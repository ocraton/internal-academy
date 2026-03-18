<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Models\Workshop;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    public function store(StoreEnrollmentRequest $request, Workshop $workshop): RedirectResponse
    {
        $result = $this->enrollmentService->enroll($request->user(), $workshop);

        return match ($result['status']) {
            'enrolled' => back(303)->with('success', 'Iscrizione confermata!'),
            'waitlisted' => back(303)->with('success', "Workshop pieno. Sei in lista d'attesa (posizione {$result['position']})."),
            'overlap' => back(303)->with('error', $result['message']),
            'already_enrolled' => back(303)->with('error', 'Sei già iscritto a questo workshop.'),
            default => back(303),
        };
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $this->enrollmentService->cancel(auth()->user(), $workshop);

        return back(303)->with('success', 'Iscrizione cancellata.');
    }
}
