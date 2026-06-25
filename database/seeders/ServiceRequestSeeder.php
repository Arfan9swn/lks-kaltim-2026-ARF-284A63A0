<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\ServiceType;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $serviceTypes = ServiceType::all();
        $statuses = ['pending', 'processing', 'done', 'rejected'];

        foreach (range(1, 10) as $i) {
            ServiceRequest::create([
                'user_id' => $users->random()->id,
                'service_type_id' => $serviceTypes->random()->id,
                'description' => "Deskripsi permintaan layanan #$i. Ini adalah contoh deskripsi untuk permintaan layanan.",
                'attachment_url' => null, // Excluding images as requested
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}