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
        Schema::table('registration_windows', function (Blueprint $table) {
            $table->integer('password_valid_duration_hours')->nullable()->default(2)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            $table->dropColumn('password_valid_duration_hours');
        });
    }
};
