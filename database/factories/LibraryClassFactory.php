<?php
namespace Database\Factories;
use App\Models\LibraryClass;
use Illuminate\Database\Eloquent\Factories\Factory;
class LibraryClassFactory extends Factory { protected $model=LibraryClass::class; public function definition(): array { return ['name'=>fake()->unique()->randomElement(['VII A','VIII A','IX A']),'grade_level'=>'7','academic_year'=>'2026/2027']; } }
