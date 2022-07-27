<?php

namespace App\Domain\LeadSources;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSource extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = ['name', 'source', 'ui', 'active', 'misc'];

    protected $casts = [
        'misc' => 'array',
    ];

    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
