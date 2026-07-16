<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Make;
use App\Models\CarModel;

class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsByMake = [
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Hilux', 'Land Cruiser', 'Yaris', 'Fortuner', 'Prius'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'HR-V', 'City', 'Jazz', 'Pilot'],
            'Nissan' => ['Almera', 'Sentra', 'X-Trail', 'Navara', 'Patrol', 'Altima', 'Leaf'],
            'Mazda' => ['Mazda2', 'Mazda3', 'Mazda6', 'CX-5', 'CX-30', 'BT-50'],
            'Subaru' => ['Impreza', 'Forester', 'Outback', 'XV', 'WRX'],
            'Mitsubishi' => ['Lancer', 'Outlander', 'Pajero', 'Triton', 'Mirage'],
            'Lexus' => ['IS', 'ES', 'RX', 'NX', 'LX'],
            'Suzuki' => ['Swift', 'Vitara', 'Ertiga', 'Jimny', 'Baleno'],
            'Ford' => ['Ranger', 'Everest', 'Focus', 'Fiesta', 'Mustang', 'Explorer', 'F-150'],
            'Chevrolet' => ['Cruze', 'Malibu', 'Trailblazer', 'Colorado', 'Silverado', 'Camaro'],
            'Jeep' => ['Wrangler', 'Cherokee', 'Grand Cherokee', 'Compass', 'Renegade'],
            'Tesla' => ['Model 3', 'Model S', 'Model X', 'Model Y'],
            'GMC' => ['Sierra', 'Yukon', 'Terrain', 'Acadia'],
            'Dodge' => ['Charger', 'Challenger', 'Durango', 'Ram 1500'],
            'Volkswagen' => ['Golf', 'Polo', 'Passat', 'Tiguan', 'Jetta', 'Touareg'],
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5', '7 Series', 'M3'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE', 'A-Class'],
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7'],
            'Porsche' => ['911', 'Cayenne', 'Macan', 'Panamera', 'Taycan'],
            'Opel' => ['Corsa', 'Astra', 'Insignia', 'Mokka'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Accent', 'Kona'],
            'Kia' => ['Sportage', 'Sorento', 'Cerato', 'Rio', 'Seltos', 'Picanto'],
            'Genesis' => ['G70', 'G80', 'G90', 'GV70', 'GV80'],
            'Peugeot' => ['208', '308', '2008', '3008', '508'],
            'Renault' => ['Clio', 'Megane', 'Duster', 'Captur', 'Kwid'],
            'Citroën' => ['C3', 'C4', 'C5 Aircross', 'Berlingo'],
            'Fiat' => ['500', 'Panda', 'Tipo', 'Doblo'],
            'Alfa Romeo' => ['Giulia', 'Stelvio', 'Giulietta'],
            'Ferrari' => ['Roma', 'Portofino', '488', 'SF90'],
            'Lamborghini' => ['Huracán', 'Aventador', 'Urus'],
            'Volvo' => ['XC40', 'XC60', 'XC90', 'S60', 'S90'],
            'Land Rover' => ['Range Rover', 'Discovery', 'Defender', 'Range Rover Evoque'],
            'Jaguar' => ['XE', 'XF', 'F-Pace', 'E-Pace'],
            'Mini' => ['Cooper', 'Countryman', 'Clubman'],
            'Bentley' => ['Continental GT', 'Bentayga', 'Flying Spur'],
            'BYD' => ['Atto 3', 'Seal', 'Dolphin', 'Han', 'Song Plus'],
            'Geely' => ['Coolray', 'Emgrand', 'Azkarra', 'Okavango'],
            'Great Wall Motors' => ['Haval H6', 'Poer', 'Tank 300', 'Ora'],
            'Perodua' => ['Myvi', 'Axia', 'Bezza', 'Alza'],
            'Proton' => ['Saga', 'X50', 'X70', 'Persona'],
        ];

        foreach ($modelsByMake as $makeName => $models) {
            $make = Make::where('name', $makeName)->first();

            if (! $make) {
                continue;
            }

            foreach ($models as $modelName) {
                CarModel::updateOrCreate(
                    [
                        'make_id' => $make->id,
                        'name' => $modelName,
                    ]
                );
            }
        }
    }
}
