<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_cancel_active_loan_and_release_copies(): void
    {
        $admin = User::factory()->create(['role'=>'admin']);
        $loan = app(LoanService::class)->borrow($admin->id, Student::factory()->create()->id, null, [$copy = BookCopy::factory()->create()->id]);
        app(LoanService::class)->cancel($loan->id, $admin->id, 'Input salah');
        $this->assertSame('cancelled', $loan->fresh()->status);
        $this->assertSame('available', BookCopy::find($copy)->status);
    }

    public function test_cancel_is_rejected_after_return(): void
    {
        $user = User::factory()->create();
        $loan = app(LoanService::class)->borrow($user->id, Student::factory()->create()->id, null, [$copy = BookCopy::factory()->create()->id]);
        app(LoanService::class)->returnItem($loan->items->first()->id, $user->id);
        $this->expectException(ValidationException::class);
        app(LoanService::class)->cancel($loan->id, $user->id, 'Tidak boleh');
    }
}
