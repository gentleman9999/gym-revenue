<?php

namespace App\Domain\EndUsers\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Stringable;

class EndUserDetails extends GymRevDetailProjection
{
    use hasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'end_user_id', 'field', 'value',
        'entity_id', 'misc', 'active',
    ];

    protected $hidden = ['client_id'];

    public static function getModelName(): Stringable
    {
        return str(class_basename((new static())::class));
    }

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function endUser(): BelongsTo
    {
        return $this->parentProjection;
    }

    public static function getRelatedModel()
    {
        return new EndUser();
    }

    public static function fk(): string
    {
        return "end_user_id";
    }
}
