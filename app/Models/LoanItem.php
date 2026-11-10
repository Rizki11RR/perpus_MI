<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class LoanItem extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['borrowed_at'=>'datetime','due_date'=>'date','returned_at'=>'datetime','daily_fine_snapshot'=>'decimal:2','fine_amount'=>'decimal:2']; } public function loan(): BelongsTo { return $this->belongsTo(Loan::class); } public function bookCopy(): BelongsTo { return $this->belongsTo(BookCopy::class); } public function returnedBy(): BelongsTo { return $this->belongsTo(User::class,'returned_by'); } }
