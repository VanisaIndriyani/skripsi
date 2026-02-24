<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'position' => 'HR Manager',
            'base_salary' => 0,
        ]);

        // Employee 1
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@pms.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Staff IT',
            'base_salary' => 5000000,
        ]);

        // Employee 2
        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@pms.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'position' => 'Staff Finance',
            'base_salary' => 4500000,
        ]);
    }
}
