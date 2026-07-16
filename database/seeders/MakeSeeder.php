<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Make;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            ['name' => 'Toyota', 'country_of_origin' => 'Japan'],
            ['name' => 'Honda', 'country_of_origin' => 'Japan'],
            ['name' => 'Nissan', 'country_of_origin' => 'Japan'],
            ['name' => 'Mazda', 'country_of_origin' => 'Japan'],
            ['name' => 'Subaru', 'country_of_origin' => 'Japan'],
            ['name' => 'Mitsubishi', 'country_of_origin' => 'Japan'],
            ['name' => 'Lexus', 'country_of_origin' => 'Japan'],
            ['name' => 'Suzuki', 'country_of_origin' => 'Japan'],
            ['name' => 'Ford', 'country_of_origin' => 'United States'],
            ['name' => 'Chevrolet', 'country_of_origin' => 'United States'],
            ['name' => 'Jeep', 'country_of_origin' => 'United States'],
            ['name' => 'Tesla', 'country_of_origin' => 'United States'],
            ['name' => 'GMC', 'country_of_origin' => 'United States'],
            ['name' => 'Dodge', 'country_of_origin' => 'United States'],
            ['name' => 'Volkswagen', 'country_of_origin' => 'Germany'],
            ['name' => 'BMW', 'country_of_origin' => 'Germany'],
            ['name' => 'Mercedes-Benz', 'country_of_origin' => 'Germany'],
            ['name' => 'Audi', 'country_of_origin' => 'Germany'],
            ['name' => 'Porsche', 'country_of_origin' => 'Germany'],
            ['name' => 'Opel', 'country_of_origin' => 'Germany'],
            ['name' => 'Hyundai', 'country_of_origin' => 'South Korea'],
            ['name' => 'Kia', 'country_of_origin' => 'South Korea'],
            ['name' => 'Genesis', 'country_of_origin' => 'South Korea'],
            ['name' => 'Peugeot', 'country_of_origin' => 'France'],
            ['name' => 'Renault', 'country_of_origin' => 'France'],
            ['name' => 'Citroën', 'country_of_origin' => 'France'],
            ['name' => 'Fiat', 'country_of_origin' => 'Italy'],
            ['name' => 'Alfa Romeo', 'country_of_origin' => 'Italy'],
            ['name' => 'Ferrari', 'country_of_origin' => 'Italy'],
            ['name' => 'Lamborghini', 'country_of_origin' => 'Italy'],
            ['name' => 'Volvo', 'country_of_origin' => 'Sweden'],
            ['name' => 'Land Rover', 'country_of_origin' => 'United Kingdom'],
            ['name' => 'Jaguar', 'country_of_origin' => 'United Kingdom'],
            ['name' => 'Mini', 'country_of_origin' => 'United Kingdom'],
            ['name' => 'Bentley', 'country_of_origin' => 'United Kingdom'],
            ['name' => 'BYD', 'country_of_origin' => 'China'],
            ['name' => 'Geely', 'country_of_origin' => 'China'],
            ['name' => 'Great Wall Motors', 'country_of_origin' => 'China'],
            ['name' => 'Perodua', 'country_of_origin' => 'Malaysia'],
            ['name' => 'Proton', 'country_of_origin' => 'Malaysia'],
        ];

        foreach ($makes as $make) {
            Make::updateOrCreate(
                ['name' => $make['name']],
                $make
            );
        }
    }
}
