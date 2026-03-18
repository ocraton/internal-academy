<?php

use App\Contracts\Repositories\EnrollmentRepositoryInterface;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Workshop;
use App\Services\EnrollmentService;
use Mockery\MockInterface;

function makeService(EnrollmentRepositoryInterface $repo): EnrollmentService
{
    return new EnrollmentService($repo);
}

function makeWorkshop(int $capacity = 10): Workshop
{
    $workshop = new Workshop;
    $workshop->id = 1;
    $workshop->capacity = $capacity;
    $workshop->starts_at = now()->addDays(5);
    $workshop->ends_at = now()->addDays(5)->addHours(2);

    return $workshop;
}

it('enrolls user successfully when spots are available', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(10);

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn(null);
        $mock->shouldReceive('hasOverlappingEnrollment')->andReturn(false);
        $mock->shouldReceive('getEnrolledCount')->with($workshop->id)->andReturn(5);
        $mock->shouldReceive('createEnrollment')->once()->andReturn(new Enrollment);
    });

    $result = makeService($repo)->enroll($user, $workshop);

    expect($result['status'])->toBe('enrolled');
});

it('adds user to waitlist when workshop is full', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(1);

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn(null);
        $mock->shouldReceive('hasOverlappingEnrollment')->andReturn(false);
        $mock->shouldReceive('getEnrolledCount')->with($workshop->id)->andReturn(1);
        $mock->shouldReceive('getNextWaitlistPosition')->with($workshop->id)->andReturn(1);
        $mock->shouldReceive('createEnrollment')->once()->andReturn(new Enrollment);
    });

    $result = makeService($repo)->enroll($user, $workshop);

    expect($result['status'])->toBe('waitlisted')
        ->and($result['position'])->toBe(1);
});

it('promotes first waitlisted user when enrolled user cancels', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(1);

    $enrollment = new Enrollment;
    $enrollment->status = EnrollmentStatus::Enrolled;

    $waitlisted = new Enrollment;
    $waitlisted->status = EnrollmentStatus::Waitlisted;
    $waitlisted->position = 1;

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop, $enrollment, $waitlisted) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn($enrollment);
        $mock->shouldReceive('deleteEnrollment')->with($enrollment)->once()->andReturn(true);
        $mock->shouldReceive('getFirstWaitlisted')->with($workshop->id)->andReturn($waitlisted);
        $mock->shouldReceive('promoteFromWaitlist')->with($waitlisted)->once();
        $mock->shouldReceive('reorderWaitlist')->with($workshop->id)->once();
    });

    makeService($repo)->cancel($user, $workshop);
});

it('maintains FIFO order in waitlist after promotion', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(1);

    $enrollment = new Enrollment;
    $enrollment->status = EnrollmentStatus::Enrolled;

    $firstWaitlisted = new Enrollment;
    $firstWaitlisted->status = EnrollmentStatus::Waitlisted;
    $firstWaitlisted->position = 1;

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($workshop, $enrollment, $firstWaitlisted) {
        $mock->shouldReceive('findByUserAndWorkshop')->andReturn($enrollment);
        $mock->shouldReceive('deleteEnrollment')->andReturn(true);
        $mock->shouldReceive('getFirstWaitlisted')->with($workshop->id)->andReturn($firstWaitlisted);
        $mock->shouldReceive('promoteFromWaitlist')->with($firstWaitlisted)->once();
        $mock->shouldReceive('reorderWaitlist')->with($workshop->id)->once();
    });

    makeService($repo)->cancel($user, $workshop);
});

it('prevents enrollment in overlapping workshop', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(10);

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn(null);
        $mock->shouldReceive('hasOverlappingEnrollment')->andReturn(true);
    });

    $result = makeService($repo)->enroll($user, $workshop);

    expect($result['status'])->toBe('overlap');
});

it('allows enrollment in non-overlapping workshops', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(10);

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn(null);
        $mock->shouldReceive('hasOverlappingEnrollment')->andReturn(false);
        $mock->shouldReceive('getEnrolledCount')->andReturn(0);
        $mock->shouldReceive('createEnrollment')->once()->andReturn(new Enrollment);
    });

    $result = makeService($repo)->enroll($user, $workshop);

    expect($result['status'])->toBe('enrolled');
});

it('returns already_enrolled when user is already enrolled', function () {
    $user = new User;
    $user->id = 1;
    $workshop = makeWorkshop(10);

    $existing = new Enrollment;

    $repo = mock(EnrollmentRepositoryInterface::class, function (MockInterface $mock) use ($user, $workshop, $existing) {
        $mock->shouldReceive('findByUserAndWorkshop')->with($user->id, $workshop->id)->andReturn($existing);
    });

    $result = makeService($repo)->enroll($user, $workshop);

    expect($result['status'])->toBe('already_enrolled');
});
