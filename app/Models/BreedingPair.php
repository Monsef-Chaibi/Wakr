<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreedingPair extends Model
{
    protected $fillable = ['male_bird_id', 'female_bird_id', 'eggs', 'egg_dates', 'status', 'next_milestone'];

    protected function casts(): array
    {
        return ['eggs' => 'integer', 'egg_dates' => 'array', 'next_milestone' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maleBird(): BelongsTo
    {
        return $this->belongsTo(Bird::class, 'male_bird_id');
    }

    public function femaleBird(): BelongsTo
    {
        return $this->belongsTo(Bird::class, 'female_bird_id');
    }
}
