<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spot extends Model
{
    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'spot_id', 'id');
    }

    public function sportsAndTags(): BelongsToMany
    {
        return $this->belongsToMany(SportsAndTag::class, 'spot_sports_and_tags', 'spot_id', 'sports_and_tag_id');
    }
}
