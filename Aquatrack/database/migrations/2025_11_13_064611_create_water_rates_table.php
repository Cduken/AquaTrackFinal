<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('water_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Tier 1", "Tier 2", etc.
            $table->integer('min_consumption'); // Minimum consumption for this tier
            $table->integer('max_consumption')->nullable(); // Maximum consumption for this tier (null for unlimited)
            $table->decimal('rate_per_cubic', 10, 2); // Rate per cubic meter
            $table->decimal('fixed_charge', 10, 2)->default(0); // Fixed charge for this tier
            $table->integer('order')->default(0); // Order of application
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_rates');
    }
};
