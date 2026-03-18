<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Workshop;

it('shows upcoming workshops to employee', function () {
    $employee = User::factory()->employee()->create();
    Workshop::factory()->count(3)->create(['starts_at' => now()->addDays(5), 'ends_at' => now()->addDays(5)->addHours(2)]);
    Workshop::factory()->create(['starts_at' => now()->subDay(), 'ends_at' => now()->subDay()->addHours(2)]);

    $response = $this->actingAs($employee)->get(route('employee.workshops.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Employee/Workshops/Index')
        ->has('workshops.data', 3)
    );
});

it('allows employee to enroll in a workshop with available spots', function () {
    $employee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 10,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    $response = $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseHas('enrollments', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled->value,
    ]);
});

it('prevents double enrollment in the same workshop', function () {
    $employee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 10,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    Enrollment::create([
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $workshop));

    $this->assertDatabaseCount('enrollments', 1);
});

it('allows employee to cancel enrollment', function () {
    $employee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    Enrollment::create([
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $response = $this->actingAs($employee)
        ->delete(route('employee.workshops.unenroll', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
    ]);
});

it('prevents enrollment when workshop is full', function () {
    $employee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 1,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    $otherEmployee = User::factory()->employee()->create();
    Enrollment::create([
        'user_id' => $otherEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $workshop));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted->value,
    ]);
});

it('prevents admin from accessing employee routes', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('employee.workshops.index'));

    $response->assertForbidden();
});

it('adds user to waitlist when workshop is at capacity', function () {
    $employee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 1,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    $otherEmployee = User::factory()->employee()->create();
    Enrollment::create([
        'user_id' => $otherEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $workshop));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted->value,
        'position' => 1,
    ]);
});

it('automatically promotes waitlisted user when enrolled user cancels', function () {
    $enrolledEmployee = User::factory()->employee()->create();
    $waitlistedEmployee = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 1,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    Enrollment::create([
        'user_id' => $enrolledEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    Enrollment::create([
        'user_id' => $waitlistedEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted,
        'position' => 1,
    ]);

    $this->actingAs($enrolledEmployee)
        ->delete(route('employee.workshops.unenroll', $workshop));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $waitlistedEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled->value,
        'position' => null,
    ]);
});

it('prevents enrollment in temporally overlapping workshop', function () {
    $employee = User::factory()->employee()->create();

    $workshop1 = Workshop::factory()->create([
        'capacity' => 10,
        'starts_at' => now()->addDays(5)->setTime(10, 0),
        'ends_at' => now()->addDays(5)->setTime(12, 0),
    ]);

    $workshop2 = Workshop::factory()->create([
        'capacity' => 10,
        'starts_at' => now()->addDays(5)->setTime(11, 0),
        'ends_at' => now()->addDays(5)->setTime(13, 0),
    ]);

    Enrollment::create([
        'user_id' => $employee->id,
        'workshop_id' => $workshop1->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    $this->actingAs($employee)
        ->post(route('employee.workshops.enroll', $workshop2));

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $employee->id,
        'workshop_id' => $workshop2->id,
    ]);
});

it('reorders waitlist positions correctly after promotion', function () {
    $enrolledEmployee = User::factory()->employee()->create();
    $waitlisted1 = User::factory()->employee()->create();
    $waitlisted2 = User::factory()->employee()->create();
    $workshop = Workshop::factory()->create([
        'capacity' => 1,
        'starts_at' => now()->addDays(5),
        'ends_at' => now()->addDays(5)->addHours(2),
    ]);

    Enrollment::create([
        'user_id' => $enrolledEmployee->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled,
    ]);

    Enrollment::create([
        'user_id' => $waitlisted1->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted,
        'position' => 1,
    ]);

    Enrollment::create([
        'user_id' => $waitlisted2->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted,
        'position' => 2,
    ]);

    $this->actingAs($enrolledEmployee)
        ->delete(route('employee.workshops.unenroll', $workshop));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $waitlisted1->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Enrolled->value,
        'position' => null,
    ]);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $waitlisted2->id,
        'workshop_id' => $workshop->id,
        'status' => EnrollmentStatus::Waitlisted->value,
        'position' => 1,
    ]);
});
