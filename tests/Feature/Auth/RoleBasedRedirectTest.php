<?php

use App\Models\User;

it('redirects admin to admin dashboard after login', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->post(route('login'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.workshops.index'));
});

it('redirects employee to workshops index after login', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->post(route('login'), [
        'email' => $employee->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('employee.workshops.index'));
});

it('prevents employee from accessing admin routes', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->actingAs($employee)->get('/admin/workshops');

    $response->assertForbidden();
});
