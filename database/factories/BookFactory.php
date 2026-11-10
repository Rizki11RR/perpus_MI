<?php
namespace Database\Factories;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
class BookFactory extends Factory { protected $model=Book::class; public function definition(): array { return ['category_id'=>BookCategoryFactory::new(),'book_code'=>fake()->unique()->bothify('BK-####'),'isbn'=>fake()->optional()->numerify('978#############'),'title'=>fake()->sentence(4),'author'=>fake()->name(),'publisher'=>fake()->company(),'publication_year'=>fake()->numberBetween(2000,2026)]; } }
