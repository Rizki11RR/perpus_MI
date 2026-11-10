<?php
namespace Database\Factories;
use App\Models\BookCopy;
use Illuminate\Database\Eloquent\Factories\Factory;
class BookCopyFactory extends Factory { protected $model=BookCopy::class; public function definition(): array { return ['book_id'=>BookFactory::new(),'barcode'=>fake()->unique()->bothify('BC-########'),'condition'=>'good','status'=>'available']; } }
