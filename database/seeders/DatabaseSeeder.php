<?php

namespace Database\Seeders;

use App\Models\AreaOfInterest;
use App\Models\RegistrationWindow;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        if (!User::where('email', 'test@example.com')->exists()) {
            $adminUser = User::create([
                'name' => 'Test Admin',
                'email' => 'test@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'role' => 'admin',
                'registration_status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminUser = User::where('email', 'test@example.com')->first();
        }

        // Superadmin User
        if (!User::where('email', 'admin@epatv.pt')->exists()) {
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'admin@epatv.pt',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'registration_status' => 'approved',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Employer User (for company_id)
        if (!User::where('email', 'employer@example.com')->exists()) {
            $employer = User::create([
                'name' => 'Test Employer',
                'email' => 'employer@example.com',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'registration_status' => 'approved',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $employer = User::where('email', 'employer@example.com')->first();
        }

        // Areas of Interest
        $areas = [
            ['name' => 'Programação Informática', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cabeleireiro', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Animação Sociocultural', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gestão', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mecatrónica', 'created_at' => now(), 'updated_at' => now()],
        ];
        if (!AreaOfInterest::count()) {
            DB::table('areas_of_interest')->insert($areas);
        }

        // Registration Window
        if (!RegistrationWindow::count()) {
            RegistrationWindow::create([
                'is_active' => true,
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(30),
                'max_registrations' => 30,
                'password' => Hash::make('test123'),
                'current_registrations' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Jobs
        $area = AreaOfInterest::first();
        if ($area && !Job::count()) {
            Job::create([
                'title' => 'Desenvolvedor Web',
                'description' => 'Vaga para programador web.',
                'company_id' => $employer->id,
                'category_id' => $area->id,
                'area_of_interest_id' => $area->id,
                'posted_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Job::create([
                'title' => 'Técnico de Mecatrónica',
                'description' => 'Vaga para técnico de mecatrónica.',
                'company_id' => $employer->id,
                'category_id' => $area->id,
                'area_of_interest_id' => $area->id,
                'posted_by' => $adminUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
