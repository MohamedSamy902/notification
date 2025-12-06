<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fcm_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // The actual topic name used in FCM
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('fcm_topic_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('fcm_topics')->onDelete('cascade');
            $table->nullableMorphs('subscriber'); // To link to User or any other model
            $table->string('token')->index();
            $table->timestamps();

            $table->unique(['topic_id', 'token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_topic_subscriptions');
        Schema::dropIfExists('fcm_topics');
    }
};
