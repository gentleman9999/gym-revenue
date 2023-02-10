<?php

declare(strict_types=1);

namespace App\Domain\Clients\Models;

use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientSocialMedia extends GymRevProjection
{
    use HasFactory;

    /** @var array<string>  */
    protected $fillable = ['name', 'value'];

    /** @var array<string>  */
    protected $hidden = ['client_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
