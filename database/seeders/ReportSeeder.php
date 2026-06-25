<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $categories = ['infrastructure', 'environment', 'social', 'other'];
        $statuses = ['open', 'in_progress', 'resolved'];
        $locations = [
            'Jl. Sudirman, Samarinda',
            'Jl. Ahmad Yani, Balikpapan',
            'Jl. MT Haryono, Samarinda',
            'Jl. Letjend Suprapto, Samarinda',
            'Jl. Pahlawan, Balikpapan',
            'Jl. Diponegoro, Samarinda',
            'Jl. Cipto Mangunkusumo, Balikpapan',
            'Jl. Gajah Mada, Samarinda',
            'Jl. Veteran, Balikpapan',
            'Jl. Pattimura, Samarinda',
        ];

        foreach (range(1, 10) as $i) {
            Report::create([
                'user_id' => $users->random()->id,
                'category' => $categories[array_rand($categories)],
                'title' => "Laporan #$i: " . $this->getRandomTitle(),
                'description' => "Deskripsi laporan #$i. Ini adalah contoh deskripsi untuk laporan masyarakat.",
                'location' => $locations[$i - 1],
                'image_url' => null, // Excluding images as requested
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }

    private function getRandomTitle(): string
    {
        $titles = [
            'Kerusakan jalan di pusat kota',
            'Tumpukan sampah di pinggir jalan',
            'Lampu jalan mati',
            'Saluran air tersumbat',
            'Fasilitas umum rusak',
            'Keamanan lingkungan',
            'Kebersihan lingkungan',
            'Perbaikan infrastruktur',
            'Laporan lingkungan',
            'Pengaduan masyarakat',
        ];

        return $titles[array_rand($titles)];
    }
}