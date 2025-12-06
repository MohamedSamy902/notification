<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('channel'); // email, fcm, database, sms
            $table->boolean('is_enabled')->default(true);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'channel']);
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // We assume 'users' table exists, but we won't enforce FK to allow flexibility
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
