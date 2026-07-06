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
    Schema::create('wedding_details', function (Blueprint $table) {
        $table->id();
        // Links this detail directly to a specific user
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('partner_name')->nullable();
        $table->date('wedding_date')->nullable();
        $table->string('venue')->nullable();
        $table->string('theme')->nullable();
        $table->integer('estimated_guests')->nullable();
        $table->decimal('total_budget', 10, 2)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_details');
    }
};
