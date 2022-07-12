<?php

namespace App\Domain\LeadStatuses;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['status', 'order', 'active'];

    protected $hidden = ['client_id'];

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }
}
