<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class LibrarySetting extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['daily_fine'=>'decimal:2']; } }
