<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "first_name" => fake()->name(),
            "last_name" => fake()->name(),
            "email" => fake()->email(),
            "telephone" => fake()->phoneNumber(),
            "password" => Hash::make("password"),
        ];
    }
}
