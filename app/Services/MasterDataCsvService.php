<?php
/**
 * Tujuan: memvalidasi import dan menghasilkan export CSV untuk katalog buku/siswa.
 * Caller: komponen Livewire BookIndex/StudentIndex dan controller export.
 * Dependensi: model Book, BookCategory, Student, LibraryClass, database transaction.
 * Public utama: importBooks(), importStudents(), exportBooks(), exportStudents().
 * Side effect: membaca upload, menulis batch data, dan mengirim stream HTTP CSV.
 */

namespace App\Services;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\LibraryClass;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterDataCsvService
{
    public function importBooks(UploadedFile $file): int
    {
        $rows = $this->readRows($file, [
            'book_code' => ['book_code', 'kode buku', 'kode'],
            'title' => ['title', 'judul'],
            'category' => ['category', 'kategori'],
            'isbn' => ['isbn'],
            'author' => ['author', 'penulis'],
            'publisher' => ['publisher', 'penerbit'],
            'publication_year' => ['publication_year', 'tahun terbit', 'tahun'],
            'description' => ['description', 'deskripsi'],
        ], ['book_code', 'title', 'category']);

        $codes = [];
        $categoryNames = [];
        foreach ($rows as $index => $row) {
            $code = trim((string) $row['book_code']);
            $title = trim((string) $row['title']);
            $category = trim((string) $row['category']);
            if ($code === '' || $title === '' || $category === '') {
                $this->invalidRow($index + 2, 'Kode buku, judul, dan kategori wajib diisi.');
            }
            if (isset($codes[$code])) {
                $this->invalidRow($index + 2, 'Kode buku duplikat dalam file.');
            }
            $codes[$code] = true;
            $categoryNames[$category] = true;
            $year = trim((string) ($row['publication_year'] ?? ''));
            if ($year !== '' && (! ctype_digit($year) || (int) $year < 1000 || (int) $year > 2100)) {
                $this->invalidRow($index + 2, 'Tahun terbit harus berupa tahun yang valid.');
            }
            $rows[$index] = compact('code', 'title', 'category', 'year') + [
                'isbn' => trim((string) ($row['isbn'] ?? '')) ?: null,
                'author' => trim((string) ($row['author'] ?? '')) ?: null,
                'publisher' => trim((string) ($row['publisher'] ?? '')) ?: null,
                'description' => trim((string) ($row['description'] ?? '')) ?: null,
            ];
        }

        $categories = BookCategory::query()->whereIn('name', array_keys($categoryNames))->pluck('id', 'name');
        foreach ($categoryNames as $name => $_) {
            if (! isset($categories[$name])) {
                $this->invalidRow(1, "Kategori '{$name}' tidak ditemukan.");
            }
        }
        $existing = Book::query()->whereIn('book_code', array_keys($codes))->pluck('book_code');
        if ($existing->isNotEmpty()) {
            $this->invalidRow(1, 'Kode buku sudah ada: '.$existing->implode(', ').'.');
        }

        $now = now();
        $payload = array_map(fn (array $row): array => [
            'category_id' => $categories[$row['category']],
            'book_code' => $row['code'],
            'title' => $row['title'],
            'isbn' => $row['isbn'],
            'author' => $row['author'],
            'publisher' => $row['publisher'],
            'publication_year' => $row['year'] !== '' ? (int) $row['year'] : null,
            'description' => $row['description'],
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows);
        DB::transaction(fn (): int => Book::insert($payload) ? count($payload) : 0);
        return count($payload);
    }

    public function importStudents(UploadedFile $file): int
    {
        $rows = $this->readRows($file, [
            'student_number' => ['student_number', 'nis', 'nomor siswa'],
            'full_name' => ['full_name', 'nama', 'nama lengkap'],
            'class' => ['class', 'kelas'],
            'status' => ['status'],
        ], ['student_number', 'full_name']);
        $numbers = [];
        $classNames = [];
        foreach ($rows as $index => $row) {
            $number = trim((string) $row['student_number']);
            $fullName = trim((string) $row['full_name']);
            $className = trim((string) ($row['class'] ?? ''));
            $status = strtolower(trim((string) ($row['status'] ?? ''))) ?: 'active';
            if ($number === '' || $fullName === '') $this->invalidRow($index + 2, 'NIS dan nama lengkap wajib diisi.');
            if (isset($numbers[$number])) $this->invalidRow($index + 2, 'NIS duplikat dalam file.');
            if (! in_array($status, ['active', 'inactive', 'graduated'], true)) $this->invalidRow($index + 2, 'Status tidak valid.');
            $numbers[$number] = true;
            if ($className !== '') $classNames[$className] = true;
            $rows[$index] = compact('number', 'fullName', 'className', 'status');
        }
        $existing = Student::query()->whereIn('student_number', array_keys($numbers))->pluck('student_number');
        if ($existing->isNotEmpty()) $this->invalidRow(1, 'NIS sudah ada: '.$existing->implode(', ').'.');
        $classes = LibraryClass::query()->whereIn('name', array_keys($classNames))->get(['id', 'name'])->groupBy('name');
        foreach ($classNames as $name => $_) {
            if (! isset($classes[$name])) $this->invalidRow(1, "Kelas '{$name}' tidak ditemukan.");
            if ($classes[$name]->count() > 1) $this->invalidRow(1, "Kelas '{$name}' memiliki tahun ajaran ganda.");
        }
        $now = now();
        $payload = array_map(fn (array $row): array => [
            'student_number' => $row['number'], 'full_name' => $row['fullName'],
            'class_id' => $row['className'] !== '' ? $classes[$row['className']]->first()->id : null,
            'status' => $row['status'], 'is_active' => $row['status'] !== 'inactive',
            'created_at' => $now, 'updated_at' => $now,
        ], $rows);
        DB::transaction(fn (): int => Student::insert($payload) ? count($payload) : 0);
        return count($payload);
    }

    public function exportBooks(): StreamedResponse
    {
        return $this->stream('katalog-buku', ['Kode Buku', 'Judul', 'Kategori', 'ISBN', 'Penulis', 'Penerbit', 'Tahun Terbit', 'Deskripsi'], function ($handle): void {
            Book::query()->with('category')->orderBy('id')->chunkById(500, function ($books) use ($handle): void {
                foreach ($books as $book) fputcsv($handle, [$book->book_code, $book->title, $book->category?->name, $book->isbn, $book->author, $book->publisher, $book->publication_year, $book->description]);
            });
        });
    }

    public function exportStudents(): StreamedResponse
    {
        return $this->stream('data-siswa', ['NIS', 'Nama Lengkap', 'Kelas', 'Status'], function ($handle): void {
            Student::query()->with('classRoom')->orderBy('id')->chunkById(500, function ($students) use ($handle): void {
                foreach ($students as $student) fputcsv($handle, [$student->student_number, $student->full_name, $student->classRoom?->name, $student->status]);
            });
        });
    }

    private function readRows(UploadedFile $file, array $aliases, array $required): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) throw ValidationException::withMessages(['importFile' => 'File CSV tidak dapat dibaca.']);
        try {
            $rawHeaders = fgetcsv($handle);
            if ($rawHeaders === false) throw ValidationException::withMessages(['importFile' => 'File CSV kosong.']);
            $map = [];
            foreach ($rawHeaders as $header) {
                $normalized = $this->normalize((string) $header);
                $canonical = collect($aliases)->search(fn (array $accepted): bool => in_array($normalized, $accepted, true));
                if ($canonical === false || isset($map[$canonical])) throw ValidationException::withMessages(['importFile' => 'Header CSV tidak valid atau duplikat.']);
                $map[$canonical] = count($map);
            }
            foreach ($required as $field) if (! array_key_exists($field, $map)) throw ValidationException::withMessages(['importFile' => "Kolom {$field} wajib ada."]);
            $rows = [];
            while (($values = fgetcsv($handle)) !== false) {
                if (count($values) === 1 && trim((string) $values[0]) === '') continue;
                if (count($values) !== count($rawHeaders)) $this->invalidRow(count($rows) + 2, 'Jumlah kolom tidak sesuai header.');
                $row = [];
                foreach ($map as $field => $position) $row[$field] = trim((string) ($values[$position] ?? ''));
                $rows[] = $row;
            }
            if ($rows === []) throw ValidationException::withMessages(['importFile' => 'File CSV tidak memiliki data.']);
            return $rows;
        } finally {
            fclose($handle);
        }
    }

    private function stream(string $name, array $headers, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $writer): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            $writer($handle);
            fclose($handle);
        }, $name.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function normalize(string $value): string { return strtolower(trim($value, " \t\r\n\xEF\xBB\xBF")); }
    private function invalidRow(int $row, string $message): never { throw ValidationException::withMessages(['importFile' => "Baris {$row}: {$message}"]); }
}
