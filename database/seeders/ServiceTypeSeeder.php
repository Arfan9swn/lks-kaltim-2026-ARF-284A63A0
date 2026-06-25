<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Perbaikan Jalan',
                'description' => 'Layanan perbaikan jalan rusak, lubang, atau kerusakan lainnya',
                'estimated_days' => 7,
            ],
            [
                'name' => 'Pengangkutan Sampah',
                'description' => 'Layanan pengangkutan dan pembuangan sampah',
                'estimated_days' => 3,
            ],
            [
                'name' => 'Perbaikan Lampu Jalan',
                'description' => 'Layanan perbaikan dan pemasangan lampu jalan',
                'estimated_days' => 5,
            ],
            [
                'name' => 'Pembersihan Saluran Air',
                'description' => 'Layanan pembersihan dan perbaikan saluran air',
                'estimated_days' => 4,
            ],
            [
                'name' => 'Perbaikan Fasilitas Umum',
                'description' => 'Layanan perbaikan fasilitas umum seperti taman, halte, dll',
                'estimated_days' => 10,
            ],
            [
                'name' => 'Pengaduan Kriminalitas',
                'description' => 'Layanan pengaduan terkait keamanan dan kriminalitas',
                'estimated_days' => 2,
            ],
            [
                'name' => 'Bantuan Medis',
                'description' => 'Layanan bantuan medis dan kesehatan',
                'estimated_days' => 1,
            ],
            [
                'name' => 'Pendidikan',
                'description' => 'Layanan terkait pendidikan dan beasiswa',
                'estimated_days' => 14,
            ],
            [
                'name' => 'Ketenagakerjaan',
                'description' => 'Layanan informasi dan bantuan ketenagakerjaan',
                'estimated_days' => 7,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Layanan lainnya yang tidak termasuk dalam kategori di atas',
                'estimated_days' => 5,
            ],
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::create($serviceType);
        }
    }
}