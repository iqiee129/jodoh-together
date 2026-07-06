<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->default('others');
                $table->decimal('amount', 10, 2)->default(0);
                $table->date('expense_date')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        } else {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                }

                if (!Schema::hasColumn('expenses', 'title')) {
                    $table->string('title')->after('user_id');
                }

                if (!Schema::hasColumn('expenses', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }

                if (!Schema::hasColumn('expenses', 'category')) {
                    $table->string('category')->default('others')->after('description');
                }

                if (!Schema::hasColumn('expenses', 'amount')) {
                    $table->decimal('amount', 10, 2)->default(0)->after('category');
                }

                if (!Schema::hasColumn('expenses', 'expense_date')) {
                    $table->date('expense_date')->nullable()->after('amount');
                }

                if (!Schema::hasColumn('expenses', 'status')) {
                    $table->string('status')->default('pending')->after('expense_date');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};