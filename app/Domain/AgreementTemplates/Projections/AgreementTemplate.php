<?php

declare(strict_types=1);

namespace App\Domain\AgreementTemplates\Projections;

use App\Domain\BillingSchedules\Projections\BillingSchedule;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Enums\AgreementAvailabilityEnum;
use App\Models\GymRevProjection;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgreementTemplate extends GymRevProjection
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['client_id', 'gr_location_id', 'created_by', 'agreement_name', 'agreement_json', 'is_not_billable', 'availability'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

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

    public function billingSchedule(): BelongsToMany
    {
        return $this->belongsToMany(BillingSchedule::class, 'agreement_template_billing_schedule', 'billing_schedule_id', 'agreement_template_id');
    }
}
