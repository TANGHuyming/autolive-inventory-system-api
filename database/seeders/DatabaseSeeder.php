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

        // Stock items to shelves
        $inventories = Inventory::all();
        $shelves = Shelf::all();
        $inventories->each(function ($i) use ($shelves) {
            $randomShelfId = $shelves->pluck("id")->random();
            $i->shelves()->attach($randomShelfId, [
                "stock_quantity" => random_int(0, 15),
            ]);
        });
    }
}
