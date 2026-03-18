<?php

namespace Database\Seeders;

use App\Models\User;
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

        foreach (['employee1', 'employee2', 'employee3'] as $index => $slug) {
            User::factory()->employee()->create([
                'name' => 'Employee '.($index + 1),
                'email' => $slug.'@academy.test',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
