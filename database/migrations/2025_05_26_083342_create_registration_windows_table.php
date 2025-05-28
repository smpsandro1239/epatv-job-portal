<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationWindowsTable extends Migration
{
    public function up()
    {
        Schema::create('registration_windows', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('max_registrations')->default(30);
            $table->string('password')->nullable();
            $table->dateTime('first_use_time')->nullable();
            $table->integer('current_registrations')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registration_windows');
    }
}
