<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        // Adds the columns needed by the dashboard if they don't exist yet
        $table->boolean('is_completed')->default(false)->after('title');
        $table->boolean('is_important')->default(false)->after('is_completed');
        $table->date('due_date')->nullable()->after('is_important');
    });
}

public function down(): void
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['is_completed', 'is_important', 'due_date']);
    });
}
};
