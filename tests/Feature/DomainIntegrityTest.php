<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class DomainIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_copy_belongs_to_book_and_barcode_is_unique(): void
    {
        $category = BookCategory::create(['name' => 'Fiksi']);
        $book = Book::create(['category_id' => $category->id, 'book_code' => 'BK-001', 'title' => 'Judul']);
        $copy = BookCopy::create(['book_id' => $book->id, 'barcode' => 'BC-001']);

        $this->assertTrue($copy->book->is($book));
        $this->expectException(QueryException::class);
        BookCopy::create(['book_id' => $book->id, 'barcode' => 'BC-001']);
    }

    public function test_student_number_is_unique(): void
    {
        Student::create(['student_number' => 'NIS-001', 'full_name' => 'Siswa Satu']);
        $this->expectException(QueryException::class);
        Student::create(['student_number' => 'NIS-001', 'full_name' => 'Siswa Dua']);
    }
}
