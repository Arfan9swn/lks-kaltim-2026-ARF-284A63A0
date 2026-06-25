<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters due to foreign key constraints
        $this->call([
            UserSeeder::class,           // Create users first
            ServiceTypeSeeder::class,    // Create service types
            ServiceRequestSeeder::class, // Depends on users and service types
            ReportSeeder::class,         // Depends on users
            NotificationSeeder::class,   // Depends on users
        ]);
    }
}