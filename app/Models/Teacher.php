<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Teacher extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['is_active'=>'boolean']; } public function loans(): HasMany { return $this->hasMany(Loan::class); } }
