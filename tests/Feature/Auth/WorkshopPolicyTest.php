<?php

use App\Models\User;
use App\Models\Workshop;

it('allows employee to view upcoming workshops', function () {
    $employee = User::factory()->employee()->create();
    Workshop::factory()->count(2)->create([
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    $response = $this->actingAs($employee)->get(route('employee.workshops.index'));

    $response->assertOk();
});

it('prevents employee from enrolling in a past workshop', function () {
    $employee = User::factory()->employee()->create();
    $pastWorkshop = Workshop::factory()->create([
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->subDays(2)->addHours(2),
    ]);

    $response = $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $pastWorkshop));

    $response->assertForbidden();
});

it('prevents guest from enrolling in any workshop', function () {
    $workshop = Workshop::factory()->create([
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    $response = $this->post(route('employee.workshops.enroll', $workshop));

    $response->assertRedirect(route('login'));
});

it('allows admin to perform CRUD on any workshop', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.workshops.index'));

    $response->assertOk();
});

it('prevents employee from accessing admin workshop routes', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->actingAs($employee)->get(route('admin.workshops.index'));

    $response->assertForbidden();
});
