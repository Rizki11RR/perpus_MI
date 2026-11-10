<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\View\View;

class PrintController extends Controller
{
    public function catalog(): View { return view('print.catalog', ['books' => Book::with('category')->where('is_active', true)->orderBy('title')->get()]); }
    public function loans(): View { return view('print.loans', ['loans' => Loan::with(['student','teacher','items.bookCopy.book'])->latest('borrowed_at')->get()]); }
}
