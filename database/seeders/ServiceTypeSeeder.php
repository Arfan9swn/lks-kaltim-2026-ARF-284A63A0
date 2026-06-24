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
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat keterangan domisili untuk keperluan administrasi',
                'estimated_days' => 3
            ],
            [
                'name' => 'Surat Keterangan Tidak Mampu',
                'description' => 'Surat keterangan tidak mampu untuk keperluan beasiswa atau bantuan',
                'estimated_days' => 2
            ],
            [
                'name' => 'Surat Pengantar RT/RW',
                'description' => 'Surat pengantar dari RT/RW untuk keperluan administrasi',
                'estimated_days' => 1
            ],
            [
                'name' => 'Surat Keterangan Usaha',
                'description' => 'Surat keterangan usaha untuk UMKM',
                'estimated_days' => 3
            ],
            [
                'name' => 'Surat Keterangan Kelahiran',
                'description' => 'Surat keterangan kelahiran untuk administrasi kependudukan',
                'estimated_days' => 2
            ]
        ];

        foreach ($serviceTypes as $serviceType) {
            ServiceType::create($serviceType);
        }
    }
}