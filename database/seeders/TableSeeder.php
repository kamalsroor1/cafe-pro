<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            RestaurantTable::create([
                'name' => 'T'.$i,
                'capacity' => rand(2, 6),
                'status' => 'available',
            ]);
        }
    }
}
