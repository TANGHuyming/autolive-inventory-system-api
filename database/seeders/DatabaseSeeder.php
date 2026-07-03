<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\Inventory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Employee::factory()->count(10)->create();
        Warehouse::factory()
            ->has(Inventory::factory()
                ->count(50))
            ->count(5)
            ->create();

        $this->call([
            EmployeeSeeder::class,
        ]);
    }
}
