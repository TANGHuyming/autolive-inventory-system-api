<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Year;
use App\Models\CarModel;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $years = [];
        for ($i = 2019; $i <= date("Y"); $i++) {
            $years[] = $i;
        }

        $carModels = CarModel::all();
        foreach ($carModels as $model) {
            foreach ($years as $year) {
                Year::updateOrCreate([
                    "car_model_id" => $model->id,
                    "year" => $year,
                ]);
            }
        }
    }
}
