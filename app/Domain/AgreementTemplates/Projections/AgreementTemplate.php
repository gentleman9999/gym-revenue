<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Enums\AgreementAvailabilityEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementTemplate extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<string> */
    protected $fillable = [
        'client_id',
        'gr_location_id',
        'created_by',
        'agreement_name',
        'agreement_json',
        'is_not_billable',
        'availability',
        'billing_schedule_id',
        'contract_id',
    ];

    protected $casts = [
        'agreement_availability' => AgreementAvailabilityEnum::class,
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'gymrevenue_id', 'gr_location_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
