<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'transaction_code' => fake()->unique()->bothify('PJM-########'),
            'student_id' => Student::factory(),
            'processed_by' => User::factory(),
            'borrowed_at' => now(),
            'status' => 'active',
        ];
    }
}
