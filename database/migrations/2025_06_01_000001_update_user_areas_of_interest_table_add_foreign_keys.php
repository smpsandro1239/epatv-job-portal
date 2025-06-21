<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_areas_of_interest', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('area_of_interest_id')->constrained('areas_of_interest')->onDelete('cascade');
            $table->unique(['user_id', 'area_of_interest_id'], 'user_areas_of_interest_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_areas_of_interest', function (Blueprint $table) {
            $table->dropUnique('user_areas_of_interest_unique');
            $table->dropForeign(['user_id']);
            $table->dropForeign(['area_of_interest_id']);
        });
    }
};
