<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_areas_of_interest', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('area_of_interest_id')->constrained('areas_of_interest')->onDelete('cascade');
            // Add a composite primary key
            $table->primary(['user_id', 'area_of_interest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_areas_of_interest', function (Blueprint $table) {
            // $table->dropForeign(['user_id']);
            // $table->dropForeign(['area_of_interest_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('area_of_interest_id');
            // Drop primary key
            $table->dropPrimary(['user_id', 'area_of_interest_id']);
        });
    }
};
