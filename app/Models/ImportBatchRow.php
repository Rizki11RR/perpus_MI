<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ImportBatchRow extends Model { use HasFactory; protected $guarded=[]; protected function casts(): array { return ['payload'=>'array','errors'=>'array','is_valid'=>'boolean']; } public function batch(): BelongsTo { return $this->belongsTo(ImportBatch::class,'import_batch_id'); } }
