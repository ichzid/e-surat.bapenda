<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User default untuk testing
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Pegawai Surat',
            'username' => 'surat',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role' => 'user',
        ]);
    }
}
