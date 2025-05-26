<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('area_of_interest_id')->constrained('areas_of_interest')->after('description');
            $table->foreignId('posted_by')->constrained('users')->after('area_of_interest_id');
            $table->string('status')->default('open')->after('posted_by');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['area_of_interest_id']);
            $table->dropForeign(['posted_by']);
            $table->dropColumn(['area_of_interest_id', 'posted_by', 'status']);
        });
    }
};
