<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_otps', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_otps', 'attempts')) {
                $table->unsignedTinyInteger('attempts')->default(0)->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_otps', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_otps', 'attempts')) {
                $table->dropColumn('attempts');
            }
        });
    }
};
