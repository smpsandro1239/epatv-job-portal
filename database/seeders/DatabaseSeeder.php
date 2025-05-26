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
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'registration_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Superadmin User
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

        // Employer User (for company_id)
        $employer = User::factory()->create([
            'name' => 'Test Employer',
            'email' => 'employer@example.com',
            'password' => Hash::make('password'),
            'role' => 'employer',
            'registration_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        // Areas of Interest
        $areas = [
            ['name' => 'Programação Informática'],
            ['name' => 'Cabeleireiro'],
            ['name' => 'Animação Sociocultural'],
            ['name' => 'Gestão'],
            ['name' => 'Mecatrónica'],
        ];
        DB::table('areas_of_interest')->insert($areas);

        // Registration Window
        RegistrationWindow::factory()->create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ]);

        // Jobs
        $area = AreaOfInterest::first();
        if ($area) {
            Job::factory()->count(3)->create([
                'company_id' => $employer->id,
                'category_id' => $area->id,
                'area_of_interest_id' => $area->id,
                'posted_by' => $admin->id,
            ]);
        }
    }
}
