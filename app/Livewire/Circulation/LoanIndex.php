<?php

namespace App\Livewire\Circulation;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\LoanService;
use Livewire\Component;
use Livewire\WithPagination;

class LoanIndex extends Component
{
    use WithPagination;

    public ?int $studentId = null;
    public ?int $teacherId = null;
    public string $borrowerSearch = '';
    public string $teacherSearch = '';
    public array $copyIds = [];
    public string $statusFilter = '';
    public string $copySearch = '';
    public string $scanBarcode = '';
    public string $returnBarcode = '';
    public string $cancelReason = '';
    public ?int $cancelLoanId = null;

    public function prepareCancel(int $loanId): void { abort_unless(auth()->user()->isAdmin(), 403); $this->cancelLoanId = $loanId; $this->cancelReason = ''; }

    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedCopySearch(): void { }

    public function scanCopy(LoanService $service): void
    {
        $barcode = trim($this->scanBarcode);
        $this->reset('scanBarcode');
        if ($barcode === '') return;
        $copy = BookCopy::with('book')->where('barcode', $barcode)->first();
        if (! $copy) { session()->flash('error', 'Barcode tidak ditemukan.'); return; }
        if ($copy->status !== 'available' || ! $copy->book->is_active) { session()->flash('error', 'Eksemplar tidak tersedia.'); return; }
        if (in_array($copy->id, array_map('intval', $this->copyIds), true)) { session()->flash('error', 'Barcode sudah dipilih.'); return; }
        $this->copyIds[] = $copy->id;
        session()->flash('success', 'Barcode '.$copy->barcode.' ditambahkan.');
    }

    public function scanReturn(LoanService $service): void
    {
        $barcode = trim($this->returnBarcode);
        $this->reset('returnBarcode');
        if ($barcode === '') return;
        $item = LoanItem::query()->whereHas('bookCopy', fn ($q) => $q->where('barcode', $barcode))->where('status', 'borrowed')->first();
        if (! $item) { session()->flash('error', 'Barcode tidak ditemukan pada pinjaman aktif.'); return; }
        $service->returnItem($item->id, auth()->id());
        session()->flash('success', 'Pengembalian barcode '.$barcode.' berhasil.');
    }

    public function removeCopy(int $copyId): void { $this->copyIds = array_values(array_filter($this->copyIds, fn ($id) => (int) $id !== $copyId)); }
    public function updatedBorrowerSearch(): void { $this->studentId = null; }
    public function updatedTeacherSearch(): void { $this->teacherId = null; }

    public function save(LoanService $service): void
    {
        $this->validate(['studentId'=>'nullable|integer|exists:students,id','teacherId'=>'nullable|integer|exists:teachers,id','copyIds'=>'required|array|min:1','copyIds.*'=>'integer|exists:book_copies,id']);
        $service->borrow(auth()->id(), $this->studentId, $this->teacherId, $this->copyIds);
        $this->reset(['studentId','teacherId','copyIds']);
        session()->flash('success', 'Peminjaman berhasil dicatat.');
    }

    public function cancel(int $loanId, LoanService $service): void { abort_unless(auth()->user()->isAdmin(), 403); $this->validate(['cancelReason'=>'required|string|max:1000']); $service->cancel($loanId, auth()->id(), $this->cancelReason); $this->reset(['cancelReason','cancelLoanId']); session()->flash('success','Peminjaman dibatalkan.'); }

    public function returnItem(int $itemId, LoanService $service): void
    {
        $service->returnItem($itemId, auth()->id());
        session()->flash('success', 'Pengembalian berhasil dicatat.');
    }

    public function render()
    {
        return view('livewire.circulation.loan-index', [
            'students' => Student::where('is_active', true)->when($this->borrowerSearch, fn ($q) => $q->where('student_number', 'like', '%'.$this->borrowerSearch.'%')->orWhere('full_name', 'like', '%'.$this->borrowerSearch.'%'))->orderBy('full_name')->limit(30)->get(),
            'teachers' => Teacher::where('is_active', true)->when($this->teacherSearch, fn ($q) => $q->where('teacher_number', 'like', '%'.$this->teacherSearch.'%')->orWhere('full_name', 'like', '%'.$this->teacherSearch.'%'))->orderBy('full_name')->limit(30)->get(),
            'copies' => BookCopy::with('book')->where('status', 'available')->whereHas('book', fn ($q) => $q->where('is_active', true))->when($this->copySearch, fn ($q) => $q->where('barcode', 'like', '%'.$this->copySearch.'%')->orWhereHas('book', fn ($book) => $book->where('title', 'like', '%'.$this->copySearch.'%')))->orderBy('barcode')->get(),
            'loans' => Loan::with(['student','teacher','items.bookCopy.book'])->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))->latest('borrowed_at')->paginate(10),
        ])->layout('components.layouts.app', ['heading' => 'Sirkulasi']);
    }
}
