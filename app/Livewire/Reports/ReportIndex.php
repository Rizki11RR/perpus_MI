<?php

namespace App\Livewire\Reports;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class ReportIndex extends Component
{
    use WithPagination;

    public string $report = 'loans';
    public string $status = '';
    public string $month = '';

    public function updatedReport(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedMonth(): void { $this->resetPage(); }

    public function render()
    {
        $loans = Loan::with(['student', 'teacher', 'items.bookCopy.book'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->month, fn ($q) => $q->whereBetween('borrowed_at', [$this->month.'-01', $this->month.'-'.date('t', strtotime($this->month.'-01')).' 23:59:59']))
            ->latest('borrowed_at')->paginate(20);
        return view('livewire.reports.report-index', [
            'loans' => $loans,
            'books' => Book::with('category')->where('is_active', true)->latest()->paginate(20, ['*'], 'booksPage'),
            'students' => Student::where('is_active', true)->latest()->paginate(20, ['*'], 'studentsPage'),
            'teachers' => Teacher::where('is_active', true)->latest()->paginate(20, ['*'], 'teachersPage'),
        ])->layout('components.layouts.app', ['heading' => 'Laporan']);
    }
}
