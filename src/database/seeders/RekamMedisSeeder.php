<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\RekamMedis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RekamMedisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $pasienIds = Pasien::pluck('id')->toArray();
        $dokterIds = Dokter::pluck('id')->toArray();
        
        // Pastikan ada data pasien dan dokter sebelum membuat rekam medis
        if (!empty($pasienIds) && !empty($dokterIds)) {
            foreach (range(1, 20) as $index) {
                RekamMedis::factory()->create([
                    'pasien_id' => fake()->randomElement($pasienIds),
                    'dokter_id' => fake()->randomElement($dokterIds),
                ]);
            }
        }
    }
}
