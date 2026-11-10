<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'bookTitles' => Book::count(),
            'totalCopies' => BookCopy::count(),
            'availableCopies' => BookCopy::where('status', 'available')->count(),
            'borrowedCopies' => BookCopy::where('status', 'borrowed')->count(),
            'activeStudents' => Student::where('status', 'active')->count(),
            'activeTeachers' => Teacher::where('status', 'active')->count(),
            'activeStaff' => Staff::where('is_active', true)->count(),
            'recentLoans' => Loan::with(['student', 'teacher', 'processor'])->latest('borrowed_at')->limit(5)->get(),
        ])->layout('components.layouts.app', ['heading' => 'Dashboard']);
    }
}
