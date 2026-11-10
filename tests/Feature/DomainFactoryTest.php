<?php

namespace Tests\Feature;

use App\Models\BookCopy;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_factory_creates_a_student_loan_with_items(): void
    {
        $loan = Loan::factory()->create();
        $item = $loan->items()->create([
            'book_copy_id' => BookCopy::factory()->create()->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
            'daily_fine_snapshot' => 0,
            'fine_amount' => 0,
            'status' => 'borrowed',
        ]);

        $this->assertNotNull($loan->student_id);
        $this->assertTrue($item->loan->is($loan));
        $this->assertSame('borrowed', $item->status);
    }
}
