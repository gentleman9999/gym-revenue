<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Projections;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use CapeAndBay\Versionable\Versionable;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;
    /** @see https://github.com/GymRevenue/versionable */
    use Versionable;
    use Uuid;


    /** @var array<string>  */
    protected $fillable = ['client_id', 'name'];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
