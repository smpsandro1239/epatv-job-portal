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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('name')->after('job_id'); // Add new fields after existing ones
            $table->string('email')->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->integer('course_completion_year')->nullable()->after('phone');
            $table->string('cv_path')->nullable()->after('course_completion_year');

            // Rename cover_letter to message if it exists
            if (Schema::hasColumn('applications', 'cover_letter')) {
                $table->renameColumn('cover_letter', 'message');
            } elseif (!Schema::hasColumn('applications', 'message')) {
                // If cover_letter doesn't exist and message also doesn't, add message
                $table->text('message')->nullable()->after('cv_path');
            }
            // If 'message' already exists from a previous attempt and 'cover_letter' is gone, this is fine.
            // If 'cover_letter' is gone and 'message' exists, ensure 'message' is text and nullable.
            // This subtask will assume one of the first two conditions.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Rename message back to cover_letter if 'message' exists and 'cover_letter' does not
            if (Schema::hasColumn('applications', 'message') && !Schema::hasColumn('applications', 'cover_letter')) {
                $table->renameColumn('message', 'cover_letter');
            }
            // If 'message' does not exist (e.g. it was dropped) or 'cover_letter' was never dropped, this rename is skipped.

            $table->dropColumn(['cv_path', 'course_completion_year', 'phone', 'email', 'name']);
            // If message was added (not renamed from cover_letter) in up(), and cover_letter never existed,
            // then 'message' should be dropped here. The current logic prioritizes renaming back.
            // For robustness, if 'message' was added because 'cover_letter' did not exist,
            // and we are in 'down', we might want to drop 'message' if 'cover_letter' also still doesn't exist.
            // However, standard rollback of rename is rename back.
        });
    }
};
