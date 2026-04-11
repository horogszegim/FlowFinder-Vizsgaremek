<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SportsAndTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function spots(): BelongsToMany
    {
        return $this->belongsToMany(Spot::class, 'spot_sports_and_tags', 'sports_and_tag_id', 'spot_id');
    }
}
