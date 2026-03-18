<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Workshop;

it('shows statistics dashboard to admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Admin/Statistics/Index'));
});

it('returns correct most popular workshop in statistics', function () {
    $admin = User::factory()->admin()->create();

    $workshopA = Workshop::factory()->create(['title' => 'Workshop A']);
    $workshopB = Workshop::factory()->create(['title' => 'Workshop B']);

    Enrollment::factory()->count(3)->create([
        'workshop_id' => $workshopA->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);
    Enrollment::factory()->count(1)->create([
        'workshop_id' => $workshopB->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Statistics/Index')
        ->where('most_popular.title', 'Workshop A')
    );
});

it('returns correct total enrollments count', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create();

    Enrollment::factory()->count(5)->create([
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);
    Enrollment::factory()->count(2)->create([
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Statistics/Index')
        ->where('total_enrollments', 5)
    );
});

it('prevents employee from accessing statistics dashboard', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->actingAs($employee)->get(route('admin.statistics.index'));

    $response->assertForbidden();
});

it('returns null for most_popular when no workshops exist', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Statistics/Index')
        ->where('most_popular', null)
    );
});

it('returns zero for total_enrollments when no enrollments exist', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Statistics/Index')
        ->where('total_enrollments', 0)
    );
});

it('calculates fill_percentage correctly in workshop stats', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create(['capacity' => 10]);

    Enrollment::factory()->count(5)->create([
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.statistics.index'));

    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Statistics/Index')
        ->where('workshop_stats.0.fill_percentage', 50)
    );
});
