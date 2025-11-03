<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->string('action_url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->boolean('important')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
