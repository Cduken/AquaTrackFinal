<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // In your migration file
        Schema::create('dismissed_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_id');
            $table->string('type');
            $table->timestamp('dismissed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'notification_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dismissed_notifications');
    }
};
