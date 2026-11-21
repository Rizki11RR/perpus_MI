<?php
/**
 * Tujuan: memverifikasi import/export CSV katalog buku dan siswa.
 * Caller: PHPUnit feature suite.
 * Dependensi: RefreshDatabase, Livewire, UploadedFile, route/controller/service CSV.
 * Public utama: test import buku, import siswa, dan export kedua master data.
 * Side effect: membuat data uji di database testing dan membaca respons file CSV.
 */

namespace Tests\Feature;

use App\Livewire\Books\BookIndex;
use App\Livewire\Members\StudentIndex;
use App\Models\BookCategory;
use App\Models\Book;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class MasterDataCsvTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_csv_can_be_imported_from_catalog_page(): void
    {
        $user = User::factory()->create();
        BookCategory::create(['name' => 'Fiksi']);
        $file = UploadedFile::fake()->createWithContent('buku.csv', "Kode Buku,Judul,Kategori,Penulis\nBK-100,Buku Uji,Fiksi,Penulis Uji\n");

        Livewire::actingAs($user)
            ->test(BookIndex::class)
            ->set('importFile', $file)
            ->call('importCsv')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('books', ['book_code' => 'BK-100', 'title' => 'Buku Uji']);
    }

    public function test_student_csv_can_be_imported_from_student_page(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('siswa.csv', "NIS,Nama Lengkap,Status\nS-100,Siswa Uji,active\n");

        Livewire::actingAs($user)
            ->test(StudentIndex::class)
            ->set('importFile', $file)
            ->call('importCsv')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('students', ['student_number' => 'S-100', 'full_name' => 'Siswa Uji']);
    }

    public function test_book_and_student_csv_exports_are_available_to_authenticated_users(): void
    {
        $user = User::factory()->create();
        BookCategory::create(['name' => 'Fiksi', 'description' => 'Cerita']);
        Book::create(['category_id' => BookCategory::first()->id, 'book_code' => 'BK-101', 'title' => 'Buku Export', 'is_active' => true]);
        Student::create(['student_number' => 'S-101', 'full_name' => 'Siswa Export', 'status' => 'active', 'is_active' => true]);

        $bookResponse = $this->actingAs($user)->get(route('catalog.books.export'));
        $studentResponse = $this->actingAs($user)->get(route('data.students.export'));

        $bookResponse->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $studentResponse->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Buku Export', $bookResponse->streamedContent());
        $this->assertStringContainsString('Siswa Export', $studentResponse->streamedContent());
    }
}
