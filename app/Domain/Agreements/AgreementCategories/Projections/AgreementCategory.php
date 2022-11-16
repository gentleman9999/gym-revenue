<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories\Projections;

use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementCategory extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    public const NAME_MEMBERSHIP = 'Membership';
    public const NAME_PERSONAL_TRAINING = 'Personal Training';

    protected $fillable = ['client_id', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
