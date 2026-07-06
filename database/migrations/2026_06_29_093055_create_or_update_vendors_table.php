<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category')->default('others');
                $table->string('location')->nullable();
                $table->string('state')->nullable();
                $table->text('description')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('image_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};