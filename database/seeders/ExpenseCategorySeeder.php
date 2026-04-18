<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rent', 'description' => 'Monthly property rent'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, internet'],
            ['name' => 'Payroll', 'description' => 'Employee salaries and wages'],
            ['name' => 'Supplies', 'description' => 'Cleaning and cafe supplies non-inventory'],
            ['name' => 'Maintenance', 'description' => 'Equipment repair and upkeep'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotions'],
            ['name' => 'Other', 'description' => 'Miscellaneous expenses'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
