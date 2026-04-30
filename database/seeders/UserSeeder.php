<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fishpos.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cashier@fishpos.com'],
            [
                'name' => 'Cashier',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ]
        );
    }
}
