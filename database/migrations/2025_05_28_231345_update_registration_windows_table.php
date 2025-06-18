<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRegistrationWindowsTable extends Migration
{
    public function up()
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            // Remover colunas antigas that were added by 2025_05_26_152017_add_dates_to_registration_windows_table.php
            if (Schema::hasColumn('registration_windows', 'start_date') && Schema::hasColumn('registration_windows', 'end_date')) {
                $table->dropColumn(['start_date', 'end_date']);
            }
        });
    }

    public function down()
    {
        Schema::table('registration_windows', function (Blueprint $table) {
            // Re-add the columns if they don't exist, to revert the 'up' operation
            if (!Schema::hasColumn('registration_windows', 'start_date')) {
                $table->dateTime('start_date')->after('id')->nullable(); // Assuming it can be nullable or set a default
            }
            if (!Schema::hasColumn('registration_windows', 'end_date')) {
                $table->dateTime('end_date')->after('start_date')->nullable(); // Assuming it can be nullable or set a default
            }
        });
    }
}
