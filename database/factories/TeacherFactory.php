<?php
namespace Database\Factories;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
class TeacherFactory extends Factory { protected $model=Teacher::class; public function definition(): array { return ['teacher_number'=>fake()->unique()->numerify('NIP-#####'),'full_name'=>fake()->name(),'position'=>'Guru','status'=>'active']; } }
