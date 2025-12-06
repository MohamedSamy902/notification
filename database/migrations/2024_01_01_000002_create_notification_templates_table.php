<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'welcome_email', 'order_shipped'
            $table->string('name'); // Human readable name
            $table->json('title'); // Translatable
            $table->json('body'); // Translatable
            $table->string('type')->default('info'); // info, success, warning, error
            $table->string('icon')->nullable();
            $table->string('action_url')->nullable();
            $table->json('metadata')->nullable(); // Extra config
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
};
