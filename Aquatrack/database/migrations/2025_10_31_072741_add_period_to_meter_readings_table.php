<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->string('period')->nullable()->after('reading_date'); // Format: YYYY-MM for monthly, YYYY for yearly
            $table->string('period_type')->default('monthly')->after('period'); // 'monthly' or 'yearly'
        });
    }

    public function down()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropColumn(['period', 'period_type']);
        });
    }
};
