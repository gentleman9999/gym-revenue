<?php

namespace App\Domain\Users\Models;

use App\Models\GymRevDetailProjection;
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

    public static function fk(): string
    {
        return "user_id";
    }
}
