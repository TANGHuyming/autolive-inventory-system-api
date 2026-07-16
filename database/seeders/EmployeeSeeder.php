<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ["first_name" => "Super", "last_name" => "Admin", "email" => "superadmin@email.com", "telephone" => "015978206", "password" => Hash::make("password"), "role_id" => 1],
            ["first_name" => "tester", "last_name" => "tester", "email" => "test@gmail.com", "telephone" => "0159782906", "password" => Hash::make("password"), "role_id" => 2],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate($employee);
        }
    }
}
