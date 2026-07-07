<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partNames = ["Front Fender", "Front Bumper","Rear Fender", "Rear Bumper", "Side View Mirror LH", "Side View Mirror RH"];
        $makes = ["Ford", "Changan", "Mazda"];
        $models = ["Model1", "Model2", "Model3"];
        $years = ["2022", "2023", "2024"];

        return [
            //
            "nameEn" => fake()->randomElement($partNames),
            "nameKh" => null,
            "make" => fake()->randomElement($makes),
            "model" => fake()->randomElement($models),
            "year" => fake()->randomElement($years),
            "code" => fake()->unique()->randomNumber(8, true),
            "picture_url" => null,
        ];
    }
}
