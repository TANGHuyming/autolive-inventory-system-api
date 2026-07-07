<?php

namespace Database\Factories;

use App\Models\Bay;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bay>
 */
class BayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => strtoupper($this->faker->randomLetter()),
        ];
    }
}
