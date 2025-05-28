<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegistrationWindowsTable extends Migration
{
    public function up()
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            // Remover colunas antigas
            $table->dropColumn(['start_date', 'end_date']);
            // Adicionar colunas corretas
            $table->boolean('is_active')->default(false)->after('id');
            $table->dateTime('start_time')->nullable()->after('is_active');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->integer('max_registrations')->default(30)->after('end_time');
            $table->string('password')->nullable()->after('max_registrations');
            $table->dateTime('first_use_time')->nullable()->after('password');
            $table->integer('current_registrations')->default(0)->after('first_use_time');
        });
    }

    public function down()
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'start_time',
                'end_time',
                'max_registrations',
                'password',
                'first_use_time',
                'current_registrations',
            ]);
            $table->dateTime('start_date')->after('id');
            $table->dateTime('end_date')->after('start_date');
        });
    }
}
