<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            $table->dateTime('start_date')->after('id');
            $table->dateTime('end_date')->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
