<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $types = ['service_request', 'report', 'system', 'announcement'];
        $messages = [
            'Permintaan layanan Anda sedang diproses',
            'Laporan Anda telah diterima',
            'Status laporan telah diperbarui',
            'Anda memiliki pesan baru',
            'Layanan baru tersedia',
            'Pengingat: Jadwal maintenance',
            'Pengumuman penting dari admin',
            'Feedback Anda telah diterima',
            'Permintaan layanan telah selesai',
            'Notifikasi sistem',
        ];

        foreach (range(1, 10) as $i) {
            Notification::create([
                'user_id' => $users->random()->id,
                'message' => $messages[$i - 1],
                'type' => $types[array_rand($types)],
                'is_read' => rand(0, 1) ? true : false,
                'reference_id' => rand(1, 100),
                'reference_type' => $types[array_rand($types)],
            ]);
        }
    }
}