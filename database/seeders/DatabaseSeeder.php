<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@academy.test',
            'password' => Hash::make('password'),
        ]);

        $employees = [];
        foreach (range(1, 5) as $i) {
            $employees[] = User::factory()->employee()->create([
                'name' => 'Employee '.$i,
                'email' => 'employee'.$i.'@academy.test',
                'password' => Hash::make('password'),
            ]);
        }

        // Workshop 1: capacity 2, pieno + 1 in waitlist
        $workshop1 = Workshop::factory()->create([
            'title' => 'Workshop Avanzato di Laravel',
            'description' => 'Approfondiamo le funzionalità avanzate di Laravel 13.',
            'capacity' => 2,
            'starts_at' => now()->addDays(7),
            'ends_at' => now()->addDays(7)->addHours(3),
        ]);

        Enrollment::create(['user_id' => $employees[0]->id, 'workshop_id' => $workshop1->id, 'status' => EnrollmentStatus::Enrolled]);
        Enrollment::create(['user_id' => $employees[1]->id, 'workshop_id' => $workshop1->id, 'status' => EnrollmentStatus::Enrolled]);
        Enrollment::create(['user_id' => $employees[2]->id, 'workshop_id' => $workshop1->id, 'status' => EnrollmentStatus::Waitlisted, 'position' => 1]);

        // Workshop 2: capacity 5, 3 iscritti
        $workshop2 = Workshop::factory()->create([
            'title' => 'Introduzione a Vue 3',
            'description' => 'Composition API, script setup e integrazione con Inertia.',
            'capacity' => 5,
            'starts_at' => now()->addDays(14),
            'ends_at' => now()->addDays(14)->addHours(2),
        ]);

        Enrollment::create(['user_id' => $employees[0]->id, 'workshop_id' => $workshop2->id, 'status' => EnrollmentStatus::Enrolled]);
        Enrollment::create(['user_id' => $employees[2]->id, 'workshop_id' => $workshop2->id, 'status' => EnrollmentStatus::Enrolled]);
        Enrollment::create(['user_id' => $employees[3]->id, 'workshop_id' => $workshop2->id, 'status' => EnrollmentStatus::Enrolled]);

        // Workshop 3: capacity 20, 1 iscritto
        $workshop3 = Workshop::factory()->create([
            'title' => 'Tailwind CSS per tutti',
            'description' => 'Utility-first CSS con Tailwind v3 e responsive design.',
            'capacity' => 20,
            'starts_at' => now()->addDays(21),
            'ends_at' => now()->addDays(21)->addHours(2),
        ]);

        Enrollment::create(['user_id' => $employees[4]->id, 'workshop_id' => $workshop3->id, 'status' => EnrollmentStatus::Enrolled]);

        // Workshop passato: 2 iscritti
        $workshopPast = Workshop::factory()->create([
            'title' => 'PHP 8.4 Novità',
            'description' => 'Overview delle nuove funzionalità di PHP 8.4.',
            'capacity' => 20,
            'starts_at' => now()->subDays(7),
            'ends_at' => now()->subDays(7)->addHours(2),
        ]);

        Enrollment::create(['user_id' => $employees[0]->id, 'workshop_id' => $workshopPast->id, 'status' => EnrollmentStatus::Enrolled]);
        Enrollment::create(['user_id' => $employees[1]->id, 'workshop_id' => $workshopPast->id, 'status' => EnrollmentStatus::Enrolled]);
    }
}
