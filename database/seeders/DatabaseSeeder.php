<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmployeeSeeder::class,
            RoleSeeder::class,
        ]);

        Employee::factory()->count(10)->create();

        $roles = Role::all();
        $employees = Employee::all();
        $superAdmin = $employees->first()->roles()->attach(["role_id" => 1]);
        $tester = $employees->get(1)->roles()->attach(["role_id" => 2]);
        $rest = $employees->slice(2);

        $rest->each(function ($e) {
            $e->roles()->attach(["role_id" => 3]);
        });

        Warehouse::factory()
            ->has(Inventory::factory()
                ->count(50))
            ->count(5)
            ->create();
    }
}
