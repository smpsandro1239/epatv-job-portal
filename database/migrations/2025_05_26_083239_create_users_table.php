<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['superadmin', 'admin', 'student']);
            $table->string('phone')->nullable();
            $table->integer('course_completion_year')->nullable();
            $table->string('photo')->nullable();
            $table->string('cv')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_description')->nullable();
            $table->string('company_logo')->nullable();
            $table->enum('registration_status', ['approved', 'pending'])->default('approved');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
