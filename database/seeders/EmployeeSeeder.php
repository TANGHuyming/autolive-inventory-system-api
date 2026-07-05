<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("employees")->insert([
            ["first_name" => "Super", "last_name" => "Admin", "email" => "superadmin@email.com", "telephone" => "015978206", "password" => Hash::make("password")],
            ["first_name" => "tester", "last_name" => "tester", "email" => "test@gmail.com", "telephone" => "0159782906", "password" => Hash::make("password")],
        ]);
    }
}
