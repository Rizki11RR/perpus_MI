<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Staff extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['is_active'=>'boolean']; } public function user(): BelongsTo { return $this->belongsTo(User::class); } }
