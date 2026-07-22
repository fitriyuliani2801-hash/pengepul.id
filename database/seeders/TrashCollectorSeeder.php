<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrashCollectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Katalog Harga Sampah
        \Illuminate\Support\Facades\DB::table('sampah_katalog')->insert([
            ['nama_material' => 'Kardus Bekas', 'harga_beli_per_kg' => 3000, 'icon' => '📦', 'created_at' => now(), 'updated_at' => now()],
            ['nama_material' => 'Botol Plastik PET', 'harga_beli_per_kg' => 4500, 'icon' => '🧴', 'created_at' => now(), 'updated_at' => now()],
            ['nama_material' => 'Minyak Jelantah', 'harga_beli_per_kg' => 7000, 'icon' => '🛢️', 'created_at' => now(), 'updated_at' => now()],
            ['nama_material' => 'Logam / Besi', 'harga_beli_per_kg' => 12000, 'icon' => '🔩', 'created_at' => now(), 'updated_at' => now()],
            ['nama_material' => 'Kertas Koran/HVS', 'harga_beli_per_kg' => 2000, 'icon' => '📰', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Seed User Driver
        \App\Models\User::create([
            'name' => 'Sopir Budi',
            'email' => 'driver1@gmail.com',
            'status' => 'active',
            'role' => 'driver',
            'password' => \Illuminate\Support\Facades\Hash::make('driver')
        ]);

        \App\Models\User::create([
            'name' => 'Sopir Agus',
            'email' => 'driver2@gmail.com',
            'status' => 'active',
            'role' => 'driver',
            'password' => \Illuminate\Support\Facades\Hash::make('driver')
        ]);
    }
}
