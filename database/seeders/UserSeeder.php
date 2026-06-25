<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password123'),
                'role' => $i === 1 ? 'admin' : 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        User::insert($users);
    }
}