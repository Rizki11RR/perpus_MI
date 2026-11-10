<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\BookCopy;
use App\Models\LibrarySetting;
use App\Models\Loan;
use App\Models\LoanItem;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function borrow(int $userId, ?int $studentId, ?int $teacherId, array $copyIds, ?CarbonInterface $borrowedAt = null): Loan
    {
        if (($studentId === null) === ($teacherId === null)) throw ValidationException::withMessages(['borrower' => 'Pilih tepat satu peminjam.']);
        $settings = LibrarySetting::query()->latest('id')->first();
        $max = $settings?->max_books_per_borrower ?? 3;
        $borrowedQuery = Loan::query()->where('status', 'active')->whereHas('items', fn ($q) => $q->where('status', 'borrowed'));
        $borrowedQuery->when($studentId, fn ($q) => $q->where('student_id', $studentId))->when($teacherId, fn ($q) => $q->where('teacher_id', $teacherId));
        $currentCount = $borrowedQuery->withCount(['items as borrowed_count' => fn ($q) => $q->where('status', 'borrowed')])->get()->sum('borrowed_count');
        $copyIds = array_values(array_unique(array_map('intval', $copyIds)));
        if (count($copyIds) < 1 || count($copyIds) > $max || $currentCount + count($copyIds) > $max) throw ValidationException::withMessages(['copies' => "Peminjam maksimal memiliki {$max} buku aktif."]);
        return DB::transaction(function () use ($userId, $studentId, $teacherId, $copyIds, $borrowedAt, $settings): Loan {
            if ($studentId !== null) \App\Models\Student::query()->whereKey($studentId)->lockForUpdate()->firstOrFail();
            if ($teacherId !== null) \App\Models\Teacher::query()->whereKey($teacherId)->lockForUpdate()->firstOrFail();
            $copies = BookCopy::query()->whereKey($copyIds)->lockForUpdate()->get();
            if ($copies->count() !== count($copyIds) || $copies->contains(fn (BookCopy $copy) => $copy->status !== 'available')) throw ValidationException::withMessages(['copies' => 'Salah satu eksemplar tidak tersedia.']);
            $time = $borrowedAt ?? now();
            $loan = Loan::create(['transaction_code'=>$this->transactionCode(),'student_id'=>$studentId,'teacher_id'=>$teacherId,'processed_by'=>$userId,'borrowed_at'=>$time,'status'=>'active']);
            $due = $time->copy()->addDays($settings?->loan_duration_days ?? 7)->toDateString();
            foreach ($copies as $copy) { $loan->items()->create(['book_copy_id'=>$copy->id,'borrowed_at'=>$time,'due_date'=>$due,'daily_fine_snapshot'=>$settings?->daily_fine ?? 0,'status'=>'borrowed']); $copy->update(['status'=>'borrowed']); }
            ActivityLog::create(['user_id'=>$userId,'action'=>'created','module'=>'loans','description'=>'Peminjaman '.$loan->transaction_code.' dicatat.']);
            return $loan->load('items');
        });
    }

    public function returnItem(int $itemId, int $userId, ?CarbonInterface $returnedAt = null): void
    {
        DB::transaction(function () use ($itemId, $userId, $returnedAt): void {
            $item = LoanItem::query()->with('loan')->lockForUpdate()->findOrFail($itemId);
            if ($item->status !== 'borrowed') return;
            $time = $returnedAt ?? now();
            $settings = LibrarySetting::query()->latest('id')->first();
            $grace = $settings?->grace_period_days ?? 0;
            $daysLate = max(0, $item->due_date->diffInDays($time->copy()->startOfDay(), false) - $grace);
            $item->update(['returned_at'=>$time,'returned_by'=>$userId,'fine_amount'=>$daysLate * (float) $item->daily_fine_snapshot,'status'=>'returned']);
            BookCopy::query()->whereKey($item->book_copy_id)->lockForUpdate()->update(['status'=>'available']);
            if (! $item->loan->items()->where('status','borrowed')->where('id','!=',$item->id)->exists()) $item->loan->update(['status'=>'returned']);
            ActivityLog::create(['user_id'=>$userId,'action'=>'returned','module'=>'loans','description'=>'Item transaksi '.$item->loan->transaction_code.' dikembalikan.']);
        });
    }

    public function cancel(int $loanId, int $userId, string $reason): void
    {
        DB::transaction(function () use ($loanId, $userId, $reason): void {
            $loan = Loan::query()->with('items')->lockForUpdate()->findOrFail($loanId);
            if ($loan->items->contains(fn (LoanItem $item) => $item->status === 'returned')) throw ValidationException::withMessages(['loan'=>'Transaksi dengan pengembalian tidak dapat dibatalkan.']);
            if ($loan->status !== 'active') throw ValidationException::withMessages(['loan'=>'Transaksi sudah tidak aktif.']);
            $loan->update(['status'=>'cancelled','cancelled_by'=>$userId,'cancelled_at'=>now(),'cancellation_reason'=>$reason]);
            foreach ($loan->items as $item) BookCopy::query()->whereKey($item->book_copy_id)->lockForUpdate()->update(['status'=>'available']);
            ActivityLog::create(['user_id'=>$userId,'action'=>'cancelled','module'=>'loans','description'=>'Peminjaman '.$loan->transaction_code.' dibatalkan.']);
        });
    }

    private function transactionCode(): string { do { $code='PJM-'.now()->format('YmdHis').'-'.random_int(100,999); } while (Loan::where('transaction_code',$code)->exists()); return $code; }
}
