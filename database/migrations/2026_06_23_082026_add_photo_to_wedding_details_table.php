<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_details', function (Blueprint $table) {
            if (!Schema::hasColumn('wedding_details', 'photo')) {
                $table->string('photo')->nullable()->after('total_budget');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wedding_details', function (Blueprint $table) {
            if (Schema::hasColumn('wedding_details', 'photo')) {
                $table->dropColumn('photo');
            }
        });
    }
};