<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table): void {
            $table->dropUnique(['name', 'academic_year']);
            $table->unique('name');
            $table->dropColumn(['grade_level', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table): void {
            $table->string('grade_level')->nullable();
            $table->string('academic_year', 9)->default('');
            $table->dropUnique(['name']);
            $table->unique(['name', 'academic_year']);
        });
    }
};
