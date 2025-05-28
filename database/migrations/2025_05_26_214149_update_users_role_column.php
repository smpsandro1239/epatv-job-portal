<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->integer('course_completion_year')->nullable()->after('phone');
            $table->string('company_name')->nullable()->after('course_completion_year');
            $table->string('company_city')->nullable()->after('company_name');
            $table->string('company_website')->nullable()->after('company_city');
            $table->text('company_description')->nullable()->after('company_website');
            $table->string('photo')->nullable()->after('company_description');
            $table->string('cv')->nullable()->after('photo');
            $table->string('company_logo')->nullable()->after('cv');
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
