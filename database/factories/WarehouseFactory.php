<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $warehouseNames = ["Khmounh", "Hengly", "Komar Cheat"];

        return [
            "name" => fake()->randomElement($warehouseNames),
            "city" => fake()->city(),
            "district" => fake()->streetName(),
            "commune" => fake()->streetName(),
            "village" => fake()->streetName(),
            "street" => fake()->buildingNumber(),
            "house_number" => fake()->buildingNumber(),
        ];
    }
}
