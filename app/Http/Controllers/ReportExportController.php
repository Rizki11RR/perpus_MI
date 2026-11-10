<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Teacher;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    public function csv(string $type): Response
    {
        abort_unless(in_array($type, ['loans', 'books', 'students', 'teachers'], true), 404);
        $rows = match ($type) {
            'books' => Book::with('category')->where('is_active', true)->get()->map(fn ($book) => [$book->book_code, $book->title, $book->category->name]),
            'students' => Student::with('class')->get()->map(fn ($x) => [$x->student_number, $x->full_name, $x->class?->name, $x->status]),
            'teachers' => Teacher::get()->map(fn ($x) => [$x->teacher_number, $x->full_name, $x->position, $x->status]),
            default => Loan::with(['student', 'teacher'])->latest('borrowed_at')->get()->map(fn ($loan) => [$loan->transaction_code, $loan->student?->full_name ?: $loan->teacher?->full_name, $loan->borrowed_at->format('Y-m-d H:i:s'), $loan->status]),
        };
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, match ($type) { 'books' => ['Kode', 'Judul', 'Kategori'], 'students' => ['NIS', 'Nama', 'Kelas', 'Status'], 'teachers' => ['NIP', 'Nama', 'Jabatan', 'Status'], default => ['Kode transaksi', 'Peminjam', 'Tanggal', 'Status'] });
        foreach ($rows as $row) fputcsv($stream, $row);
        rewind($stream);
        return response()->streamDownload(fn () => fpassthru($stream), $type.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function json(string $type): Response
    {
        abort_unless(in_array($type, ['loans', 'books', 'students', 'teachers'], true), 404);
        $data = match ($type) {
            'books' => Book::with('category')->where('is_active', true)->get(),
            'students' => Student::with('class')->where('is_active', true)->get(),
            'teachers' => Teacher::where('is_active', true)->get(),
            default => Loan::with(['student', 'teacher', 'items'])->latest('borrowed_at')->get(),
        };
        return response()->json($data);
    }
}
