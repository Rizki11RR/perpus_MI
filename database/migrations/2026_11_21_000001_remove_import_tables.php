<?php
/**
 * Tujuan: menghapus tabel staging yang hanya dipakai fitur import yang dicabut.
 * Caller: Laravel migration runner pada deployment existing.
 * Dependensi: tabel import_batches dan import_batch_rows bila masih ada.
 * Public utama: up() dan down().
 * Side effect: menghapus metadata dan baris staging import; tidak menyentuh data master.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('import_batch_rows');
        Schema::dropIfExists('import_batches');
    }

    public function down(): void
    {
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
    }
};
