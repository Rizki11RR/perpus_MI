<?php

namespace Database\Factories;

use App\Models\BookCopy;
use App\Models\LoanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanItemFactory extends Factory
{
    protected $model = LoanItem::class;

    public function definition(): array
    {
        return [
            'loan_id' => \App\Models\Loan::factory(),
            'book_copy_id' => BookCopy::factory(),
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
            'daily_fine_snapshot' => 0,
            'fine_amount' => 0,
            'status' => 'borrowed',
        ];
    }
}
