<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'kamal@admin.com'],
            [
                'name'     => 'Kamal Sroor',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');
    }
}
