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
        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->constrained('jobs_employment')->onDelete('cascade');
            // Add a composite primary key
            $table->primary(['user_id', 'job_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            // Drop foreign keys first if they were explicitly named.
            // Otherwise, Laravel handles it if column is dropped.
            // $table->dropForeign(['user_id']);
            // $table->dropForeign(['job_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('job_id');
            // Drop primary key
            $table->dropPrimary(['user_id', 'job_id']);
        });
    }
};
