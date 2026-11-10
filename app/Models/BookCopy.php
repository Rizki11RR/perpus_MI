<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class BookCopy extends Model { use HasFactory; protected $guarded=[]; public function book(): BelongsTo { return $this->belongsTo(Book::class); } public function loanItems(): HasMany { return $this->hasMany(LoanItem::class); } }
