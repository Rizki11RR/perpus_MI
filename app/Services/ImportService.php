<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\ImportBatch;
use App\Models\ImportBatchRow;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportService
{
    public function preview(UploadedFile $file, string $type, int $userId, string $duplicateMode = 'skip'): ImportBatch
    {
        $allowed = ['books', 'students', 'teachers'];
        if (! in_array($type, $allowed, true)) throw ValidationException::withMessages(['type'=>'Jenis import tidak valid.']);
        $batch = DB::transaction(function () use ($file, $type, $userId, $duplicateMode): ImportBatch {
            $batch = ImportBatch::create(['type'=>$type,'file_name'=>$file->getClientOriginalName(),'uploaded_by'=>$userId,'status'=>'preview','duplicate_mode'=>$duplicateMode]);
            $handle = $this->openRows($file);
            if ($handle === false) throw ValidationException::withMessages(['file'=>'File import tidak dapat dibaca.']);
            $headers = array_map(fn ($value) => $this->normalizeHeader($value), $handle->current());
            $handle->next();
            $requiredHeaders = match ($type) { 'books'=>['book_code','title','category'], 'students'=>['student_number','full_name'], default=>['full_name'] };
            if (count($headers) !== count(array_unique($headers)) || array_diff($requiredHeaders, $headers)) {
                throw ValidationException::withMessages(['file'=>'Header CSV tidak lengkap atau duplikat.']);
            }
            $valid = $invalid = 0; $rowNumber = 1; $seen = [];
            while ($handle->valid()) {
                $values = $handle->current();
                $handle->next();
                if (count($values) === 1 && trim((string) $values[0]) === '') continue;
                if (count($values) !== count($headers)) throw ValidationException::withMessages(['file'=>'Jumlah kolom tidak sesuai pada baris '.$rowNumber.'.']);
                $rowNumber++;
                $payload = array_combine($headers, array_pad(array_slice($values, 0, count($headers)), count($headers), null)) ?: [];
                $payload = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $payload);
                $key = $type === 'books' ? ($payload['book_code'] ?? '') : ($type === 'students' ? ($payload['student_number'] ?? '') : ($payload['teacher_number'] ?? $payload['full_name'] ?? ''));
                $errors = $this->validateRow($payload, $type);
                if ($key !== '' && isset($seen[$key])) $errors['duplicate'] = 'Duplikat dalam file.';
                if ($key !== '') $seen[$key] = true;
                ImportBatchRow::create(['import_batch_id'=>$batch->id,'row_number'=>$rowNumber,'payload'=>$payload,'errors'=>$errors ?: null,'is_valid'=>!$errors]); $errors ? $invalid++ : $valid++;
            }
            $batch->update(['valid_rows'=>$valid,'invalid_rows'=>$invalid]); return $batch;
        });
        return $batch->load('rows');
    }

    public function commit(ImportBatch $batch, int $userId, bool $validOnly = true): void
    {
        if ((int) $batch->uploaded_by !== $userId) throw ValidationException::withMessages(['batch'=>'Batch bukan milik pengguna ini.']);
        DB::transaction(function () use ($batch, $userId, $validOnly): void {
            $rows = $batch->rows()->where('is_valid', true)->get();
            if (! $validOnly && $batch->invalid_rows > 0) throw ValidationException::withMessages(['batch'=>'Batch memiliki baris tidak valid.']);
            foreach ($rows as $row) $this->store($batch->type, $row->payload, $batch->duplicate_mode ?? 'skip');
            $batch->update(['status'=>'committed']);
            ActivityLog::create(['user_id'=>$userId,'action'=>'imported','module'=>'imports','description'=>'Import '.$batch->type.' selesai.']);
        });
    }

    private function normalizeHeader(mixed $value): string
    {
        return strtolower(trim((string) $value, " \t\r\n\xEF\xBB\xBF"));
    }

    private function openRows(UploadedFile $file): ?\Generator
    {
        if (strtolower($file->getClientOriginalExtension()) === 'xlsx') {
            $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
            foreach ($sheet->toArray(null, true, true, false) as $row) yield $row;
            return;
        }
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) return null;
        try {
            while (($row = fgetcsv($handle)) !== false) yield $row;
        } finally {
            fclose($handle);
        }
    }

    private function validateRow(array $row, string $type): array
    {
        $errors = [];
        $required = match ($type) { 'books'=>['book_code','title','category'], 'students'=>['student_number','full_name'], default=>['full_name'] };
        foreach ($required as $key) if (trim((string) ($row[$key] ?? '')) === '') $errors[$key] = 'Kolom wajib kosong.';
        if ($type === 'books' && ($row['category'] ?? '') && ! BookCategory::where('name', $row['category'])->exists()) $errors['category'] = 'Kategori tidak ditemukan.';
        if ($type === 'books' && Book::where('book_code', $row['book_code'] ?? null)->exists()) $errors['book_code'] = 'Kode buku sudah ada.';
        if ($type === 'students' && Student::where('student_number', $row['student_number'] ?? null)->exists()) $errors['student_number'] = 'NIS sudah ada.';
        if ($type === 'students' && ($row['class'] ?? '') && ! \App\Models\LibraryClass::where('name', $row['class'])->exists()) $errors['class'] = 'Kelas tidak ditemukan.';
        if ($type === 'teachers' && ($row['teacher_number'] ?? '') && Teacher::where('teacher_number', $row['teacher_number'])->exists()) $errors['teacher_number'] = 'NIP sudah ada.';
        return $errors;
    }

    private function store(string $type, array $row, string $duplicateMode = 'skip'): void
    {
        if ($type === 'books') {
            $data = ['category_id'=>BookCategory::where('name',$row['category'])->value('id'),'book_code'=>$row['book_code'],'title'=>$row['title'],'isbn'=>$row['isbn'] ?? null,'author'=>$row['author'] ?? null,'publisher'=>$row['publisher'] ?? null];
            $model = Book::where('book_code', $row['book_code'])->first();
            if ($model && $duplicateMode === 'update') $model->update($data); elseif (! $model) Book::create($data);
        } elseif ($type === 'students') {
            $data = ['student_number'=>$row['student_number'],'full_name'=>$row['full_name'],'class_id'=>($row['class'] ?? null) ? \App\Models\LibraryClass::where('name',$row['class'])->value('id') : null,'status'=>'active'];
            $model = Student::where('student_number', $row['student_number'])->first();
            if ($model && $duplicateMode === 'update') $model->update($data); elseif (! $model) Student::create($data);
        } else {
            $data = ['teacher_number'=>$row['teacher_number'] ?? null,'full_name'=>$row['full_name'],'status'=>'active'];
            $model = ($row['teacher_number'] ?? null) ? Teacher::where('teacher_number',$row['teacher_number'])->first() : null;
            if ($model && $duplicateMode === 'update') $model->update($data); elseif (! $model) Teacher::create($data);
        }
    }
}
