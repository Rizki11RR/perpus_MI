<?php
namespace Database\Factories;
use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
class BookCategoryFactory extends Factory { protected $model=BookCategory::class; public function definition(): array { return ['name'=>fake()->unique()->words(2,true),'description'=>fake()->sentence()]; } }
