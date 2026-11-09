<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bird extends Model
{
    protected $fillable = [
        'name',
        'species',
        'sex',
        'cage_id',
        'status',
        'band_number',
        'color_mutation',
        'birth_date',
        'parent_male_id',
        'parent_female_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cage(): BelongsTo
    {
        return $this->belongsTo(Cage::class);
    }

    public function maleBreedingPairs(): HasMany
    {
        return $this->hasMany(BreedingPair::class, 'male_bird_id');
    }

    public function femaleBreedingPairs(): HasMany
    {
        return $this->hasMany(BreedingPair::class, 'female_bird_id');
    }

    public function flockRecords(): HasMany
    {
        return $this->hasMany(FlockRecord::class);
    }
}
