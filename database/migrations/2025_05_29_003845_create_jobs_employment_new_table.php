<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsEmploymentNewTable extends Migration
{
    public function up()
    {
        Schema::create('jobs_employment', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('company_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('areas_of_interest')->onDelete('cascade');
            $table->foreignId('area_of_interest_id')->constrained('areas_of_interest')->onDelete('cascade');
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs_employment');
    }
}
