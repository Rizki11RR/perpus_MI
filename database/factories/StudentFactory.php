<?php
namespace Database\Factories;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
class StudentFactory extends Factory { protected $model=Student::class; public function definition(): array { return ['student_number'=>fake()->unique()->numerify('NIS-#####'),'full_name'=>fake()->name(),'status'=>'active']; } }
