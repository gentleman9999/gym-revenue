<?php

namespace App\Domain\EndUsers\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Stringable;

abstract class EndUserDetails extends GymRevDetailProjection
{
    use hasFactory;
    use SoftDeletes;

    public static function getModelName(): Stringable
    {
        return str(class_basename((new static())::class));
    }

    public function endUser(): BelongsTo
    {
        return $this->parentProjection;
    }
}
