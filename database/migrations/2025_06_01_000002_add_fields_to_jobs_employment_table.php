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
        Schema::table('jobs_employment', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('salary')->nullable(); // Using string to accommodate ranges or text
            $table->date('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs_employment', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->dropColumn('contract_type');
            $table->dropColumn('salary');
            $table->dropColumn('expiration_date');
        });
    }
};
