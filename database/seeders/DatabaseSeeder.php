<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Role;
use App\Models\Bay;
use App\Models\Shelf;
use App\Models\Make;
use App\Models\Year;
use App\Models\CarModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User provided seed data
        $this->call([
            RoleSeeder::class,
            EmployeeSeeder::class,
            MakeSeeder::class,
            CarModelSeeder::class,
            YearSeeder::class,
        ]);

        Employee::factory()->count(10)->create();

        // Generate Warehouses which have Bays which have Shelves
        Warehouse::factory()
            ->has(Bay::factory()
                ->has(Shelf::factory()
                    ->count(25))
                ->count(25))
            ->count(5)
            ->create();

        // Generate items
        Inventory::factory()->count(100)->create();

        $inventories = Inventory::all();
        $years = Year::all();
        $shelves = Shelf::all();

        // Attach makes and shelves to items
        $inventories->each(function ($i) use ($shelves, $years) {
            $randomShelfId = $shelves->pluck("id")->random();
            $randomYearId = $years->pluck("id")->random();
            $i->shelves()->attach($randomShelfId, [
                "stock_quantity" => random_int(0, 15),
            ]);
            $i->years()->attach($randomYearId);
        });
    }
}
