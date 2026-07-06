<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sent_app_notifications')) {
            Schema::create('sent_app_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('notification_key');
                $table->date('sent_for_date');
                $table->timestamps();

                $table->unique(
                    ['user_id', 'notification_key', 'sent_for_date'],
                    'sent_app_notification_unique'
                );

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });

            return;
        }

        Schema::table('sent_app_notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('sent_app_notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('sent_app_notifications', 'notification_key')) {
                $table->string('notification_key')->nullable()->after('user_id');
            }

            if (! Schema::hasColumn('sent_app_notifications', 'sent_for_date')) {
                $table->date('sent_for_date')->nullable()->after('notification_key');
            }
        });

        try {
            DB::statement(
                'ALTER TABLE sent_app_notifications 
                 ADD UNIQUE sent_app_notification_unique 
                 (user_id, notification_key, sent_for_date)'
            );
        } catch (\Throwable $e) {
            // Ignore if unique key already exists.
        }

        try {
            DB::statement(
                'ALTER TABLE sent_app_notifications 
                 ADD CONSTRAINT sent_app_notifications_user_id_foreign 
                 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE'
            );
        } catch (\Throwable $e) {
            // Ignore if foreign key already exists.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_app_notifications');
    }
};