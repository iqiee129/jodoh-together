<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_key');
            $table->date('sent_for_date');
            $table->timestamps();

            $table->unique(['user_id', 'notification_key', 'sent_for_date'], 'sent_notification_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_app_notifications');
    }
};