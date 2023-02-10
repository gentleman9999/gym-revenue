<?php

declare(strict_types=1);

namespace App\Domain\LeadTypes;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadType extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<string>  */
    protected $fillable = ['name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
