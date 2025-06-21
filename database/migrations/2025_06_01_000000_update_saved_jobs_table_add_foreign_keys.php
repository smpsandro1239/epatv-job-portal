<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'job_id'], 'saved_jobs_user_id_job_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
            $table->dropUnique('saved_jobs_user_id_job_id_unique');
            $table->dropForeign(['user_id']);
            $table->dropForeign(['job_id']);
        });
    }
};
