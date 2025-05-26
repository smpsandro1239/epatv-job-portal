<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('areas_of_interest')->insert([
            ['name' => 'Programação Informática'],
            ['name' => 'Cabeleireiro'],
            ['name' => 'Animação Sociocultural'],
            ['name' => 'Gestão'],
            ['name' => 'Mecatrónica'],
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@epatv.pt',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'registration_status' => 'approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
