<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Gabriel Antonio Picon Escalante',
            'email' => 'gpicone@miumg.edu.gt',
            'password' => Hash::make('Soytonypicon10%'), // cámbiala por la que quieras
            'role' => 'admin',
            'estado' => 'activo',
        ]);
    }
}
