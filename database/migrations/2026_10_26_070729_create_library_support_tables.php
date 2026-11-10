<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('school_name');
            $table->string('library_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('accreditation')->nullable();
            $table->timestamps();
        });

        Schema::create('library_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('max_books_per_borrower')->default(3);
            $table->unsignedSmallInteger('loan_duration_days')->default(7);
            $table->decimal('daily_fine', 12, 2)->default(0);
            $table->unsignedSmallInteger('grace_period_days')->default(0);
            $table->string('timezone', 64)->default('Asia/Jakarta');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('module', 50);
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->index(['module', 'action']);
        });

        Schema::create('import_batches', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 30);
            $table->string('file_name');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('preview');
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('invalid_rows')->default(0);
            $table->timestamps();
        });

        Schema::create('import_batch_rows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('payload');
            $table->json('errors')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->timestamps();
            $table->unique(['import_batch_id', 'row_number']);
        });

        Schema::create('backup_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('file_name');
            $table->string('path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('type', 30);
            $table->string('status', 20);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('import_batch_rows');
        Schema::dropIfExists('import_batches');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('library_settings');
        Schema::dropIfExists('library_profiles');
    }
};
