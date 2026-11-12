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
        $search = request()->string('search')->trim()->toString();
        $rows = match ($type) {
            'books' => Book::with('category')->where('is_active', true)->when($search, fn ($q) => $q->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('book_code', 'like', "%{$search}%")->orWhere('isbn', 'like', "%{$search}%")))->get()->map(fn ($book) => [$book->book_code, $book->title, $book->category?->name, $book->isbn, $book->author, $book->publisher]),
            'students' => Student::with('classRoom')->where('is_active', true)->when($search, fn ($q) => $q->where(fn ($q) => $q->where('student_number', 'like', "%{$search}%")->orWhere('full_name', 'like', "%{$search}%")))->get()->map(fn ($x) => [$x->student_number, $x->full_name, $x->classRoom?->name, $x->status]),
            'teachers' => Teacher::where('is_active', true)->when($search, fn ($q) => $q->where(fn ($q) => $q->where('teacher_number', 'like', "%{$search}%")->orWhere('full_name', 'like', "%{$search}%")))->get()->map(fn ($x) => [$x->teacher_number, $x->full_name, $x->position, $x->status]),
            default => Loan::with(['student', 'teacher'])->latest('borrowed_at')->get()->map(fn ($loan) => [$loan->transaction_code, $loan->student?->full_name ?: $loan->teacher?->full_name, $loan->borrowed_at->format('Y-m-d H:i:s'), $loan->status]),
        };
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, match ($type) { 'books' => ['Kode Buku', 'Judul', 'Kategori', 'ISBN', 'Penulis', 'Penerbit'], 'students' => ['NIS', 'Nama', 'Kelas', 'Status'], 'teachers' => ['NIP', 'Nama', 'Jabatan', 'Status'], default => ['Kode transaksi', 'Peminjam', 'Tanggal', 'Status'] });
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
