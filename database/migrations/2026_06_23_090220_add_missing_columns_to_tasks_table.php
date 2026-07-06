<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('tasks', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (!Schema::hasColumn('tasks', 'category')) {
                $table->string('category')->default('others')->after('description');
            }

            if (!Schema::hasColumn('tasks', 'priority')) {
                $table->string('priority')->default('medium')->after('category');
            }

            if (!Schema::hasColumn('tasks', 'deadline')) {
                $table->date('deadline')->nullable()->after('priority');
            }

            if (!Schema::hasColumn('tasks', 'status')) {
                $table->string('status')->default('pending')->after('deadline');
            }
        });

        if (Schema::hasColumn('tasks', 'status')) {
            DB::table('tasks')
                ->whereIn('status', ['upcoming', 'in progress'])
                ->update(['status' => 'pending']);
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('tasks', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};