<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            ["name" => "super_admin", "description" => "Can do everything and manipulate roles"],
            ["name" => "admin", "description" => "Can do everything"],
            ["name" => "employee", "description" => "Employee"],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate($role);
        }
    }
}
