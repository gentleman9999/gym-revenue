<?php

namespace App\Domain\Users\Models;

use App\Domain\Teams\Models\Team;
use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $casts = [
        'misc' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->parentModel;
    }

    public static function getRelatedModel()
    {
        return new User();
    }

    public function team(): ?BelongsTo
    {
        if ($this->attributes['field'] == 'default_team_id') {
            return $this->belongsTo(Team::class, 'value');
        } else {
            return null;
        }
    }

    public static function fk(): string
    {
        return "user_id";
    }
}
