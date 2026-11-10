<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model { use HasFactory; protected $guarded=[]; public function user(): BelongsTo { return $this->belongsTo(User::class); } public function subject(): MorphTo { return $this->morphTo(); } }
