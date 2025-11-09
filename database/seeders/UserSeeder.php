<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'username' => 'admin',
            'email' => 'admin@marhas.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'loket_id' => null,
            'status' => 'aktif'
        ]);

        // Operator untuk setiap loket
        User::create([
            'name' => 'Petugas Loket 1',
            'username' => 'petugas1',
            'email' => 'petugas1@marhas.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'loket_id' => 1,
            'status' => 'aktif'
        ]);

        User::create([
            'name' => 'Petugas Loket 2',
            'username' => 'petugas2',
            'email' => 'petugas2@marhas.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'loket_id' => 2,
            'status' => 'aktif'
        ]);

        User::create([
            'name' => 'Petugas Loket 3',
            'username' => 'petugas3',
            'email' => 'petugas3@marhas.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'loket_id' => 3,
            'status' => 'aktif'
        ]);

        User::create([
            'name' => 'Petugas Loket 4',
            'username' => 'petugas4',
            'email' => 'petugas4@marhas.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'loket_id' => 4,
            'status' => 'aktif'
        ]);
    }
}
