<?php

use App\Models\User;
use App\Models\Workshop;

function workshopData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Test Workshop',
        'description' => 'Una descrizione di prova',
        'starts_at' => now()->addDays(10)->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(10)->addHours(2)->format('Y-m-d H:i:s'),
        'capacity' => 20,
    ], $overrides);
}

it('allows admin to create a workshop', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.workshops.store'), workshopData());

    $response->assertRedirect(route('admin.workshops.index'));
    $this->assertDatabaseHas('workshops', ['title' => 'Test Workshop']);
});

it('prevents employee from creating a workshop', function () {
    $employee = User::factory()->employee()->create();

    $response = $this->actingAs($employee)
        ->post(route('admin.workshops.store'), workshopData());

    $response->assertForbidden();
});

it('allows admin to update a workshop', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($admin)
        ->put(route('admin.workshops.update', $workshop), workshopData(['title' => 'Titolo Aggiornato']));

    $response->assertRedirect(route('admin.workshops.index'));
    $this->assertDatabaseHas('workshops', ['id' => $workshop->id, 'title' => 'Titolo Aggiornato']);
});

it('allows admin to delete a workshop', function () {
    $admin = User::factory()->admin()->create();
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($admin)
        ->delete(route('admin.workshops.destroy', $workshop));

    $response->assertRedirect(route('admin.workshops.index'));
    $this->assertDatabaseMissing('workshops', ['id' => $workshop->id]);
});

it('validates required fields when creating a workshop', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.workshops.store'), []);

    $response->assertSessionHasErrors(['title', 'description', 'starts_at', 'ends_at', 'capacity']);
});
