<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('staff_number')->unique();
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('grade_level')->nullable();
            $table->string('academic_year', 9);
            $table->timestamps();
            $table->unique(['name', 'academic_year']);
        });

        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_number')->unique();
            $table->string('full_name');
            $table->string('gender', 20)->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('teachers', function (Blueprint $table): void {
            $table->id();
            $table->string('teacher_number')->nullable()->unique();
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('book_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('book_categories')->restrictOnDelete();
            $table->string('book_code')->unique();
            $table->string('isbn')->nullable()->index();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('book_copies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->string('barcode')->unique();
            $table->date('acquisition_date')->nullable();
            $table->string('shelf_location')->nullable();
            $table->string('condition', 20)->default('good');
            $table->string('status', 20)->default('available');
            $table->timestamps();
            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('staff');
    }
};
