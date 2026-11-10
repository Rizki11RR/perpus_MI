<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Book extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['is_active'=>'boolean']; } public function category(): BelongsTo { return $this->belongsTo(BookCategory::class,'category_id'); } public function copies(): HasMany { return $this->hasMany(BookCopy::class); } }
