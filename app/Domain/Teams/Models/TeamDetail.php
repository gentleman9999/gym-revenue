<?php

namespace App\Domain\Teams\Models;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamDetail extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $casts = [
        'misc' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->parentProjection;
    }

    public static function getRelatedModel()
    {
        return new Team();
    }

    public static function fk(): string
    {
        return "team_id";
    }
}
