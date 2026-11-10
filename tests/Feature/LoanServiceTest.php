<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\LibrarySetting;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_updates_copy_and_snapshots_settings(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $copy = BookCopy::factory()->create();
        LibrarySetting::create(['max_books_per_borrower'=>3,'loan_duration_days'=>7,'daily_fine'=>'1000','grace_period_days'=>0,'timezone'=>'Asia/Jakarta','updated_by'=>$user->id]);
        $loan = app(LoanService::class)->borrow($user->id, $student->id, null, [$copy->id]);
        $this->assertSame('borrowed', $copy->fresh()->status);
        $this->assertSame('1000.00', (string) $loan->items->first()->daily_fine_snapshot);
    }

    public function test_borrow_requires_exactly_one_borrower_and_available_copy(): void
    {
        $user = User::factory()->create();
        $copy = BookCopy::factory()->create(['status'=>'borrowed']);
        $this->expectException(ValidationException::class);
        app(LoanService::class)->borrow($user->id, null, null, [$copy->id]);
    }

    public function test_partial_return_releases_copy_and_full_return_closes_loan(): void
    {
        $user = User::factory()->create();
        $student = Student::factory()->create();
        $first = BookCopy::factory()->create();
        $second = BookCopy::factory()->create();
        $loan = app(LoanService::class)->borrow($user->id, $student->id, null, [$first->id, $second->id]);
        app(LoanService::class)->returnItem($loan->items[0]->id, $user->id);
        $this->assertSame('available', $first->fresh()->status);
        $this->assertSame('active', $loan->fresh()->status);
        app(LoanService::class)->returnItem($loan->items[1]->id, $user->id);
        $this->assertSame('returned', $loan->fresh()->status);
    }
}
