<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Loan extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['borrowed_at'=>'datetime','cancelled_at'=>'datetime']; } public function student(): BelongsTo { return $this->belongsTo(Student::class); } public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); } public function processor(): BelongsTo { return $this->belongsTo(User::class,'processed_by'); } public function items(): HasMany { return $this->hasMany(LoanItem::class); } public function cancelledBy(): BelongsTo { return $this->belongsTo(User::class, 'cancelled_by'); } }
