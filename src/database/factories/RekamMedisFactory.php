<?php

namespace Database\Factories;

use App\Models\Dokter;
use App\Models\Pasien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RekamMedis>
 */
class RekamMedisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pasien_id' => Pasien::factory(),
            'dokter_id' => Dokter::factory(),
            'tanggal' => fake()->dateTimeBetween('-1 year', 'now'),
            'diagnosa' => fake()->paragraph(),
            'pengobatan' => fake()->paragraph(),
        ];
    }
}
