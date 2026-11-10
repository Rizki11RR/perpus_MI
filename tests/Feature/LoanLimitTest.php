<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\LibrarySetting;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_respects_existing_active_loan_limit(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        LibrarySetting::create(['max_books_per_borrower'=>2,'loan_duration_days'=>7,'daily_fine'=>0,'grace_period_days'=>0,'timezone'=>'Asia/Jakarta']);
        app(LoanService::class)->borrow($user->id, $student->id, null, [BookCopy::factory()->create()->id]);
        $this->expectException(ValidationException::class);
        app(LoanService::class)->borrow($user->id, $student->id, null, [BookCopy::factory()->create()->id, BookCopy::factory()->create()->id]);
    }
}
