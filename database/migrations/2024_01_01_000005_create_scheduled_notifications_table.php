<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scheduled_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('recipient_type'); // User, Group, etc.
            $table->unsignedBigInteger('recipient_id');
            $table->string('channel')->nullable(); // If null, use all enabled
            $table->unsignedBigInteger('template_id');
            $table->json('data')->nullable(); // Dynamic data for template
            $table->timestamp('send_at');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'send_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
