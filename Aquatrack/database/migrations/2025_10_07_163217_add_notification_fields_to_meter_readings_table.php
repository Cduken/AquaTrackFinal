<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In the migration
    public function up()
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->boolean('amount_updated')->default(false);
            $table->boolean('viewed_by_staff')->default(false);
            $table->timestamp('staff_viewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            //
        });
    }
};
