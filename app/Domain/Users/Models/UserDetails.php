<?php

namespace App\Domain\Users\Models;

use App\Domain\Teams\Models\Team;
use App\Enums\UserTypesEnum;
use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'client_id', 'field', 'value', 'misc', 'active'];

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
        if ($this->attributes['field'] == 'default_team_id' && $this->user->user_type == UserTypesEnum::EMPLOYEE) {
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
