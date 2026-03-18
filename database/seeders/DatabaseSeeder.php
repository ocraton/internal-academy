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
        foreach (['employee1', 'employee2', 'employee3'] as $index => $slug) {
            $employees[] = User::factory()->employee()->create([
                'name' => 'Employee '.($index + 1),
                'email' => $slug.'@academy.test',
                'password' => Hash::make('password'),
            ]);
        }

        $workshops = Workshop::factory()->count(5)->create();

        Enrollment::create([
            'user_id' => $employees[0]->id,
            'workshop_id' => $workshops[0]->id,
            'status' => EnrollmentStatus::Enrolled,
        ]);

        Enrollment::create([
            'user_id' => $employees[0]->id,
            'workshop_id' => $workshops[1]->id,
            'status' => EnrollmentStatus::Enrolled,
        ]);
    }
}
