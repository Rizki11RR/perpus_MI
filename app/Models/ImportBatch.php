<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ImportBatch extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['valid_rows'=>'integer','invalid_rows'=>'integer']; } public function uploader(): BelongsTo { return $this->belongsTo(User::class,'uploaded_by'); } public function rows(): HasMany { return $this->hasMany(ImportBatchRow::class); } }
