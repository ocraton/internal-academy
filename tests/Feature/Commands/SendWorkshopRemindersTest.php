<?php

use App\Enums\EnrollmentStatus;
use App\Mail\WorkshopReminderMail;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Mail;

it('sends reminder emails only to enrolled participants of tomorrows workshops', function () {
    $workshop = Workshop::factory()->create([
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHours(2),
    ]);

    $enrolledUsers = User::factory()->count(2)->create();
    $waitlistedUser = User::factory()->create();

    foreach ($enrolledUsers as $user) {
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => EnrollmentStatus::Enrolled,
        ]);
    }

    Enrollment::factory()->create([
        'user_id' => $waitlistedUser->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted,
    ]);

    Mail::fake();

    $this->artisan('academy:remind')->assertSuccessful();

    Mail::assertQueued(WorkshopReminderMail::class, 2);
});

it('does not send emails for workshops not scheduled for tomorrow', function () {
    $workshop = Workshop::factory()->create([
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addHours(2),
    ]);

    Enrollment::factory()->create([
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    Mail::fake();

    $this->artisan('academy:remind')->assertSuccessful();

    Mail::assertNothingQueued();
});

it('sends emails to enrolled users across multiple workshops', function () {
    $workshopA = Workshop::factory()->create([
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHours(2),
    ]);

    $workshopB = Workshop::factory()->create([
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHours(3),
    ]);

    foreach ([$workshopA, $workshopB] as $workshop) {
        Enrollment::factory()->count(2)->create([
            'workshop_id' => $workshop->id,
            'status' => EnrollmentStatus::Enrolled,
        ]);
    }

    Mail::fake();

    $this->artisan('academy:remind')->assertSuccessful();

    Mail::assertQueued(WorkshopReminderMail::class, 4);
});

it('sends no emails when no workshops are scheduled for tomorrow', function () {
    Mail::fake();

    $this->artisan('academy:remind')->assertSuccessful();

    Mail::assertNothingQueued();
});
