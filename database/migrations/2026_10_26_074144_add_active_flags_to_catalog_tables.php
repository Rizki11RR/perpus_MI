<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_categories', fn (Blueprint $table) => $table->boolean('is_active')->default(true)->after('name'));
        Schema::table('books', fn (Blueprint $table) => $table->boolean('is_active')->default(true)->after('description'));
        Schema::table('students', fn (Blueprint $table) => $table->boolean('is_active')->default(true)->after('status'));
        Schema::table('teachers', fn (Blueprint $table) => $table->boolean('is_active')->default(true)->after('status'));
    }

    public function down(): void
    {
        Schema::table('book_categories', fn (Blueprint $table) => $table->dropColumn('is_active'));
        Schema::table('teachers', fn (Blueprint $table) => $table->dropColumn('is_active'));
        Schema::table('students', fn (Blueprint $table) => $table->dropColumn('is_active'));
        Schema::table('books', fn (Blueprint $table) => $table->dropColumn('is_active'));
    }
};
