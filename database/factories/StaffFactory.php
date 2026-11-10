<?php
namespace Database\Factories;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
class StaffFactory extends Factory { protected $model=Staff::class; public function definition(): array { return ['staff_number'=>fake()->unique()->numerify('STF-#####'),'full_name'=>fake()->name(),'position'=>'Petugas','is_active'=>true]; } }
