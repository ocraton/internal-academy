<?php

use App\Contracts\Repositories\EnrollmentRepositoryInterface;
use App\Contracts\Repositories\WorkshopRepositoryInterface;

test('controllers do not use Eloquent models directly')
    ->expect('App\Http\Controllers')
    ->not->toUse(['Illuminate\Database\Eloquent\Model']);

test('enrollment repository implements its contract')
    ->expect('App\Repositories\EnrollmentRepository')
    ->toImplement(EnrollmentRepositoryInterface::class);

test('workshop repository implements its contract')
    ->expect('App\Repositories\WorkshopRepository')
    ->toImplement(WorkshopRepositoryInterface::class);

test('services do not extend anything')
    ->expect('App\Services')
    ->toExtendNothing();

test('models do not have business logic')
    ->expect('App\Models')
    ->not->toUse('App\Services');

test('enums are backed')
    ->expect('App\Enums')
    ->toBeStringBackedEnums();

test('no debug functions are used')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'ddd'])
    ->not->toBeUsed();
