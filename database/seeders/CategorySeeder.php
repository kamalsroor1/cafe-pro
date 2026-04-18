<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Hot Drinks',
            'Cold Drinks',
            'Desserts',
            'Food'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate([
                'name' => $cat,
                'slug' => Str::slug($cat)
            ]);
        }
    }
}
