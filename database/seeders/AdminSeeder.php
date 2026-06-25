<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], // Condition para walang duplicate
             [
                'first_name'   => 'System',
                'middle_name'  => 'Super',
                'last_name'    => 'Administrator',
                'email'        => 'admin@gmail.com',
                'password'     => Hash::make('admin123'),
                'phone_number' => '09123456789', // ✅ lagay dummy phone number dito
                'image'        => 'images/default-admin.png',
                'role'         => 'admin',
            ]
        );
    }
}
