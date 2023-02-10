<?php

declare(strict_types=1);

namespace App\Domain\LeadStatuses;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<string> */
    protected $fillable = ['status', 'order', 'active'];

    /** @var array<string> */
    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
