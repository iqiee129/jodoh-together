<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {

            if (!Schema::hasColumn('vendors', 'location')) {
                $table->string('location')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('vendors', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'image_url')) {
                $table->string('image_url')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'price',
                'phone',
                'email',
                'image_url',
                'description',
                'is_active'
            ]);
        });
    }
};
