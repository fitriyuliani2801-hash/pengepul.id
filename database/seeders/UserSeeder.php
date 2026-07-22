<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create(['name' => 'admin', 'email' => 'admin@gmail.com', 'status' => 'active', 'role' => 'admin', 'password' => Hash::make('admin')]);
        User::create(['name' => 'staff', 'email' => 'staff@gmail.com', 'status' => 'active', 'role' => 'staff', 'password' => Hash::make('staff')]);
        User::create(['name' => 'customer', 'email' => 'customer@gmail.com', 'status' => 'active', 'role' => 'customer', 'password' => Hash::make('customer')]);
    }
}