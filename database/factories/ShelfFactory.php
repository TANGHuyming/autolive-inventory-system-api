<?php

namespace Database\Factories;

use App\Models\Shelf;
use App\Models\Bay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shelf>
 */
class ShelfFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => (string) $this->faker->randomNumber(4, true),
        ];
    }
}
