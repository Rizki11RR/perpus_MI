<?php

namespace Tests\Feature;

use App\Models\BookCategory;
use App\Models\ImportBatch;
use App\Models\User;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_csv_preview_and_commit(): void
    {
        $user = User::factory()->create(); BookCategory::create(['name'=>'Fiksi']);
        $batch = app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('books.csv', "book_code,title,category\nBK-1,Buku Satu,Fiksi\n"), 'books', $user->id);
        $this->assertSame(1, $batch->valid_rows); app(ImportService::class)->commit($batch, $user->id); $this->assertDatabaseHas('books', ['book_code'=>'BK-1']);
    }

    public function test_duplicate_rows_are_invalid(): void
    {
        $user = User::factory()->create(); BookCategory::create(['name'=>'Fiksi']);
        $batch = app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('books.csv', "book_code,title,category\nBK-1,Satu,Fiksi\nBK-1,Dua,Fiksi\n"), 'books', $user->id);
        $this->assertSame(1, $batch->valid_rows); $this->assertSame(1, $batch->invalid_rows);
    }

    public function test_invalid_import_row_is_staged_with_errors(): void
    {
        $user = User::factory()->create(); $batch = app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('students.csv', "student_number,full_name\n,Siswa\n"), 'students', $user->id);
        $this->assertSame(1, $batch->invalid_rows); $this->assertFalse($batch->rows->first()->is_valid);
    }

    public function test_csv_header_is_class_only_and_bom_is_supported(): void
    {
        $user = User::factory()->create();
        $batch = app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('students.csv', "\xEF\xBB\xBFstudent_number,full_name\nS-1,Siswa\n"), 'students', $user->id);
        $this->assertSame(1, $batch->valid_rows);
    }

    public function test_duplicate_headers_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(ImportService::class)->preview(UploadedFile::fake()->createWithContent('students.csv', "student_number,full_name,full_name\nS-1,Siswa,Siswa\n"), 'students', $user->id);
    }
}
