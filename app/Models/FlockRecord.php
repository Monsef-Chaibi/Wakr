<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlockRecord extends Model
{
    protected $fillable = ['bird_id', 'activity', 'type', 'occurred_at', 'notes'];

    protected function casts(): array
    {
        return ['occurred_at' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bird(): BelongsTo
    {
        return $this->belongsTo(Bird::class);
    }
}
