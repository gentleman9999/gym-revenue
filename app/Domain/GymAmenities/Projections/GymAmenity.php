<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Projections;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymAmenity extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id','location_id', 'name', 'capacity', 'working_hour', 'started_at', 'closed_at'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
