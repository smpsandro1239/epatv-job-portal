<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'course_completion_year')) {
                $table->integer('course_completion_year')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('course_completion_year');
            }
            if (!Schema::hasColumn('users', 'company_city')) {
                $table->string('company_city')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('users', 'company_website')) {
                $table->string('company_website')->nullable()->after('company_city');
            }
            if (!Schema::hasColumn('users', 'company_description')) {
                $table->text('company_description')->nullable()->after('company_website');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('company_description');
            }
            if (!Schema::hasColumn('users', 'cv')) {
                $table->string('cv')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('users', 'company_logo')) {
                $table->string('company_logo')->nullable()->after('cv');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'course_completion_year',
                'company_name',
                'company_city',
                'company_website',
                'company_description',
                'photo',
                'cv',
                'company_logo',
            ]);
        });
    }
}
