<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'google_event_id')) {
                    $table->string('google_event_id')->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('wedding_details')) {
            Schema::table('wedding_details', function (Blueprint $table) {
                if (!Schema::hasColumn('wedding_details', 'google_event_id')) {
                    $table->string('google_event_id')->nullable()->after('wedding_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (Schema::hasColumn('tasks', 'google_event_id')) {
                    $table->dropColumn('google_event_id');
                }
            });
        }

        if (Schema::hasTable('wedding_details')) {
            Schema::table('wedding_details', function (Blueprint $table) {
                if (Schema::hasColumn('wedding_details', 'google_event_id')) {
                    $table->dropColumn('google_event_id');
                }
            });
        }
    }
};