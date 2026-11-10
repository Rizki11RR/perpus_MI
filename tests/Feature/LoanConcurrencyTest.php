<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\Student;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoanConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_borrow_of_same_copy_is_rejected_after_first_borrow(): void
    {
        $service = app(LoanService::class);
        $user = User::factory()->create();
        $copy = BookCopy::factory()->create();
        $service->borrow($user->id, Student::factory()->create()->id, null, [$copy->id]);
        $this->expectException(ValidationException::class);
        $service->borrow($user->id, Student::factory()->create()->id, null, [$copy->id]);
    }
}
