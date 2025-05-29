<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegistrationWindowsTableAddColumns extends Migration
{
    public function up()
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_windows', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('id');
            }
            if (!Schema::hasColumn('registration_windows', 'start_time')) {
                $table->timestamp('start_time')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('registration_windows', 'end_time')) {
                $table->timestamp('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('registration_windows', 'max_registrations')) {
                $table->integer('max_registrations')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('registration_windows', 'password')) {
                $table->string('password')->nullable()->after('max_registrations');
            }
            if (!Schema::hasColumn('registration_windows', 'current_registrations')) {
                $table->integer('current_registrations')->default(0)->after('password');
            }
            if (!Schema::hasColumn('registration_windows', 'first_use_time')) {
                $table->timestamp('first_use_time')->nullable()->after('current_registrations');
            }
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
                'current_registrations',
                'first_use_time',
            ]);
        });
    }
}
