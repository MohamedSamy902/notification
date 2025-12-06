<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_analytics', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id')->nullable(); // Link to advanced_notifications
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('channel');
            $table->string('event_type'); // sent, delivered, read, clicked, failed
            $table->nullableMorphs('user'); // user_id and user_type();
            $table->json('metadata')->nullable(); // IP, User Agent, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_analytics');
    }
};
